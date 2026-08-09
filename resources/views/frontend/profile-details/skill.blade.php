<style>
    .tags-input-wrapper input {
        display: none;
    }

    .hidden-skill {
        display: none !important;
    }

    .subcategory-block {
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
    }

    .subcategory-title {
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
    }

    .skill-list {
        list-style: none;
        padding-left: 0;
    }

    .skill-list li {
        margin: 5px 0;
    }

    .show-more-skills {
        font-size: 0.9rem;
        color: #309400;
        cursor: pointer;
        text-decoration: underline;
        border: none;
        background: none;
    }

    .show-more-skills:hover {
        text-decoration: none;
        color: #309400;
    }
</style>

@php
    $array_skill = array_filter(array_map('trim', explode(',', $skills)));
@endphp

@if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username == $username)
    <div class="profile-wrapper-item radius-10">
        <div class="profile-wrapper-item-flex flex-between align-items-center profile-border-bottom">
            <h4 class="profile-wrapper-item-title"> {{ __('Skills') }} </h4>
            <div class="profile-wrapper-item-plus display_edit_skill_wrapper edit_skill_show_hide">
                <i class="fa-regular fa-pen-to-square"></i>
            </div>
        </div>

        <ul class="setup-wrapper-work-list freelancer_skill_list">
            @foreach($array_skill as $skill)
                <li class="setup-wrapper-work-list-item"> {{ $skill }} </li>
            @endforeach
        </ul>

        <div class="edit_skill_wrapper">
            <div class="setup-wrapper-skill">
                <p class="setup-wrapper-skill-para">{{ __('Please choose skills only from the suggestions below') }}</p>

                <div class="setup-wrapper-skill-tagInputs mt-4">
                    <input type="text" id="skill_input" placeholder="{{ __('select tags') }}">
                </div>
            </div>

            <h6 class="setup-wrapper-experience-details-subtitle mt-4">{{ __('Suggested Skill') }}</h6>
            <ul class="setup-wrapper-work-list mt-3 suggested-skills-list">
                @php
                    $array_skill = array_filter(array_map('trim', explode(',', $skills ?? '')));
                @endphp

                @if($skills_according_to_category->isNotEmpty())
                    @foreach($skills_according_to_category as $category)
                        @foreach($category->sub_categories as $sub)
                            @php
                                $visibleSkills = $sub->skills->filter(function ($s) use ($array_skill) {
                                    return !in_array($s->skill, $array_skill);
                                })->values();
                                $initialShow = 15;
                            @endphp

                            @if($visibleSkills->isNotEmpty())
                                <div class="subcategory-block">
                                    <h6 class="subcategory-title ms-4">{{ $sub->sub_category }}</h6>

                                    <ul class="setup-wrapper-work-list ms-4 skill-list">
                                        @foreach($visibleSkills as $index => $skill)
                                            <li
                                                class="setup-wrapper-work-list-item choose_skill {{ $index >= $initialShow ? 'hidden-skill' : '' }}">
                                                {{ $skill->skill }}
                                            </li>
                                        @endforeach

                                        @if($visibleSkills->count() > $initialShow)
                                            <li>
                                                <button type="button" class="show-more-skills btn btn-link p-0"
                                                    data-initial="{{ $initialShow }}">
                                                    {{ __('Show More') }}
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                @else
                    <p>{{ __('No skills found for this category.') }}</p>
                @endif
            </ul>

            <div class="btn-wrapper d-flex justify-content-end mt-3">
                <a href="javascript:void(0)"
                    class="cmn-btn btn-bg-1 btn-small update_freelancer_skill">{{ __('Update Skills') }}</a>
            </div>
        </div>
    </div>
@else
    <div class="profile-wrapper-item radius-10">
        <div class="profile-wrapper-item-flex flex-between align-items-center profile-border-bottom">
            <h4 class="profile-wrapper-item-title"> {{ __('Skills') }} </h4>
        </div>
        <ul class="setup-wrapper-work-list">
            @foreach($array_skill as $skill)
                <li class="setup-wrapper-work-list-item"> {{ $skill }} </li>
            @endforeach
        </ul>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.show-more-skills').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const list = btn.closest('.skill-list');
                if (!list) return;

                const hiddenItems = Array.from(list.querySelectorAll('li.hidden-skill'));
                const batch = parseInt(btn.dataset.initial) || 15;

                for (let i = 0; i < batch && i < hiddenItems.length; i++) {
                    hiddenItems[i].classList.remove('hidden-skill');
                }

                if (list.querySelectorAll('li.hidden-skill').length === 0) {
                    btn.parentElement.remove(); 
                }
            });
        });
    });
</script>