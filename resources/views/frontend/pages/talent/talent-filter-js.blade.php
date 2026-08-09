<script>
(function ($) {
    "use strict";
    $(document).ready(function () {
        $('.country_select2').select2();
        $('#category').select2();
        $('#skill').select2();

        // 🔹 When category changes → show loader and fetch skills
        $(document).on('change', '#category', function () {
            let categoryId = $(this).val();

            // Show loader while fetching
            $('#skill').html('<option value="">{{ __("Loading...") }}</option>').trigger('change');

            // Fetch skills by category via AJAX
            $.ajax({
                url: "{{ route('get.skills.by.category') }}",
                type: "GET",
                data: { category_id: categoryId },
                success: function (response) {
                    let options = '<option value="">{{ __("Select Skill") }}</option>';
                    if (response.skills && response.skills.length > 0) {
                        $.each(response.skills, function (index, skill) {
                            options += `<option value="${skill.skill}">${skill.skill}</option>`;
                        });
                    } else {
                        options = '<option value="">{{ __("No skills found") }}</option>';
                    }

                    $('#skill').html(options).trigger('change');
                },
                error: function () {
                    $('#skill').html('<option value="">{{ __("Error loading skills") }}</option>').trigger('change');
                }
            });

            // Also refresh profiles when category changes
            profiles();
        });

        // 🔹 Filter triggers
        $(document).on('change', '#country , #level , #talent_badge, #skill, #get_pro_profile', function() {
            profiles();
        });

        // 🔹 Pagination click
        $(document).on('click', '.pagination a', function(e){
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            profiles(page);
        });

        // 🔹 Filter reset
        $(document).on('click', '#talent_filter_reset', function(e){
            e.preventDefault();
            $('#country, #talent_badge, #level, #category, #skill').val('').trigger('change');

            $.ajax({
                url:"{{ route('talents.filter.reset')}}",
                method:'GET',
                success:function(res){
                    if(res.status=='nothing'){
                        $('.search_talent_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                    }else{
                        $('.search_talent_result').html(res);
                    }
                }
            });
        });

        // 🔹 Fetch profiles
        function profiles(page=1){
            let country = $('#country').val();
            let talent_badge = $('#talent_badge').val();
            let category = $('#category').val();
            let level = $('#level').val();
            let skill = $('#skill').val();
            let get_pro_profiles;

            if($('#get_pro_profile').prop('checked')){
                $('#get_pro_profile').val('1')
                get_pro_profiles = $('#get_pro_profile').val()
            }else{
                $('#get_pro_profile').val('0')
                get_pro_profiles = $('#get_pro_profile').val()
            }

            $.ajax({
                url:"{{ route('talents.pagination').'?page='}}" + page,
                method:'GET',
                data:{country:country,talent_badge:talent_badge,level:level,category:category,skill:skill,get_pro_profiles:get_pro_profiles},
                success:function(res){
                    if(res.status=='nothing'){
                        $('.search_talent_result').html(
                            `<div class="congratulation-area section-bg-2 pat-100 pab-100">
                                <div class="container">
                                    <div class="congratulation-wrapper">
                                        <div class="congratulation-contents center-text">
                                            <div class="congratulation-contents-icon bg-danger">
                                                <i class="fas fa-times"></i>
                                            </div>
                                            <h4 class="congratulation-contents-title"> {{ __('OPPS!') }} </h4>
                                            <p class="congratulation-contents-para">{{ __('Nothing') }} <strong>{{ __('Found') }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                        );
                    } else {
                        $('.search_talent_result').html(res);
                    }

                    const talentSection = document.getElementById('talent-list-anchor');
                    if (talentSection) {
                        talentSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        }

    });
})(jQuery);
</script>
