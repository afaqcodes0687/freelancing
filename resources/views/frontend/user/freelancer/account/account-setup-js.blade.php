<script>
    (function ($) {
        "use strict";
        pre_next();
        $(document).ready(function () {

            // change country and get state
            $(document).on('change', '#country_id , #edit_country_id', function () {
                let country = $(this).val();
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.state.all') }}",
                    data: {
                        country: country
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select State')}}</option>";
                            let all_state = res.states;
                            $.each(all_state, function (index, value) {
                                all_options += "<option value='" + value.id +
                                    "'>" + value.state + "</option>";
                            });
                            $(".get_country_state").html(all_options);
                            $(".state_info").html('');
                            if (all_state.length <= 0) {
                                $(".state_info").html('<span class="text-danger"> {{ __('No state found for selected country!') }} <span>');
                            }
                        }
                    }
                })
            })

            // todo add experience
            $(document).on('click', '.add_experience', function () {
            let experience_title = $('#experience_title').val().trim();
            let organization = $('#organization').val().trim();
            let address = $('#address').val().trim();
            let short_description = $('#short_description').val().trim();
            let start_date = $('#start_date').val().trim();
            let end_date = $('#end_date').val().trim();

            // Regex for phone numbers (digits, +, - , spaces)
            let phoneRegex = /(\+?\d[\d\-\s]{6,}\d)/;

            // Regex for email addresses
            let emailRegex = /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i;

            // Reset previous error highlight
            $('#experience_title, #organization, #address, #short_description, #start_date, #end_date')
                .removeClass('input-error');

            // Step-by-step validation (only show one warning)
            if (experience_title === '') {
                toastr_warning_js("{{ __('Please enter experience title!') }}");
                $('#experience_title').addClass('input-error').focus();
                return false;
            }

            if (organization === '') {
                toastr_warning_js("{{ __('Please enter organization name!') }}");
                $('#organization').addClass('input-error').focus();
                return false;
            }

            if (address === '') {
                toastr_warning_js("{{ __('Please enter address!') }}");
                $('#address').addClass('input-error').focus();
                return false;
            }

            if (short_description === '') {
                toastr_warning_js("{{ __('Please enter short description!') }}");
                $('#short_description').addClass('input-error').focus();
                return false;
            }

            // Prevent phone/email in description
            if (phoneRegex.test(short_description)) {
                toastr_warning_js("{{ __('Phone numbers are not allowed in description!') }}");
                $('#short_description').addClass('input-error').focus();
                return false;
            }

            if (emailRegex.test(short_description)) {
                toastr_warning_js("{{ __('Email addresses are not allowed in description!') }}");
                $('#short_description').addClass('input-error').focus();
                return false;
            }

            if (start_date === '') {
                toastr_warning_js("{{ __('Please select start date!') }}");
                $('#start_date').addClass('input-error').focus();
                return false;
            }

            if (end_date !== '' && start_date > end_date) {
                toastr_warning_js("{{ __('Start date must not be greater than end date!') }}");
                $('#end_date').addClass('input-error').focus();
                return false;
            }

            // ✅ AJAX call (if all validation passes)
            $.ajax({
                url: "{{ route('freelancer.account.experience.add') }}",
                type: 'POST',
                data: {
                    experience_title: experience_title,
                    organization: organization,
                    address: address,
                    short_description: short_description,
                    country_id: 1,
                    state_id: 1,
                    start_date: start_date,
                    end_date: end_date,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (res) {
                    if (res.status === 'ok') {
                        $('.popup-fixed, .popup-overlay').removeClass('popup-active');
                        $('#display_user_experience_data').load(location.href + " #display_user_experience_data");
                        $('form[name="addExperienceForm"]')[0].reset();
                        toastr_success_js("{{ __('Experience Successfully Added') }}");

                        // Move to education tab
                        current = 2;
                        toggleListings();
                        toggleSections();

                        setTimeout(function () {
                            current = 3;
                            toggleListings();
                            toggleSections();
                        }, 1000);
                    }
                },
                error: function (xhr) {
                    toastr_warning_js("{{ __('Something went wrong. Please try again!') }}");
                    console.error(xhr.responseText);
                }
            });
        });



            // edit experience
            $(document).on('click', '.edit_single_experience', function () {
                let id = $(this).data('id');
                let title = $(this).data('title');
                let organization = $(this).data('organization');
                let address = $(this).data('address');
                let short_description = $(this).data('short_description');
                let start_date = $(this).data('start_date');
                let end_date = $(this).data('end_date');

                $('#edit_id').val(id);
                $('#edit_experience_title').val(title);
                $('#edit_organization').val(organization);
                $('#edit_address').val(address);
                $('#edit_short_description').val(short_description);
                $('#edit_start_date').val(start_date);
                $('#edit_start_date').parent().find('.date-picker').val(start_date);
                $('#edit_end_date').parent().find('.date-picker').val(end_date);
                $('#edit_end_date').val(end_date);
            });

            // ✅ Update Experience Script (with full validation)
            $(document).on('click', '.update_single_experience', function () {
                let id = $('#edit_id').val().trim();
                let experience_title = $('#edit_experience_title').val().trim();
                let organization = $('#edit_organization').val().trim();
                let address = $('#edit_address').val().trim();
                let short_description = $('#edit_short_description').val().trim();
                let start_date = $('#edit_start_date').val().trim();
                let end_date = $('#edit_end_date').val().trim();

                // Regex for phone numbers (digits, +, - , spaces)
                let phoneRegex = /(\+?\d[\d\-\s]{6,}\d)/;

                // Regex for email addresses
                let emailRegex = /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i;

                // Remove previous error highlights
                $('#edit_experience_title, #edit_organization, #edit_address, #edit_short_description, #edit_start_date, #edit_end_date')
                    .removeClass('input-error');

                // 🔹 Step-by-step validation
                if (experience_title === '') {
                    toastr_warning_js("{{ __('Please enter experience title!') }}");
                    $('#edit_experience_title').addClass('input-error').focus();
                    return false;
                }

                if (organization === '') {
                    toastr_warning_js("{{ __('Please enter organization name!') }}");
                    $('#edit_organization').addClass('input-error').focus();
                    return false;
                }

                if (address === '') {
                    toastr_warning_js("{{ __('Please enter address!') }}");
                    $('#edit_address').addClass('input-error').focus();
                    return false;
                }

                if (short_description === '') {
                    toastr_warning_js("{{ __('Please enter short description!') }}");
                    $('#edit_short_description').addClass('input-error').focus();
                    return false;
                }

                // 🔹 Prevent phone/email inside description
                if (phoneRegex.test(short_description)) {
                    toastr_warning_js("{{ __('Phone numbers are not allowed in description!') }}");
                    $('#edit_short_description').addClass('input-error').focus();
                    return false;
                }

                if (emailRegex.test(short_description)) {
                    toastr_warning_js("{{ __('Email addresses are not allowed in description!') }}");
                    $('#edit_short_description').addClass('input-error').focus();
                    return false;
                }

                if (start_date === '') {
                    toastr_warning_js("{{ __('Please select start date!') }}");
                    $('#edit_start_date').addClass('input-error').focus();
                    return false;
                }

                if (end_date !== '' && start_date > end_date) {
                    toastr_warning_js("{{ __('Start date must not be greater than end date!') }}");
                    $('#edit_end_date').addClass('input-error').focus();
                    return false;
                }

                // ✅ If all validation passes → Proceed with AJAX Update
                $.ajax({
                    url: "{{ route('freelancer.account.experience.update') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        experience_title: experience_title,
                        organization: organization,
                        address: address,
                        short_description: short_description,
                        country_id: 1, // optional if required like add function
                        state_id: 1,   // optional if required like add function
                        start_date: start_date,
                        end_date: end_date,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (res) {
                        if (res.status === 'ok') {
                            $('.popup-fixed, .popup-overlay').removeClass('popup-active');
                            $('#display_user_experience_data').load(location.href + " #display_user_experience_data");
                            toastr_success_js("{{ __('Experience Successfully Updated') }}");
                        } else {
                            toastr_warning_js("{{ __('Unable to update experience. Please try again!') }}");
                        }
                    },
                    error: function (xhr) {
                        toastr_warning_js("{{ __('Something went wrong. Please try again!') }}");
                        console.error(xhr.responseText);
                    }
                });
            });


            // todo add education
            $(document).on('click', '.add_education', function () {
            let institution = $('#institution').val().trim();
            let degree = $('#degree').val().trim();
            let subject = $('#subject').val().trim();
            let start_date = $('#start_date_edu').val().trim();
            let end_date = $('#end_date_edu').val().trim();

            // Remove previous highlights
            $('#institution, #degree, #subject, #start_date_edu, #end_date_edu').removeClass('input-error');

            // 🔹 Step-by-step validation
            if (institution === '') {
                toastr_warning_js("{{ __('Please enter your institution name!') }}");
                $('#institution').addClass('input-error').focus();
                return false;
            }

            if (degree === '') {
                toastr_warning_js("{{ __('Please enter your degree!') }}");
                $('#degree').addClass('input-error').focus();
                return false;
            }

            if (subject === '') {
                toastr_warning_js("{{ __('Please enter your major or field of study!') }}");
                $('#subject').addClass('input-error').focus();
                return false;
            }

            if (start_date === '') {
                toastr_warning_js("{{ __('Please select your start date!') }}");
                $('#start_date_edu').addClass('input-error').focus();
                return false;
            }

            // Optional end date: only check if provided, and not before start date
            if (end_date !== '' && start_date > end_date) {
                toastr_warning_js("{{ __('Start date must not be greater than end date!') }}");
                $('#end_date_edu').addClass('input-error').focus();
                return false;
            }

            // ✅ All validation passed → Proceed with AJAX
            $.ajax({
                url: "{{ route('freelancer.account.education.add') }}",
                type: 'POST',
                data: {
                    institution: institution,
                    degree: degree,
                    subject: subject,
                    start_date: start_date,
                    end_date: end_date,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (res) {
                    if (res.status === 'ok') {
                        $('.popup-fixed, .popup-overlay').removeClass('popup-active');
                        $('#display_user_education_data').load(location.href + " #display_user_education_data");
                        $('form[name="addEducationForm"]')[0].reset();
                        toastr_success_js("{{ __('Education Successfully Added') }}");

                        // Move to work tab
                        current = 3;
                        toggleListings();
                        toggleSections();

                        setTimeout(function () {
                            current = 4;
                            toggleListings();
                            toggleSections();
                        }, 1000);
                    } else {
                        toastr_warning_js("{{ __('Unable to add education. Please try again!') }}");
                    }
                },
                error: function (xhr) {
                    toastr_warning_js("{{ __('Something went wrong. Please try again!') }}");
                    console.error(xhr.responseText);
                }
            });
        });

            // edit education
            $(document).on('click', '.edit_single_education', function () {
                let id = $(this).data('id');
                let institution = $(this).data('institution');
                let subject = $(this).data('subject');
                let degree = $(this).data('degree');
                let start_date = $(this).data('start_date');
                let end_date = $(this).data('end_date');

                $('#edit_id').val(id);
                $('#edit_institution').val(institution);
                $('#edit_subject').val(subject);
                $('#edit_degree').val(degree);
                $('#edit_start_date_edu').val(start_date);
                $('#edit_start_date_edu').parent().find('.date-picker').val(start_date);
                $('#edit_end_date_edu').val(end_date);
                $('#edit_end_date_edu').parent().find('.date-picker').val(end_date);
            });

            // update education
            $(document).on('click', '.update_single_education', function () {
                let id = $('#edit_id').val();
                let institution = $('#edit_institution').val();
                let subject = $('#edit_subject').val();
                let degree = $('#edit_degree').val();
                let start_date = $('#edit_start_date_edu').val();
                let end_date = $('#edit_end_date_edu').val();
                if (institution == '' || subject == '' || degree == '' || start_date == '' || end_date == '') {
                    toastr_warning_js('Please fill all fields !');
                    return false;
                } else {
                    $.ajax({
                        url: "{{ route('freelancer.account.education.update') }}",
                        type: 'post',
                        data: {
                            id: id,
                            institution: institution,
                            subject: subject,
                            degree: degree,
                            start_date: start_date,
                            end_date: end_date,
                        },
                        success: function (res) {
                            if (res.status == 'ok') {
                                $('.popup-fixed, .popup-overlay').removeClass('popup-active');
                                $('#display_user_education_data').load(location.href + " #display_user_education_data");
                                $(addExperienceForm)[0].reset();
                                toastr_success_js("{{ __('Education Successfully Updated') }}");
                            }
                        }
                    });
                }
            });

            // get subcategories
            $(document).ready(function () {

                $(document).on('show.bs.collapse', '.accordion-collapse', function () {
                    let categoryId = $(this).attr('id').replace('collapse', '');
                    let container = $("#subcategory-" + categoryId);

                    if (container.data('loaded')) return;

                    loadSubcategories(categoryId, 0);
                    container.data('loaded', true);
                });

                // Load More button
                $(document).on('click', '.load-more-subcategories', function () {
                    let categoryId = $(this).data('category');
                    let offset = $(this).data('offset');
                    loadSubcategories(categoryId, offset, true);
                    $(this).data('offset', offset + 10);
                });

                function loadSubcategories(categoryId, offset, append = false) {
                    let container = $("#subcategory-" + categoryId);
                    let loadMoreBtn = $(".load-more-subcategories[data-category='" + categoryId + "']");

                    $.ajax({
                        method: 'post',
                        url: "{{ route('au.subcategory.all') }}",
                        data: { category: categoryId, offset: offset, _token: '{{ csrf_token() }}' },
                        beforeSend: function () {
                            if (!append) {
                                container.html('<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                            }
                            loadMoreBtn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                        },

                        success: function (res) {
                            if (res.status === 'success' && res.subcategories.length > 0) {
                                let html = '';
                                res.subcategories.forEach(sc => {
                                    html += `
                                    <div class="subcategory-card">
                                        <input type="checkbox" id="sub-${sc.id}" class="subcategory-checkbox" 
                                            data-id="${sc.id}" data-name="${sc.sub_category}">
                                        <label for="sub-${sc.id}">${sc.sub_category}</label>
                                    </div>`;
                                });
                                container.html(html);
                                loadMoreBtn.removeClass('d-none').text("Load More");
                            } else {
                                container.html('<div class="text-danger">No subcategories found</div>');
                                loadMoreBtn.addClass('d-none');
                            }
                        }

                    });
                }

                $(document).ready(function () {
                    let shown = 6;
                    let step = 3;

                    $("#loadMoreCategories").on("click", function () {
                        let total = $(".category-item").length;

                        $(".category-item.d-none").slice(0, step).removeClass("d-none");

                        shown += step;

                        if (shown >= total) {
                            $(this).fadeOut();
                        }
                    });
                });


                let selectedData = {};

                $(document).on('change', '.subcategory-checkbox', function () {
                    let catId = $(this).closest('.accordion-collapse').attr('id').replace('collapse', '');
                    let subId = $(this).data('id');
                    let subName = $(this).data('name');

                    if (!selectedData[catId]) {
                        selectedData[catId] = [];
                    }

                    if ($(this).is(':checked')) {
                        selectedData[catId].push(subId);
                        $("#selected-subcategories").append(`
                    <span class="subcategory-chip" data-cat="${catId}" data-id="${subId}">
                        ${subName} <span class="remove-sub">&times;</span>
                    </span>
                `);

                        $(`#heading${catId} .accordion-button`).addClass("selected-category");

                    } else {
                        selectedData[catId] = selectedData[catId].filter(id => id !== subId);
                        $(`.subcategory-chip[data-id="${subId}"][data-cat="${catId}"]`).remove();

                        if (selectedData[catId].length === 0) {
                            delete selectedData[catId];
                            $(`#heading${catId} .accordion-button`).removeClass("selected-category");
                        }
                    }

                    updateHiddenInput();
                });

                $(document).on('click', '.remove-sub', function () {
                    let subId = $(this).parent().data('id');
                    let catId = $(this).parent().data('cat');

                    $(this).parent().remove();
                    $(`.subcategory-checkbox[data-id="${subId}"][data-cat="${catId}"]`).prop('checked', false);

                    selectedData[catId] = selectedData[catId].filter(id => id !== subId);
                    if (selectedData[catId].length === 0) {
                        delete selectedData[catId];
                        $(`#heading${catId} .accordion-button`).removeClass("selected-category");
                    }

                    updateHiddenInput();
                });

                function updateHiddenInput() {
                    $("#subcategory_input").val(JSON.stringify(selectedData));
                }
            });

            $(document).on('click', '.choose_a_subcategory', function () {
                let sub_category = $(this).data('id');
                $('#set_sub_category_id').val(sub_category); //set sub category id
            });

            // search category
            $(document).on('keyup', '#category_search_string', function () {
                let string_search = $(this).val();
                $.ajax({
                    url: "{{ route('freelancer.account.category.search') }}",
                    method: 'GET',
                    data: { string_search: string_search },
                    success: function (res) {
                        if (res.status == 'nothing') {
                            $('.search_result').html('<h5 class="text-center text-danger">' + "{{ __('Nothing Found') }}" + '</h5>');
                        } else {
                            $('.search_result').html(res);
                        }
                    }
                });
            });

            //choose skill
            let myTagInput = null;

            function initSkillTags(skillsArray = []) {
                myTagInput = new TagsInputs({
                    selector: 'skill_input',
                    duplicate: false,
                    max: 30,
                });

                skillsArray.forEach(skill => {
                    if (skill.trim() !== "") {
                        myTagInput.addData([skill]);
                    }
                });

                $(document).off('click', '.choose_skill').on('click', '.choose_skill', function () {
                    let skill = $(this).text();
                    myTagInput.addData([skill]);
                });
            }

            document.querySelector('#upload_profile_photo').addEventListener('change', function () {
                $("#profilePhotoModal").modal('show');
                if (this.files && this.files[0]) {
                    let img = document.querySelector('.profile_photo_preview');
                    img.onload = () => {
                        URL.revokeObjectURL(img.src);
                    }
                    img.src = URL.createObjectURL(this.files[0]);
                    document.querySelector(".profile_photo_upload").files = this.files;
                    $("#crop").trigger("click");
                }
            });

            //profile photo save
            $(document).on('submit', '#profilePhotoUploadForm', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('freelancer.account.profile.photo.upload') }}",
                    method: 'post',
                    data: new FormData(e.target),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: () => { },
                    success: (res) => {
                        if (res.status == 'uploaded') {
                            $('#profilePhotoModal').modal('hide');
                            $('.profile_photo_area').load(location.href + ' .profile_photo_area');
                        } else {
                            $('.error_msg').html('');
                        }
                    }, errors: (err) => {
                    }
                });
            });
        });

        $(document).on('click', '.show-more-skills', function () {
            let target = $(this).data('target');
            let visibleCount = parseInt($(this).data('visible'));
            let $list = $("#" + target).find(".skill-tag");

            console.log("Show More Clicked → target:", target, "visibleCount:", visibleCount, "Total:", $list.length);

            $list.slice(visibleCount, visibleCount + 10).removeClass("d-none");

            $(this).data('visible', visibleCount + 10);

            if ($list.length <= visibleCount + 10) {
                $(this).hide();
            }
        });

    }(jQuery));

    function pre_next() {
        let Listings = document.querySelectorAll(".single-setup-request-list li");
        let sections = document.querySelectorAll(".setup-wrapper-contents");
        let nextButton = document.querySelector("#next");
        let prevButton = document.querySelector("#previous");
        let current = 0;

        const toggleListings = () => {
            Listings.forEach(function (e) {
                e.classList.remove('running');
            });
            Listings[current]?.classList?.add("running");
            Listings[current]?.classList?.remove("completed");
            if (current != 0) {
                Listings[current - 1]?.classList?.add("completed");
            }
        }

        const toggleSections = () => {
            sections.forEach(function (section) {
                section?.classList?.remove('active');
            });
            sections[current]?.classList?.add("active");
        }

        if (nextButton != null) {
            nextButton.addEventListener("click", function (e) {
                e.preventDefault();
                if (current <= Listings.length - 1) {
                    current++

                    // todo add introduction
                    if (current == 1) {
                        let title = $('#title').val().trim();
                        let description = $('#description').val().trim();

                        // Regex for phone numbers (digits with optional +, - , spaces)
                        let phoneRegex = /(\+?\d[\d\-\s]{6,}\d)/;

                        let emailRegex = /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i;

                        if (title === '' || description === '') {
                            current = 0;
                            toastr_warning_js("{{ __('Please fill title and description !') }}");
                            return false;
                        }

                        if (phoneRegex.test(description)) {
                            current = 0;
                            toastr_warning_js("{{ __('Phone numbers are not allowed in description !') }}");
                            return false;
                        }

                        if (emailRegex.test(description)) {
                            current = 0;
                            toastr_warning_js("{{ __('Email addresses are not allowed in description !') }}");
                            return false;
                        }

                        // If all good → submit AJAX
                        $.ajax({
                            url: "{{ route('freelancer.account.introduction.add') }}",
                            type: 'post',
                            data: {
                                title: title,
                                description: description
                            },
                            success: function (res) {
                                if (res.status == 'ok') {
                                    toastr_success_js("{{ __('Introduction Successfully Updated') }}");
                                }
                            }
                        });
                    }
                    // todo add experience
                    else if (current == 2) {
                        let hasExperience = $('#display_user_experience_data .setup-wrapper-experience-details').length > 0;
                        if (!hasExperience) {
                            current = 1;
                            toastr_warning_js("{{ __('Please add at least one experience !') }}");
                            return false;
                        }
                    }
                    // todo add education
                    else if (current == 3) {
                        let hasEducation = $('#display_user_education_data .setup-wrapper-experience-details').length > 0;
                        if (!hasEducation) {
                            current = 2;
                            toastr_warning_js("{{ __('Please add at least one education !') }}");
                            return false;
                        }
                    }
                    // todo add work
                    // Step 4 => Category & Subcategory Save
                    else if (current == 4) {
                        let selectedData = $('#subcategory_input').val();

                        if (selectedData == '' || selectedData == '{}') {
                            current = 3;
                            toastr_warning_js("{{ __('Please choose at least one category & subcategory !') }}");
                            return false;
                        } else {
                            $.ajax({
                                url: "{{ route('freelancer.account.work.add') }}",
                                type: 'post',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    subcategories: selectedData
                                },
                                success: function (res) {
                                    if (res.status == 'ok') {
                                        toastr_success_js("{{ __('Work Successfully Updated') }}");

                                        $('.setup-wrapper-contents').eq(4).load(
                                            location.href + " .setup-wrapper-contents:eq(4) > *",
                                            function initSkillInput() {

                                                window.myTagInput = new TagsInputs({
                                                    selector: 'skill_input',
                                                    duplicate: false,
                                                    max: 30,
                                                });

                                                @php
                                                    $skills = \App\Models\UserSkill::select('skill')
                                                        ->where('user_id', Auth::guard('web')->user()->id)
                                                        ->first()->skill ?? '';
                                                    $array_skill = explode(",", $skills);
                                                    $array_length = count($array_skill);
                                                @endphp

                                                @for ($i = 0; $i < $array_length; $i++)
                                                    @if (!empty($array_skill[$i]))
                                                        myTagInput.addData(["{{ trim($array_skill[$i]) }}"]);
                                                    @endif
                                                @endfor

                                                $(document).off('click', '.choose_skill').on('click', '.choose_skill', function () {
                                                    let skill = $(this).text().trim();

                                                    let currentSkills = [];
                                                    if (typeof myTagInput.getData === "function") {
                                                        currentSkills = myTagInput.getData();
                                                    } else if (Array.isArray(myTagInput.tagsArray)) {
                                                        currentSkills = myTagInput.tagsArray;
                                                    } else if (Array.isArray(myTagInput.items)) {
                                                        currentSkills = myTagInput.items;
                                                    } else {
                                                        let rawVal = $('#skill_input').val();
                                                        if (rawVal) {
                                                            currentSkills = rawVal.split(',').map(s => s.trim());
                                                        }
                                                    }

                                                    if (currentSkills.map(s => s.toLowerCase()).includes(skill.toLowerCase())) {
                                                        toastr_warning_js("{{ __('This skill is already selected!') }}");
                                                        return false;
                                                    }

                                                    myTagInput.addData([skill]);
                                                });
                                            }
                                        );

                                        // ✅ Next tab (skills)
                                        toggleListings();
                                        toggleSections();
                                    } else {
                                        current = 3;
                                        toastr_warning_js(res.message ?? "Please first select category & subcategory !");
                                    }
                                },
                                error: function (xhr) {
                                    current = 3;
                                    let msg = "Please first select category & subcategory !";
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        msg = xhr.responseJSON.message;
                                    }
                                    toastr_warning_js(msg);
                                }
                            });

                            return false;
                        }
                    }

                    // Step 5 => Skills Save
                    else if (current == 5) {
                        if (!window.myTagInput) {
                            toastr_warning_js("Skill input not initialized properly. Please reload page.");
                            current = 4;
                            return false;
                        }

                        let skillData = [];

                        if (typeof myTagInput.getData === "function") {
                            skillData = myTagInput.getData();
                        }
                        else if (typeof myTagInput.getArray === "function") {
                            skillData = myTagInput.getArray();
                        }
                        else if (Array.isArray(myTagInput.tagsArray)) {
                            skillData = myTagInput.tagsArray;
                        }
                        else if (Array.isArray(myTagInput.items)) {
                            skillData = myTagInput.items;
                        }
                        else {
                            let rawVal = $('#skill_input').val();
                            if (rawVal) {
                                skillData = rawVal.split(',').map(s => s.trim()).filter(Boolean);
                            }
                        }

                        console.log("👉 Final skillData:", skillData);

                        // Validation
                        if (!Array.isArray(skillData) || skillData.length === 0) {
                            current = 4;
                            toastr_warning_js("{{ __('You must add one or more skills !') }}");
                            return false;
                        }

                        let skill = skillData.join(",");

                        // ✅ Save via AJAX
                        $.ajax({
                            url: "{{ route('freelancer.account.skill.add') }}",
                            type: 'post',
                            data: {
                                _token: '{{ csrf_token() }}',
                                skill: skill
                            },
                            success: function (res) {
                                if (res.status == 'ok') {
                                    toastr_success_js("{{ __('Skill Successfully Updated') }}");
                                }
                            },
                            error: function () {
                                toastr_warning_js("{{ __('Something went wrong. Please try again.') }}");
                            }
                        });
                    }

                    // ✅ Profile photo update and Account Setup complete here
                    else if (current == 6) {
                        let hourly_rate = $('#hourly_rate').val();

                        if (hourly_rate == '') {
                            current = 5;
                            toastr_warning_js("{{ __('You must add hourly rate!') }}");
                            return false;
                        } else {
                            $.ajax({
                                url: "{{ route('freelancer.account.hourly.rate.add') }}",
                                type: 'post',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    hourly_rate: hourly_rate
                                },
                                success: function (res) {
                                    if (res.status == 'ok') {
                                        toastr_success_js("{{ __('Hourly Rate Successfully Updated') }}");

                                        // ✅ Now mark Account Setup step as completed
                                        $('.identity-verifying-list').each(function () {
                                            if ($(this).text().trim().includes("Account Setup")) {
                                                $(this).addClass('completed');
                                            }
                                        });

                                        // Redirect logic
                                        let redirectPath = "{{ route('freelancer.wallet.history') }}";
                                        @if (!empty(request()->get('return')))
                                            redirectPath = "{{ url('/' . request()->get('return')) }}";
                                        @endif
                                        window.location = redirectPath;
                                    }
                                },
                                error: function () {
                                    toastr_warning_js("{{ __('Something went wrong. Please try again.') }}");
                                }
                            });
                        }
                    }
                }
                if (current != 6) {
                    toggleListings();
                    toggleSections();
                }
            })
        }

        if (prevButton != null) {
            prevButton.addEventListener("click", function (e) {
                if (current > 0) {
                    current--
                }
                toggleListings();
                toggleSections();
            });
        }
    }

    // todo toastr warning
    function toastr_warning_js(msg) {
        Command: toastr["warning"](msg, "Warning !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
    // todo toastr success
    function toastr_success_js(msg) {
        Command: toastr["success"](msg, "Success !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
</script>