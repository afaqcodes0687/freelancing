<div class="single-input mt-3">
    <label class="label-title">{{ $title }}</label>
    <select name="{{ $name ?? '' }}" id="{{ $id ?? '' }}" class="{{ $class ?? '' }}">
        <option value="">{{ __('Select Sub Category') }}</option>
        @foreach($allSubCategories = \Modules\Service\Entities\SubCategory::all_sub_categories() as $data)
            <option value="{{ $data->id }}">{{ $data->sub_category }}</option>
        @endforeach
    </select>
</div>
