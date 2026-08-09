<!-- Child Category Edit Modal -->
<div class="modal fade" id="editChildCategoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Edit Skill') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.child-category.edit')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_child_category_id" id="edit_child_category_id" value="">
                <div class="modal-body">
                    <x-form.text :title="__('Skill')" :type="__('text')" :name="'edit_name'" :id="'edit_name'" :value="''" :placeholder="__('Enter Skill')"/>
                    <x-form.slug :name="'edit_slug'" :id="'edit_slug'"/>
                    <x-form.text :title="__('Meta Title - ideal length is 50–60 characters')" :type="__('text')" :name="'edit_meta_title'" :id="'edit_meta_title'" :value="''" :placeholder="__('Enter meta title')"/>
                    <x-form.textarea :title="__('Meta Description - ideal length is 150-160 characters')" :name="'edit_meta_description'" :id="'edit_meta_description'" :value="''" :placeholder="__('Enter meta description')"/>
                    <x-form.textarea :title="__('Short Description')" :name="'edit_short_description'" :id="'edit_short_description'" :value="old('short_description', '')" :placeholder="__('Max 190 character')"/>

                    {{-- Category Filter (not submitted — only used to cascade subcategory dropdown) --}}
                    <div class="single-input mt-3">
                        <label class="label-title">{{ __('Filter by Category') }}</label>
                        <select id="edit_category_filter" class="form-control edit_category_filter_select2">
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
                        <select name="edit_sub_category" id="edit_sub_category" class="form-control subcategory_select22">
                            <option value="">{{ __('Select Sub Category') }}</option>
                            @foreach(\Modules\Service\Entities\SubCategory::all_sub_categories() as $data)
                                <option value="{{ $data->id }}">{{ $data->sub_category }}</option>
                            @endforeach
                        </select>
                        <span id="edit_subcategory_info"></span>
                    </div>

                    <x-backend.image :title="__('')" :name="'image'" :dimentions="__('3000x300(optional)')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mt-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <x-btn.submit :title="__('Update')" :class="'btn btn-primary mt-4 pr-4 pl-4 update_child_category'" />
                </div>
            </form>
        </div>
    </div>
</div>
