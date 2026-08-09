<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {

            $('#job_proposal_form').on('submit', function () {
                $('.send_job_proposal').attr('disabled', 'true');
            });

            // proposal validate (show only first error)
            $(document).on('click', '.send_job_proposal', function (e) {
                e.preventDefault();

                let $form = $('#job_proposal_form');
                let amount = $form.find('#amount').val() ? $form.find('#amount').val().trim() : '';
                let duration = $form.find('#duration').val() ? $form.find('#duration').val().trim() : '';
                let revision = $form.find('#revision').val() ? $form.find('#revision').val().trim() : '';
                let cover_letter = $form.find('#cover_letter').val() ? $form.find('#cover_letter').val().trim() : '';
                let attachment = $form.find('#attachment').val() ? $form.find('#attachment').val().trim() : '';

                // Reset previous highlights
                $form.find('input, textarea, select').removeClass('input-error');

                function showFirstError($field, message) {
                    toastr_warning_js(message);
                    $field.addClass('input-error');
                    
                    try {
                        $field.focus();
                        let el = $field.get(0);
                        if (el && typeof el.scrollIntoView === 'function') {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } catch (err) {
                        // ignore
                    }
                }

                // 1) amount
                if (amount === '') {
                    showFirstError($form.find('#amount'), "{{ __('Proposal amount is required.') }}");
                    return false;
                }
                // numeric and > 0
                let amountNum = parseFloat(amount);
                if (isNaN(amountNum) || amountNum < 1) {
                    showFirstError($form.find('#amount'), "{{ __('Amount must be greater than 1.') }}");
                    return false;
                }

                // 2) duration
                if (!duration) {
                    showFirstError($form.find('#duration'), "{{ __('Please select a delivery time.') }}");
                    return false;
                }

                // 3) revision
                if (revision === '') {
                    // if pay_by_milestone is enabled, revision in the main form might not be used, but let's keep it if they need to provide a global one
                    // actually if it's milestone, we can skip main form revision validation
                    if ($('#pay_by_milestone').val() !== 'pay-by-milestone') {
                        showFirstError($form.find('#revision'), "{{ __('Revision field is required.') }}");
                        return false;
                    }
                }
                
                let pay_by_milestone = $('#pay_by_milestone').val();
                if(pay_by_milestone === 'pay-by-milestone') {
                    let total_milestone_price = 0;
                    let has_empty = false;
                    
                    $('.milestone_title').each(function() {
                        if($(this).val() == '') { has_empty = true; showFirstError($(this), "{{ __('Milestone title is required.') }}"); return false; }
                    });
                    if(has_empty) return false;
                    
                    $('.milestone_description').each(function() {
                        if($(this).val() == '') { has_empty = true; showFirstError($(this), "{{ __('Milestone description is required.') }}"); return false; }
                    });
                    if(has_empty) return false;
                    
                    $('.milestone_price').each(function() {
                        if($(this).val() == '') { has_empty = true; showFirstError($(this), "{{ __('Milestone price is required.') }}"); return false; }
                        total_milestone_price += parseFloat($(this).val());
                    });
                    if(has_empty) return false;
                    
                    $('.milestone_revision').each(function() {
                        if($(this).val() == '') { has_empty = true; showFirstError($(this), "{{ __('Milestone revision is required.') }}"); return false; }
                    });
                    if(has_empty) return false;
                    
                    if (amountNum !== total_milestone_price) {
                        toastr_warning_js("{{ __('Total milestone price must be equal to proposal amount.') }}");
                        return false;
                    }
                }

                // 4) cover letter
                if (cover_letter === '') {
                    showFirstError($form.find('#cover_letter'), "{{ __('Cover letter is required.') }}");
                    return false;
                }
                if (cover_letter.length < 10) {
                    showFirstError($form.find('#cover_letter'), "{{ __('Cover letter must be at least 10 characters.') }}");
                    return false;
                }

                // 5) attachment
                if (attachment === '') {
                    showFirstError($form.find('#attachment'), "{{ __('Please upload an attachment file.') }}");
                    return false;
                }

                // All good — proceed with submit
                $('#send_proposal_load_spinner').html('<i class="fas fa-spinner fa-pulse"></i>');
                // optionally disable button to prevent double submit
                $('.send_job_proposal').attr('disabled', 'true');
                $form.submit();
            });

            //tooltip
            $("body").tooltip({ selector: '[data-toggle=tooltip]' });

            function updateFeeAndReceive() {
                let amount = parseFloat($('#job_proposal_form #amount').val());
                let commissionType = '{{ get_static_option('admin_commission_type') }}';
                let commissionCharge = parseFloat('{{ get_static_option('admin_commission_charge') }}');

                if (!isNaN(amount) && amount > 0) {
                    let fee = 0;
                    if (commissionType === 'percentage') {
                        fee = (amount * commissionCharge / 100).toFixed(2);
                    } else if (commissionType === 'fixed') {
                        fee = commissionCharge.toFixed(2);
                    }
                    let receive = (amount - fee).toFixed(2);
                    $('#service_fee').val(fee);
                    $('#you_receive').val(receive);
                } else {
                    $('#service_fee').val('');
                    $('#you_receive').val('');
                }
            }
            $('#job_proposal_form #amount').on('input', updateFeeAndReceive);
            // Run on page load
            updateFeeAndReceive();
            // Milestone toggles
            $(document).on('click','#pay_by_milestone_btn',function(){
                $('.milestone_wrapper').removeClass('d-none');
                $('#pay_by_milestone').val('pay-by-milestone');
                $('#pay_at_once').val('');
                $( "#pay_by_milestone_btn").addClass( "active" );
                $( "#pay_at_once_btn").removeClass( "active" );
            });

            $(document).on('click','#pay_at_once_btn',function(){
                $('.milestone_wrapper').addClass('d-none');
                $('#pay_by_milestone').val('');
                $('#pay_at_once').val('pay-at-once');
                $( "#pay_at_once_btn").addClass( "active" );
                $( "#pay_by_milestone_btn").removeClass( "active" );
            });

            // add milestone
            $(document).on('click','.add-contract-milestone',function(){
                let html = `
                    <div class="myJob-wrapper-single-milestone-item mt-3 p-3 border rounded">
                        <div class="myJob-wrapper-single-flex flex-between align-items-start">
                            <div class="myJob-wrapper-single-contents w-100">
                                <div class="row g-4">
                                    <div class="col-sm-12">
                                        <div class="single-input">
                                            <label class="label-title">{{ __('Title') }}</label>
                                            <input type="text" class="form-control milestone_title" name="milestone_title[]" placeholder="{{ __('Enter Title') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="single-input">
                                            <label class="label-title">{{ __('Description') }}</label>
                                            <textarea cols="30" rows="3" class="form-control milestone_description" name="milestone_description[]" placeholder="{{ __('Enter Description') }}"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="single-input">
                                            <label class="label-title">{{ __('Price') }}</label>
                                            <input type="number" class="form-control milestone_price" name="milestone_price[]" placeholder="{{ __('Enter Price') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="single-input">
                                            <label class="label-title">{{ __('Revision') }}</label>
                                            <input type="number" min="1" max="100" class="form-control milestone_revision" name="milestone_revision[]" placeholder="{{ __('Enter Revision') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="single-input">
                                            <label class="label-title">{{ __('Delivery Time') }}</label>
                                            <select class="form-control milestone_deadline set_dead_line select2" name="milestone_deadline[]">
                                                <option value="1 Days">{{ __('1 Days') }}</option>
                                                <option value="2 Days">{{ __('2 Days') }}</option>
                                                <option value="3 Days">{{ __('3 Days') }}</option>
                                                <option value="Less than a week">{{ __('Less than a week') }}</option>
                                                <option value="Less than a month">{{ __('Less than a month') }}</option>
                                                <option value="Less than 2 month">{{ __('Less than 2 month') }}</option>
                                                <option value="Less than 3 month">{{ __('Less than 3 month') }}</option>
                                                <option value="More than 3 month">{{ __('More than 3 month') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-wrapper remove-milestone-contractor mt-3">
                            <a href="javascript:void(0)" class="btn-profile btn-bg-cancel text-danger">{{ __('Remove') }}</a>
                        </div>
                    </div>`;
                $('.milestone-contractor-parent').append(html);
                if($.fn.select2) {
                    $('.select2').select2();
                }
            });

            // remove milestone
            $(document).on('click','.remove-milestone-contractor',function(){
                $(this).closest('.myJob-wrapper-single-milestone-item').remove();
                calculateMilestonePrice();
            });

            // calculate milestone total price
            $(document).on('keyup change','.milestone_price',function(){
                calculateMilestonePrice();
            });

            function calculateMilestonePrice() {
                let total = 0;
                let hasMilestones = false;
                $('.milestone_price').each(function() {
                    let val = parseFloat($(this).val());
                    if (!isNaN(val)) {
                        total += val;
                        hasMilestones = true;
                    }
                });
                
                // Only update amount if there are milestones with prices
                // Otherwise, restore the original budget
                if (hasMilestones) {
                    $('#amount').val(total);
                } else {
                    // Restore original budget when all milestones are removed
                    let originalBudget = parseFloat('{{ $job_details->budget ?? 0 }}');
                    $('#amount').val(originalBudget);
                }
                updateFeeAndReceive();
            }

        });
    }(jQuery));
</script>
