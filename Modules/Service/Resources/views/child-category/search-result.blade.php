<x-validation.error/>
<table class="DataTable_activation">
    <thead>
    <tr>
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
        <th>{{__('ID')}}</th>
        <th>{{__('Skill')}}</th>
        <!-- <th>{{__('Meta Title')}}</th>
        <th>{{__('Meta Description')}}</th> -->
        <th>{{__('Short Description')}}</th>
        <th>{{__('Sub Category')}}</th>
        <th>{{__('Category')}}</th>
        <th>{{__('Status')}}</th>
        <th>{{__('Image')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_child_categories as $child_cat)
        <tr>
            <td> <x-bulk-action.bulk-delete-checkbox :id="$child_cat->id"/> </td>
            <td>{{ $child_cat->id }}</td>
            <td>{{ $child_cat->name }}</td>
            <!-- <td>{{ $child_cat->meta_title }}</td>
            <td>{{ $child_cat->meta_description }}</td> -->
            <td>{{ $child_cat->short_description }}</td>
            <td>{{ optional($child_cat->sub_category)->sub_category }}</td>
            <td>{{ optional(optional($child_cat->sub_category)->category)->category }}</td>
            <td><x-status.table.active-inactive :status="$child_cat->status"/></td>
            <td>
                <span class="img_100">
                    {!! render_image_markup_by_attachment_id($child_cat->image) !!}
                </span>
                @php $child_cat_img = get_attachment_image_by_id($child_cat->image,null,true); @endphp
                @if (!empty($child_cat_img))
                    @php  $img_url = $child_cat_img['img_url']; @endphp
                @endif
            </td>
            <td>
                <x-status.table.select-action :title="__('Select Action')"/>
                <ul class="dropdown-menu status_dropdown__list">
                    @can('child-category-edit')
                    <li class="status_dropdown__item">
                        <a
                            class="btn dropdown-item status_dropdown__list__link edit_child_category_modal"
                            data-bs-toggle="modal"
                            data-bs-target="#editChildCategoryModal"
                            data-id="{{ $child_cat->id }}"
                            data-img_id="{{ $child_cat->image }}"
                            data-img_url="{{ $img_url }}"
                            data-name="{{ $child_cat->name }}"
                            data-meta_title="{{ $child_cat->meta_title }}"
                            data-meta_description="{{ $child_cat->meta_description }}"
                            data-short_description="{{ $child_cat->short_description }}"
                            data-slug="{{ $child_cat->slug }}"
                            data-sub_category="{{ $child_cat->sub_category_id }}">
                            {{ __('Edit Skill') }}
                        </a>
                    </li>
                    @endcan
                    @can('child-category-delete')
                    <li class="status_dropdown__item"><x-popup.delete-popup :title="__('Delete Skill')" :url="route('admin.child-category.delete',$child_cat->id)"/></li>
                    @endcan
                    @can('child-category-status-change')
                    <li class="status_dropdown__item"><x-status.table.status-change :title="__('Change Status')" :url="route('admin.child-category.status',$child_cat->id)"/></li>
                    @endcan
                </ul>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_child_categories"/>
