<script>
    (function ($) {
        "use strict";
        pre_next();
        $(document).ready(function () {
            $('.category_select2').select2();
            $('.subcategory_select2').select2();

            // ===============================
            // Category → Subcategory Load
            // ===============================
            $('#subcategory_info').hide();
            $(document).on('change', '#category', function () {
                let category = $(this).val();
                $('#subcategory_info').show();
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.subcategory.all') }}",
                    data: { category: category },
                    success: function (res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select Sub Category')}}</option>";
                            $.each(res.subcategories, function (index, value) {
                                all_options += "<option value='" + value.id + "'>" + value.sub_category + "</option>";
                            });
                            $(".get_subcategory").html(all_options);
                            $("#subcategory_info").html('');
                            if (res.subcategories.length <= 0) {
                                $("#subcategory_info").html('<span class="text-danger">{{ __('No sub categories found for selected category!') }}</span>');
                            }
                        }
                    }
                })
            });

            // ===============================
            // SKILLS (Multiple Subcategories with Pagination + Duplicate Check)
            // ===============================
            let selectedSkills = @json($selectedSkills ?? []);
            let renderedSubcategories = {};
            let permanentlyDisabledSkills = []; // 🆕 store removed skills here


            // Render skills (10 at a time) for one subcategory
            function renderSkillsForSubcategory(subId, subName, skills, preselected = [], start = 0, batch = 10) {
                let wrapper = $(`#skills-subcategory-${subId}`);

                if (start === 0) wrapper.html("");

                if (skills.length === 0) {
                    wrapper.html('<p class="text-muted">No skills found for this subcategory.</p>');
                    return;
                }

                let end = Math.min(start + batch, skills.length);
                for (let i = start; i < end; i++) {
                    let skill = skills[i];

                    let isActive = preselected.includes(skill.id) ? 'active' : '';
                    let isDisabled = '';




                    wrapper.append(`
            <div class="choose-skill-item ${isActive} ${isDisabled}" data-id="${skill.id}" data-sub="${subId}">
                ${skill.skill}
            </div>
        `);

                    if (isActive) {
                        if ($(`#chip_${skill.id}`).length === 0) {
                            $("#selected-skills-list").append(`
                    <div class="selected-skill-chip" id="chip_${skill.id}" data-id="${skill.id}" data-sub="${subId}">
                        ${skill.skill} <i class="fa fa-times remove-skill"></i>
                        <input type="hidden" name="skill[]" value="${skill.id}" id="hidden_skill_${skill.id}">
                    </div>
                `);
                        }
                    }
                }

                // Remove old Show More & add new if needed
                $(`#skills-subcategory-${subId} .show-more-skills`).remove();
                if (end < skills.length) {
                    wrapper.append(`
                    <button type="button" class="show-more-skills btn btn-link p-0" data-sub="${subId}" data-start="${end}" data-batch="${batch}">
                        {{ __('Show More Skills') }}
                    </button>
                `);
                }
            }

            $(document).on('change', '#subcategory', function () {
                let sub_ids = $(this).val() || [];

                $(".skills-group").each(function () {
                    let subId = $(this).attr("id").replace("subcategory-block-", "");
                   if (!sub_ids.includes(subId)) {

                        $(`.selected-skill-chip[data-sub="${subId}"]`).each(function () {
                            let skillId = $(this).data("id");
                            
                            selectedSkills = selectedSkills.filter(id => id != skillId);

                            $(`#hidden_skill_${skillId}`).remove();

                            $(this).remove();
                        });

                        $(this).remove();
                        delete renderedSubcategories[subId];
                    }

                });

                // 2. ADD new subcategories (same as your working code)
                sub_ids.forEach(subId => {
                    if (renderedSubcategories[subId]) return;

                    $("#skills-wrapper").prepend(`
            <div class="skills-group" id="subcategory-block-${subId}">
                <div class="category-title">${$("#subcategory option[value='" + subId + "']").text()}</div>
                <div id="skills-subcategory-${subId}"><p style="color:#309400;">Loading skills...</p></div>
            </div>
        `);

                    $.ajax({
                        method: 'POST',
                        url: "{{ route('client.job.get.skills') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            subcategory_id: subId
                        },
                      
                        success: function (res) {
                            if (res.status === 'success') {
                                renderedSubcategories[subId] = res.skills;

                                // 🔹 Already selected skills IDs filter
                                let preselected = selectedSkills.filter(id => res.skills.some(s => s.id === id));

                                // 🔹 Render only first 10 in UI
                                renderSkillsForSubcategory(
                                    subId,
                                    $("#subcategory option[value='" + subId + "']").text(),
                                    res.skills,
                                    preselected,
                                    0,
                                    10
                                );

                                res.skills.forEach(skill => {
                                    if (preselected.includes(skill.id)) {
                                        if ($(`#chip_${skill.id}`).length === 0) {
                                            $("#selected-skills-list").append(`
                                                <div class="selected-skill-chip" id="chip_${skill.id}" data-id="${skill.id}" data-sub="${subId}">
                                                    ${skill.skill} <i class="fa fa-times remove-skill"></i>
                                                    <input type="hidden" name="skill[]" value="${skill.id}" id="hidden_skill_${skill.id}">
                                                </div>
                                            `);
                                        }
                                    }
                                });

                            } else {
                                $(`#skills-subcategory-${subId}`).html('<p class="text-muted">No skills found for this subcategory.</p>');
                            }
                        }
                    });
                });
            });

            // Show More Skills (next 10)
            $(document).on("click", ".show-more-skills", function () {
                let subId = $(this).data("sub");
                let start = parseInt($(this).data("start"));
                let batch = parseInt($(this).data("batch"));
                let skills = renderedSubcategories[subId];
                renderSkillsForSubcategory(subId, $("#subcategory option[value='" + subId + "']").text(), skills, selectedSkills, start, batch);
            });

            // Select/unselect skill
            $(document).on('click', '.choose-skill-item', function () {
                let skillId = $(this).data('id');
                let subId = $(this).data('sub');
                let skillName = $(this).text().trim().toLowerCase();

                if ($(this).hasClass("active")) {
                    $(this).removeClass("active");
                    delete selectedSkills[skillId];

                    $(`#chip_${skillId}`).remove();
                    $(`#hidden_skill_${skillId}`).remove();
                    return;
                }

                let alreadySelectedByName = false;
                $(".selected-skill-chip").each(function () {
                    if ($(this).text().trim().toLowerCase() === skillName) {
                        alreadySelectedByName = true;
                        return false; // break
                    }
                });

                if (alreadySelectedByName) {
                    toastr.warning("⚠️ This skill is already selected from another subcategory!");
                    return;
                }

                // ✅ Select skill
                $(this).addClass("active");
                selectedSkills[skillId] = subId;

                if ($(`#chip_${skillId}`).length === 0) {
                    $("#selected-skills-list").append(`
                        <div class="selected-skill-chip" id="chip_${skillId}" data-id="${skillId}" data-sub="${subId}">
                            ${$(this).text().trim()} <i class="fa fa-times remove-skill"></i>
                            <input type="hidden" name="skill[]" value="${skillId}" id="hidden_skill_${skillId}">
                        </div>
                    `);
                }
            });

            // Remove from chip
            $(document).on('click', '.remove-skill', function (e) {
                e.stopPropagation();
                let chip = $(this).closest('.selected-skill-chip');
                let skillId = chip.data('id');
                let subId = chip.data('sub');

                chip.remove();
                delete selectedSkills[skillId]; // remove from map
                $(`.choose-skill-item[data-id="${skillId}"][data-sub="${subId}"]`).removeClass("active");
                $(`#hidden_skill_${skillId}`).remove();
            });

            // ===============================
            // Initial render (Edit Mode)
            // ===============================
            @if($job_details->job_sub_categories->count() > 0)
                let existingSubcats = @json($job_details->job_sub_categories->pluck('id'));
                $("#subcategory").val(existingSubcats).trigger("change");
            @endif
    });
    }(jQuery));

    // ===============================
    // Steps Navigation
    // ===============================
    function pre_next() {
        let Listings = document.querySelectorAll(".single-setup-request-list li");
        let sections = document.querySelectorAll(".setup-wrapper-contents");
        let current = 0;

        const toggleListings = () => {
            Listings.forEach(e => e.classList.remove('running'));
            Listings[current].classList.add("running");
            Listings[current].classList.remove("completed");
            if (current != 0) Listings[current - 1].classList.add("completed");
        }
        const toggleSections = () => {
            sections.forEach(s => s.classList.remove('active'));
            sections[current].classList.add("active");
        }

        $(document).on("click", "#next", function (e) {
            e.preventDefault();
            if (current <= Listings.length) {
                current++;
                if (current == 1) {
                    let category = $('#category').val();
                    let subcategory = $('#subcategory').val();
                    let title = $('#title').val();
                    let description = $('#description').val();
                    let level = $('#level').val();
                    let duration = $('#duration').val();
                    if (category == '' || subcategory == '' || title == '' || description == '' || level == '' || duration == '') {
                        current = 0;
                        toastr_warning_js("{{ __('Please fill all fields !') }}");
                        return false;
                    } else if (title.length < 5) {
                        current = 0;
                        toastr_warning_js("{{ __('Title must be at least 5 characters') }}");
                        return false;
                    } else if (description.length < 10) {
                        current = 0;
                        toastr_warning_js("{{ __('Description must be at least 10 characters') }}");
                        return false;
                    } else {
                        $('.setup-footer-right').html('<button type="submit" class="btn-profile btn-bg-1" id="confirm_edit_job">{{ __("Update Job") }}<span id="job_edit_load_spinner"></span></button>');
                    }
                }
            }
            toggleListings(); toggleSections();
        });

        $(document).on("click", "#previous", function () {
            if (current > 0) {
                current--;
                if (current == 2) {
                    $('.setup-footer-right').html('<input type="submit" class="btn-profile btn-bg-1" value="{{ __("Update Job") }}">');
                } else {
                    $('.setup-footer-right').html('<a href="javascript:void(0)" class="setup-footer-next next" id="next"><i class="fas fa-arrow-right"></i></a>');
                }
            }
            toggleListings(); toggleSections();
        });
    }
</script>