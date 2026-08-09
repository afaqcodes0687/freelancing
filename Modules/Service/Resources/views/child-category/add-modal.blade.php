<!-- Child Category Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Add New Skill') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.child-category.all')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <x-validation.error/>
                    <x-form.text :title="__('Skill')" :type="__('text')" :name="'name'" :id="'name'" :value="old('name', '')" :placeholder="__('Enter Skill')"/>
                    <x-form.slug :name="'slug'" :id="'slug'"/>
                    <x-form.text :title="__('Meta Title - ideal length is 50–60 characters')" :type="__('text')" :name="'meta_title'" :id="'meta_title'" :value="old('meta_title', '')" :placeholder="__('Enter meta title')"/>
                    <x-form.textarea :title="__('Meta Description - ideal length is 150–160 characters')" :name="'meta_description'" :id="'meta_description'" :value="old('meta_description', '')" :placeholder="__('Enter meta description')"/>
                    <x-form.textarea :title="__('Short Description')" :name="'short_description'" :id="'short_description'" :value="old('short_description', '')" :placeholder="__('Max 190 character')"/>

                    {{-- Category Filter (not submitted — only used to cascade subcategory dropdown) --}}
                    <div class="single-input mt-3">
                        <label class="label-title">{{ __('Filter by Category') }}</label>
                        <select id="add_category_filter" class="form-control add_category_filter_select2">
                            <option value="">{{ __('— All Categories —') }}</option>
                            @foreach(\Modules\Service\Entities\Category::all_categories() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ __('Select a category to filter the subcategory list below') }}</small>
                    </div>

                    {{-- Subcategory dropdown (populated via AJAX based on category selection) --}}
                    <div class="single-input mt-3">
                        <label class="label-title">{{ __('Select Sub Category') }} <span class="text-danger">*</span></label>
                        <select name="sub_category" id="add_sub_category" class="form-control subcategory_select2">
                            <option value="">{{ __('Select Sub Category') }}</option>
                            @foreach(\Modules\Service\Entities\SubCategory::all_sub_categories() as $data)
                                <option value="{{ $data->id }}">{{ $data->sub_category }}</option>
                            @endforeach
                        </select>
                        <span id="add_subcategory_info"></span>
                    </div>

                    <x-form.active-inactive :title="__('Select Status')" :info="__('If you select inactive the services will off for the skill')" />
                    <x-backend.image :title="__('')" :name="'image'" :dimentions="__('3000x300(optional)')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mt-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <x-btn.submit :title="__('Save')" :class="'btn btn-primary mt-4 pr-4 pl-4 add_child_category'" />
                </div>
            </form>
        </div>
    </div>
</div>
