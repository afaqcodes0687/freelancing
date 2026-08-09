@extends('backend.layout.master')
@section('title', __('All Skills'))
@section('style')
    <x-select2.select2-css />
    <x-media.css/>
@endsection
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <x-notice.general-notice :description="__('Notice: A skill can be deleted only if it has no dependencies. It can be removed if it is not associated with any jobs or projects.')" />
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('All Skills') }}</h4>
                            @can('child-category-add')
                            <x-btn.add-modal :title="__('Add Skill')" />
                            @endcan
                        </div>
                        <div class="search_delete_wrapper">
                            @can('child-category-bulk-delete')
                            <x-bulk-action.bulk-action />
                            @endcan
                            <x-search.search-in-table :id="'string_search'" />
                        </div>
                        <div class="customMarkup__single__inner mt-4">
                            <!-- Table Start -->
                            <div class="custom_table style-04 search_result">
                                @include('service::child-category.search-result')
                            </div>
                            <!-- Table End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('service::child-category.add-modal')
    @include('service::child-category.edit-modal')
    <x-media.markup/>
@endsection

@section('script')
    <x-media.js/>
    <x-sweet-alert.sweet-alert2-js />
    <x-select2.select2-js />
    <x-bulk-action.bulk-delete-js :url="route('admin.child-category.delete.bulk.action')"/>
    @include('service::child-category.child-category-js')
@endsection
