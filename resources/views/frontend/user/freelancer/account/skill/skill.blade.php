<!-- Setup Skills Starts -->

<style>
    .subcategory-box {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        transition: 0.2s;
    }

    .subcategory-box:hover {

        border-color: #309400;
    }

    .subcategory-label {
        font-size: 16px;
        color: #333;
    }

    .skill-tag {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .skill-tag:hover {
        background: #309400;
        color: #fff;
        border-color: #309400;
    }

    .show-more-skills {
        font-size: 0.9rem;
        color: #309400;
        cursor: pointer;
        text-decoration: underline;
        border: none;
        background: none;
        margin-top: 10px;
        display: block;

    }
    /* Skill input wrapper */
    .setup-wrapper-skill-tagInputs {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }

    /* focus highlight */
    .setup-wrapper-skill-tagInputs:focus-within {
        border-color: #309400;
        box-shadow: 0 4px 10px rgba(48, 148, 0, 0.12);
    }

    /* input field */
    #skill_input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 15px;
        color: #333;
        background: transparent;
        padding: 6px 0;
    }

    /* placeholder styling */
    #skill_input::placeholder {
        color: #aaa;
        font-style: italic;
        font-size: 14px;
    }

    .tags-input-wrapper .tag{
        background-color: #309400;
        color: white;
    }
    .tags-input-wrapper {
        background: #f9f9f9;
    }
    .subcategory-checkbox {
        accent-color: #309400;
        transform: scale(1.2);
        margin-right: 10px;
    }

</style>
<div class="setup-wrapper-contents">
    <div class="setup-wrapper-contents-item">
        <h3 class="setup-wrapper-contents-title">
            {{ get_static_option('skill_title') ?? __('Great! Now add some skills you have') }}
        </h3>
        <div class="setup-wrapper-skill">
            <p class="setup-wrapper-skill-para">
                {{ __('Type and hit ↵ Enter to add a skill or choose from suggestions below') }}
            </p>
            <div class="setup-wrapper-skill-tagInputs mt-4">
                <input type="text" id="skill_input" placeholder="{{ __('select tags') }}">
            </div>
        </div>
    </div>

    <div class="setup-wrapper-contents-item">
        @if(!empty($categoriesWithSkills))
            @foreach($categoriesWithSkills as $category)
                <h4 class="mt-3 mb-2">{{ $category['category_name'] }}</h4>

                @foreach($category['subcategories'] as $subId => $sub)
                    <div class="subcategory-box mb-4 p-3 rounded shadow-sm border">
                        <div class="d-flex align-items-center mb-2">
                            <input type="checkbox" id="sub-{{ $subId }}" class="subcategory-checkbox me-2" data-id="{{ $subId }}"
                                data-name="{{ $sub['subcategory_name'] }}" {{ $sub['selected'] ? 'checked' : '' }}>

                            <label for="sub-{{ $subId }}" class="subcategory-label fw-bold mb-0">
                                {{ $sub['subcategory_name'] }}
                            </label>
                        </div>

                        @if(!empty($sub['skills']))
                            <ul class="setup-wrapper-work-list d-flex flex-wrap gap-2 skill-list" id="skill-list-{{ $subId }}">
                                @foreach($sub['skills'] as $index => $skill)
                                    @if(!in_array($skill, $skillsArray))
                                        <li class="skill-tag choose_skill {{ $index >= 10 ? 'd-none' : '' }}">
                                          
                                            {!! $skill !!}
                                        </li>
                                    @endif
                                @endforeach
                            </ul>

                            @if(count($sub['skills']) > 10)
                                <button class="show-more-skills p-0" data-target="skill-list-{{ $subId }}" data-visible="10">
                                    Show More
                                </button>
                            @endif
                        @endif
                    </div>
                @endforeach
            @endforeach
        @endif
    </div>

</div>
<!-- Setup Skills Ends -->

@php
    $skills = \App\Models\UserSkill::select('skill')
        ->where('user_id', Auth::guard('web')->user()->id)
        ->first()->skill ?? '';
    $array_skill = explode(",", $skills);
@endphp

<script>
    document.addEventListener("DOMContentLoaded", function () {
        initSkillTags(@json($array_skill));
    });
</script>