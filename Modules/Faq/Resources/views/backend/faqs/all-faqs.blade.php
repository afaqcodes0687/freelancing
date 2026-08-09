@extends('backend.layout.master')
@section('title', __('All FAQs'))
@section('style')
    <x-select2.select2-css/>
@endsection
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('All FAQs') }}</h4>
                            <div class="d-flex align-items-center gap-3">
                                <x-search.search-in-table :id="'string_search'" />
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                                    <i class="fas fa-plus me-1"></i> {{ __('Add FAQ') }}
                                </button>
                            </div>
                        </div>
                        <div class="customMarkup__single__inner mt-4">
                            <div class="custom_table style-04 search_result">
                                @include('faq::backend.faqs.search-result')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Add FAQ Modal ── --}}
    <div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFaqLabel">{{ __('Add New FAQ') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.faq.all') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <x-validation.error/>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Category') }} <small class="text-muted">({{ __('Optional — leave blank for global FAQ') }})</small></label>
                            <select name="category_id" class="form-control add-faq-category-select2">
                                <option value="">— {{ __('Global (All Categories)') }} —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Question') }} <span class="text-danger">*</span></label>
                            <input type="text" name="question" class="form-control" placeholder="{{ __('Enter FAQ question') }}" value="{{ old('question') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Answer') }}</label>
                            <textarea name="answer" class="form-control" rows="5" placeholder="{{ __('Enter FAQ answer') }}">{{ old('answer') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save FAQ') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Edit FAQ Modal ── --}}
    <div class="modal fade" id="editFaqModal" tabindex="-1" aria-labelledby="editFaqLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFaqLabel">{{ __('Edit FAQ') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.faq.edit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="edit_faq_id" id="edit_faq_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Category') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <select name="edit_category_id" id="edit_category_id" class="form-control edit-faq-category-select2">
                                <option value="">— {{ __('Global (All Categories)') }} —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Question') }} <span class="text-danger">*</span></label>
                            <input type="text" name="edit_question" id="edit_question" class="form-control" placeholder="{{ __('Enter FAQ question') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Answer') }}</label>
                            <textarea name="edit_answer" id="edit_answer" class="form-control" rows="5" placeholder="{{ __('Enter FAQ answer') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update FAQ') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <x-sweet-alert.sweet-alert2-js/>
    <x-select2.select2-js/>
    @include('faq::backend.faqs.faq-js')
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                $('.add-faq-category-select2').select2({ dropdownParent: $('#addFaqModal') });
                $('.edit-faq-category-select2').select2({ dropdownParent: $('#editFaqModal') });

                // Open edit modal and populate fields
                $(document).on('click', '.edit_faq_btn', function(){
                    var id          = $(this).data('id');
                    var question    = $(this).data('question');
                    var answer      = $(this).data('answer');
                    var category_id = $(this).data('category_id');

                    $('#edit_faq_id').val(id);
                    $('#edit_question').val(question);
                    $('#edit_answer').val(answer);
                    $('#edit_category_id').val(category_id ? category_id : '').trigger('change');
                    $('#editFaqModal').modal('show');
                });
            });
        })(jQuery);
    </script>
@endsection
