 <style>
   
    .category-title {
        font-weight: 600;
        font-size: 1rem;
        color: #333;
        margin-bottom: 10px;
    }
    .skills-group { margin-bottom: 12px; }

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
    .choose-skill-item:hover { background: #e2e6ea; }
    .choose-skill-item.active {
        background: #309400;
        color: #fff;
        border-color: #309400;
    }
    .selected-skill-chip {
        display: inline-block;
        background: #309400;
        color: #fff;
        padding: 5px 10px;
        margin: 3px;
        border-radius: 20px;
        font-size: 0.85rem;
        cursor: pointer;
    }
    .selected-skill-chip i {
        margin-left: 5px;
        font-size: 0.75rem;
        cursor: pointer;
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
</style>
<!-- About Job Start -->
<div class="setup-wrapper-contents active">
    <div class="setup-wrapper-contents-item">
        <div class="setup-bank-form">
            <x-form.text :title="__('Job Title')" :type="'text'" :id="'title'" :name="'title'" :divClass="'mb-0'" :class="'form--control'" :value="$job_details->title ?? old('title')" :placeholder="__('e.g. I need  landing page')" />
            <span id="job_title_char_length_check"></span>

            <x-form.text :title="__('Slug')" :type="'text'" :id="'slug'" :name="'slug'" :value="$job_details->slug ?? old('slug')" :divClass="'mb-0'" :class="'form--control d-none'" :labelClass="'d-none display_label_title'" :placeholder="__('Slug')" />

           <div class="mb-1">
               <strong>{{ __('Slug:') }}</strong>
               <span class="full-slug-show"></span>
               <span class="edit_job_slug"><i class="fas fa-edit"></i></span>
           </div>

            <div class="single-input mt-3">
                <label class="label-title">{{ __('Select Category') }}</label>
                <select name="category" id="category" class="form-control category_select2">
                    @foreach(\Modules\Service\Entities\Category::all_categories() as $data)
                        <option value="{{ $data->id }}" @if($job_details->category == $data->id) selected @endif>{{ $data->category }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Select Subcategory (keep your current select) -->
            <div class="single-input">
                <label class="label-title">{{ __('Select Subcategory') }}</label>
                <select name="subcategory[]" id="subcategory" class="form-control get_subcategory subcategory_select2" multiple>
                    @foreach ($get_sub_categories_from_job_category as $subcategory)
                        <option value="{{ $subcategory->id }}"
                            @if( in_array($subcategory->id, $job_details->job_sub_categories->pluck('id')->toArray() ) ) selected @endif>
                            {{ $subcategory->sub_category }}
                        </option>
                    @endforeach
                </select>
                <span id="subcategory_info"></span>
            </div>
            <!-- Skills Wrapper -->
           <div id="selected-skills-wrapper" class="mb-3">
                <label class="label-title">{{ __('Selected Skills') }}</label>
                <div id="selected-skills-list"></div>
            </div>

            <!-- Skills Section -->
            <label class="label-title mt-3">{{ __('Select Skills') }}</label>
            <div id="skills-wrapper"></div>

            <div id="load-more-wrapper" class="mt-2" style="display:none;">
                <button type="button" id="load-more-btn" class="show-more-skills btn btn-link p-0" data-batch="10">
                    {{ __('More Skills') }}
                </button>
            </div>

            <div class="single-input">
                <label class="label-title">{{ __('Job duration') }}</label>
                <select name="duration" id="duration" class="form-control">
                    <option value="1 Days" @if( $job_details->duration == '1 Days') selected @endif>{{ __('1 Days') }}</option>
                    <option value="2 Days" @if( $job_details->duration == '2 Days') selected @endif>{{ __('2 Days') }}</option>
                    <option value="3 Days" @if( $job_details->duration == '3 Days') selected @endif>{{ __('3 Days') }}</option>
                    <option value="less than a week" @if( $job_details->duration == 'less than a week') selected @endif>{{ __('Less than a Week') }}</option>
                    <option value="less than a month" @if( $job_details->duration == 'less than a month') selected @endif>{{ __('Less than a month') }}</option>
                    <option value="less than 2 month" @if( $job_details->duration == 'less than 2 month') selected @endif>{{ __('Less than 2 month') }}</option>
                    <option value="less than 3 month" @if( $job_details->duration == 'less than 3 month') selected @endif>{{ __('Less than 3 month') }}</option>
                    <option value="More than 3 month" @if( $job_details->duration == 'More than 3 month') selected @endif>{{ __('More than 3 month') }}</option>
                </select>
            </div>
            <div class="single-input">
                <label class="label-title">{{ __('Choose experience level') }}</label>
                <select name="level" id="level" class="form-control">
                    <option value="junior" @if($job_details->level == 'junior') selected @endif>{{ __('Junior') }}</option>
                    <option value="midLevel" @if($job_details->level == 'midLevel') selected @endif>{{ __('MidLevel') }}</option>
                    <option value="senior" @if($job_details->level == 'senior') selected @endif>{{ __('Senior') }}</option>
                    <option value="not mandatory" @if($job_details->level == 'not mandatory') selected @endif>{{ __('Not Mandatory') }}</option>
                </select>
            </div>
            <x-form.summernote
                :title="__('Write a job description')"
                :name="'description'"
                :id="'description'"
                :rows="'10'" :cols="30"
                :value="$job_details->description ?? old('description')"
                :class="'description '"
            />
            <span id="job_description_char_length_check"></span>

            <!-- <x-form.text :title="__('Meta Title - ideal length is 50–60 characters (optional)')" :type="'text'" :id="'meta_title'" :name="'meta_title'" :divClass="'mb-0'" :class="'form--control'" :value="$job_details->meta_title ?? old('meta_title')" :placeholder="__('Enter meta title')" />

            <div class="single-input">
                <label class="label-title">{{ __('Meta Description - ideal length is 150-160 characters (optional)') }}</label>
                <textarea name="meta_description" id="meta_description" class="form-message" cols="30" rows="3" placeholder="{{ __('Enter meta description') }}">{{ $job_details->meta_description ?? '' }}</textarea>
            </div> -->

        </div>
    </div>
</div>
<!-- About Job Ends -->

