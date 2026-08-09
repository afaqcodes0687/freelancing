@extends('backend.layout.master')
@section('site-title')
    {{__('All Scheduled Meetings')}}
@endsection
@section('style')
<style>
    .text-brand-green { color: #309400 !important; }
    .badge-scheduled { background-color: #309400; color: white; }
    .participant-info .badge { font-size: 10px; padding: 3px 8px; }
    .custom_table.style-04 table tbody td { vertical-align: middle; }
</style>
@endsection
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title text-brand-green">
                                <i class="fa-solid fa-video me-2"></i>{{ __('All Scheduled Meetings') }}
                            </h4>
                        </div>
                        
                        <div class="search_delete_wrapper d-flex justify-content-end">
                            <x-search.search-in-table :id="'string_search'"/>
                        </div>

                        <div class="customMarkup__single__inner mt-4">
                            <div class="custom_table style-04 search_result">
                                @include('meeting::admin.search-result')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        (function($){
            "use strict";

            $(document).ready(function(){
                // Search
                $(document).on('keyup','#string_search',function(){
                    let string_search = $(this).val();
                    $.ajax({
                        url: "{{ route('admin.meeting.search') }}",
                        method: 'GET',
                        data: {string_search:string_search},
                        success: function (res) {
                            if(res.status == 'nothing'){
                                $('.search_result').html('<h4 class="text-center text-danger">{{ __('Nothing Found') }}</h4>');
                            }else{
                                $('.search_result').html(res);
                            }
                        }
                    });
                });

                // Pagination
                $(document).on('click', '.pagination a', function(e){
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    let string_search = $('#string_search').val();
                    $.ajax({
                        url: "{{ route('admin.meeting.paginate').'?page=' }}" + page,
                        method: 'GET',
                        data: {string_search:string_search},
                        success: function (res) {
                            $('.search_result').html(res);
                        }
                    });
                });
            });
        })(jQuery);
    </script>
@endsection
