<style>
    .choose-skill-item {
        display: inline-block;
        padding: 6px 12px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 20px;
        cursor: pointer;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }

    .choose-skill-item:hover {
        background: #e2e6ea;
    }

    .choose-skill-item.active {
        background: #309400;
        color: white;
        border-color: #309400;
    }

    .category-title {
        font-weight: 600;
        font-size: 1rem;
        color: #333;
        margin-bottom: 10px;
    }

    .skill-chip {
        display: inline-block;
        background: #309400;
        color: white;
        padding: 5px 10px;
        margin: 3px;
        border-radius: 15px;
        font-size: 0.9rem;
    }

    .skill-chip .remove-skill {
        margin-left: 8px;
        cursor: pointer;
        color: #fff;
        font-weight: bold;
    }


    .hidden-skill {
        display: none !important;
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

    .show-more-skills:hover {
        text-decoration: none;
        color: #267000;
    }
    .select-loader {
    display: inline-block;
    margin-left: 10px;
    font-size: 14px;
    color: #309400;
}

</style>

<!-- About Job Start -->
<div class="setup-wrapper-contents active">
    <div class="setup-wrapper-contents-item">
        <div class="setup-bank-form">
            <x-form.text :title="__('Job Title')" :type="'text'" :id="'title'" :name="'title'" :divClass="'mb-0'"
                :class="'form--control'" :value="old('title')" :placeholder="__('e.g. I need  landing page')" />
            <span id="job_title_char_length_check"></span>

            <x-form.text :title="__('Slug')" :type="'text'" :id="'slug'" :name="'slug'" :value="old('slug')"
                :divClass="'mb-0'" :class="'form--control d-none'" :labelClass="'d-none display_label_title'"
                :placeholder="__('Slug')" />
            <div class="mb-0">

                <strong>{{ __('Slug:') }}</strong>
                <span class="full-slug-show"></span>
                <span class="edit_job_slug"><i class="fas fa-edit"></i></span>
            </div>

            <x-form.category-dropdown :title="__('Select Category')" :name="'category'" :id="'category'"
                :class="'form-control category_select2'" />
            <div class="single-input">
                <label class="label-title">{{ __('Select Subcategory') }}</label>
                <select name="subcategory[]" id="subcategory" class="form-control get_subcategory subcategory_select2" multiple></select>
                <span id="subcategory_info"></span>
                <span id="subcategory_loader" class="select-loader" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('Loading...') }}
                </span>
            </div>


            <label class="label-title mt-3">{{ __('Selected Skills') }}</label>
            <div id="selected-skills" class="mb-3"></div>

            <label class="label-title mt-3" for="skill">{{ __('Select Skills') }}</label>
            <div id="skills-wrapper"></div>

            <div class="single-input">
                <label class="label-title">{{ __('Job duration') }}</label>
                <select name="duration" id="duration" class="form-control">
                    <option value="">{{ __('Select Duration') }}</option>
                    <option value="1 Days">{{ __('1 Days') }}</option>
                    <option value="1 Days">{{ __('2 Days') }}</option>
                    <option value="1 Days">{{ __('3 Days') }}</option>
                    <option value="less than a week">{{ __('Less than a Week') }}</option>
                    <option value="less than a month">{{ __('Less than a month') }}</option>
                    <option value="less than 2 month">{{ __('Less than 2 month') }}</option>
                    <option value="less than 3 month">{{ __('Less than 3 month') }}</option>
                    <option value="More than 3 month">{{ __('More than 3 month') }}</option>
                </select>
            </div>
            <x-form.experience-level-dropdown :title="__('Select Experience Level')" :class="'form-control'"
                :name="'level'" :id="'level'" />
            <x-form.summernote :title="__('Write a job description')" :name="'description'" :id="'description'"
                :rows="'10'" :cols="30" :value="old('description')" :class="'description '" />
            <span id="job_description_char_length_check"></span>

            {{-- <x-form.text :title="__('Meta Title - ideal length is 50–60 characters (optional)')" :type="'text'"
                :id="'meta_title'" :name="'meta_title'" :divClass="'mb-0'" :class="'form--control'"
                :value="old('meta_title')" :placeholder="__('Enter meta title')" />--}}

            {{-- <div class="single-input">--}}
                {{-- <label class="label-title">{{ __('Meta Description - ideal length is 150-160 characters
                    (optional)') }}</label>--}}
                {{-- <textarea name="meta_description" id="meta_description" class="form-message" cols="30" rows="3"
                    placeholder="{{ __('Enter meta description') }}"></textarea>--}}
                {{-- </div>--}}

        </div>
    </div>
</div>
<!-- About Job Ends -->

@section('scripts')
    <script src="{{ asset('assets/js/skills-show-more.js') }}"></script>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Delegated event (dynamic content ke liye)
        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('show-more-skills')) {
                const btn = e.target;
                const container = btn.closest('.subcategory-skills');
                if (!container) return;

                const hiddenItems = Array.from(container.querySelectorAll('.hidden-skill'));
                const batch = parseInt(btn.dataset.batch) || 10;

                // Reveal batch items
                for (let i = 0; i < batch && i < hiddenItems.length; i++) {
                    hiddenItems[i].classList.remove('hidden-skill');
                }

                // Agar aur hidden items na bachi ho to button remove
                if (container.querySelectorAll('.hidden-skill').length === 0) {
                    btn.remove();
                }
            }
        });
    });

</script>
