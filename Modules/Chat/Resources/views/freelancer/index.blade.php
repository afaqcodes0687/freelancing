@extends('frontend.layout.master')
@section('site_title',__('Live Chat'))

@section('style')
    <x-summernote.summernote-css />
    <style>
        .disabled-link {
            background-color: #ccc !important;
            pointer-events: none;
            cursor: default;
        }
        @keyframes pulse-opacity {
            0% { opacity: 0.5; }
            50% { opacity: 0.8; }
            100% { opacity: 0.5; }
        }
        .optimistic-message {
            animation: pulse-opacity 1.5s infinite ease-in-out;
        }
        .chat-wrapper-details-inner-chat-contents-para {
            margin-bottom: 0 !important;
        }
        .btn-schedule-meeting {
            background-color: #309400 !important;
            color: #fff !important;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(48, 148, 0, 0.2);
        }
        .btn-schedule-meeting:hover {
            background-color: #246e00 !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(48, 148, 0, 0.3);
            color: #fff !important;
        }
        .btn-schedule-meeting.disabled-link {
            background-color: #ccc !important;
            color: #666 !important;
            cursor: not-allowed !important;
            box-shadow: none !important;
            transform: none !important;
        }
        .btn-schedule-meeting.disabled-link:hover {
            background-color: #ccc !important;
            transform: none !important;
            box-shadow: none !important;
        }
        .btn-schedule-meeting i {
            font-size: 1.1rem;
        }
        .chat-header-profile-trigger {
            position: relative;
        }
        .chat-header-profile-btn {
            background: transparent;
            border: none;
            padding: 6px 10px 6px 4px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .chat-header-profile-btn:hover,
        .chat-header-profile-btn[aria-expanded="true"] {
            background-color: rgba(0, 0, 0, 0.04);
        }
        .chat-header-chevron {
            margin-left: 8px;
            color: #6b7280;
            font-size: 0.75rem;
            transition: transform 0.2s ease;
        }
        .chat-header-profile-btn[aria-expanded="true"] .chat-header-chevron {
            transform: rotate(180deg);
        }
        .chat-header-dropdown-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            min-width: 220px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            padding: 6px 0;
            z-index: 1050;
        }
        .chat-header-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 16px;
            border: none;
            background: transparent;
            color: #374151;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .chat-header-dropdown-item:hover {
            background-color: #f3f4f6;
            color: #111827;
        }
        .chat-header-dropdown-item i {
            width: 18px;
            text-align: center;
            color: #6b7280;
            font-size: 0.95rem;
        }
        .chat-header-dropdown-item .fa-solid.fa-star {
            color: #f59e0b;
        }
        .chat-list-favorite-star {
            color: #f59e0b;
            font-size: 0.7rem;
        }
        #chat_header,
        .chat-wrapper-details-header {
            overflow: visible;
        }
        .chat-empty-state {
            list-style: none;
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }
        .chat-empty-state p {
            margin: 0;
        }
        .chat-filter-buttons {
            display: flex;
            gap: 8px;
            padding: 14px 0;
        }
        .btn-filter {
            flex-shrink: 0;
            padding: 9px 22px;
            border-radius: 22px;
            background: transparent;
            border: 1.5px solid var(--border-color);
            color: var(--paragraph-color);
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s ease;
            white-space: nowrap;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-filter:hover {
            border-color: var(--main-color-one);
            color: var(--main-color-one);
            background: rgba(var(--main-color-one-rgb, 0,0,0), 0.04);
        }
        .btn-filter.active {
            background: var(--main-color-one);
            border-color: var(--main-color-one);
            color: #fff;
        }
        .btn-filter.active:hover {
            color: #fff;
        }
        .btn-filter i {
            margin-right: 5px;
        }

        .chat-wrapper-contact-list {
            max-height: 620px;
        }
        .chat-wrapper-contact-list::-webkit-scrollbar {
            width: 5px;
        }
        .chat-wrapper-contact-list::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .chat-wrapper-contact-list::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 5px;
        }
    </style>
@endsection

@section('content')
    <main>
        <!-- Profile Details area Starts -->
        <div class="responsive-overlay"></div>
        <div class="profile-area pat-20 pab-20 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @if($freelancer_chat_list->count() > 0)
                        <div class="col-lg-12">
                        <div class="chat-wrapper">
                            <div class="chat-wrapper-flex">
                                <div class="chat-sidebar chatText d-lg-none">
                                    {{__('View Chat List')}}
                                </div>
                                <div class="chat-wrapper-contact">
                                    <div class="chat-wrapper-contact-close">
                                        <div class="close-chat d-lg-none"> <i class="fas fa-times"></i> </div>
                                        <div class="chat-filter-buttons mb-3 px-3">
                                            <button type="button" class="btn btn-sm btn-filter active" id="filterAllBtn">
                                                <i class="fa-solid fa-comments"></i> {{ __('All Chats') }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-filter" id="filterFavoritesBtn">
                                                <i class="fa-solid fa-star"></i> {{ __('Favorites') }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-filter" id="filterArchivedBtn">
                                                <i class="fa-solid fa-box-archive"></i> {{ __('Archived') }}
                                            </button>
                                        </div>
                                        <ul class="chat-wrapper-contact-list" id="chatContactList">
                                            @foreach($freelancer_chat_list as $freelancer_chat)
                                                <x-chat::freelancer.client-list :freelancerChat="$freelancer_chat" />
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="chat-wrapper-details">

                                    <div class="chat-wrapper-details-header d-none flex-between" id="chat_header">

                                    </div>

                                    <div class="chat-wrapper-details-inner client-chat-body" id="chat_body" style="background: #f8f8f8;">

                                    </div>

                                    <div class="chat-wrapper-details-footer profile-border-top d-none" id="freelancer-message-footer">
                                        <div class="chat-wrapper-details-footer-form custom-form">
                                            <form action="#">
                                                <div class="single-input">
                                                    @if(moduleExists('SecurityManage'))
                                                        @if(Auth::guard('web')->user()->freeze_chat == 'freeze')
                                                            <textarea name="message" id="message" class="form--control form-message" placeholder="Write your message" disabled></textarea>
                                                        @else
                                                            <textarea name="message" id="message" class="form--control form-message" placeholder="Write your message"></textarea>
                                                        @endif
                                                    @else
                                                        <textarea name="message" id="message" class="form--control form-message" placeholder="Write your message"></textarea>
                                                    @endif
                                                </div>
                                            </form>
                                            <div class="chat-wrapper-details-footer-btn flex-btn justify-content-end mt-3">
                                                @if(moduleExists('SecurityManage'))
                                                    @if(Auth::guard('web')->user()->freeze_chat == 'freeze')
                                                        <div class="position-relative">
                                                            <input class="photo-uploaded-file inputTag" id="message-file" type="file" disabled>
                                                            <span class="show_uploaded_file"></span>
                                                            <span class="dropMedia__file disabled-link" id="uploadImage">
                                                                <i class="fa-solid fa-paperclip"></i> {{ __("Attach Files") }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="position-relative">
                                                            <input class="photo-uploaded-file inputTag" id="message-file" type="file">
                                                            <span class="show_uploaded_file"></span>
                                                            <span class="dropMedia__file" id="uploadImage">
                                                                <i class="fa-solid fa-paperclip"></i> {{ __("Attach Files") }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="position-relative">
                                                        <input class="photo-uploaded-file inputTag" id="message-file" type="file">
                                                        <span class="show_uploaded_file"></span>
                                                        <span class="dropMedia__file" id="uploadImage">
                                                            <i class="fa-solid fa-paperclip"></i> {{ __("Attach Files") }}
                                                        </span>
                                                    </div>
                                                @endif
                                                    <div class="position-relative ms-2">
                                                        @if(moduleExists('SecurityManage'))
                                                            @if(Auth::guard('web')->user()->freeze_chat == 'freeze')
                                                                <span class="btn-schedule-meeting disabled-link" id="scheduleMeetingBtn">
                                                                    <i class="fa-solid fa-video"></i> {{ __("Schedule Meeting") }}
                                                                </span>
                                                            @else
                                                                <span class="btn-schedule-meeting" id="scheduleMeetingBtn">
                                                                    <i class="fa-solid fa-video"></i> {{ __("Schedule Meeting") }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="btn-schedule-meeting" id="scheduleMeetingBtn">
                                                                <i class="fa-solid fa-video"></i> {{ __("Schedule Meeting") }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @if(moduleExists('SecurityManage'))
                                                    @if(Auth::guard('web')->user()->freeze_chat == 'freeze')
                                                        <a href="javascript:void(0)" class="btn-profile btn-bg-1 @if(Auth::guard('web')->user()->freeze_chat == 'freeze') disabled-link @endif">{{ __('Send Message') }}</a>
                                                    @else
                                                        <a href="javascript:void(0)" class="btn-profile btn-bg-1" id="freelancer-send-message-to-client">{{ __('Send Message') }}</a>
                                                    @endif
                                                @else
                                                    <a href="javascript:void(0)" class="btn-profile btn-bg-1" id="freelancer-send-message-to-client">{{ __('Send Message') }}</a>
                                                @endif
                                            </div>
                                            <div class="chat-wrapper-details-footer-btn-right">
                                                <small>{{ __('Supported file: jpeg,jpg,png,pdf,gif,docx,zip') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                        <div class="col-lg-12">
                            <div class="chat-wrapper">

                                <div class="chat-wrapper-flex">
                                    <div class="chat-sidebar d-lg-none">
                                        <i class="fas fa-bars"></i>
                                    </div>

                                    <div class="chat-wrapper-contact">
                                        <div class="chat-wrapper-contact-close">
                                            <div class="close-chat d-lg-none"> <i class="fas fa-times"></i> </div>
                                            <ul class="chat-wrapper-contact-list">
                                                <h4 class="text-danger text-center mt-5">{{ __('No Contacts Yet.') }}</h4>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="chat-wrapper-details"> </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Profile Details area end -->

        <!-- End Conversation Modal -->
        <div class="modal fade" id="endConversationModal" tabindex="-1" aria-labelledby="endConversationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow: hidden;">
                    <div class="modal-header" style="background: #fff; border-bottom: 1px solid #f3f4f6; padding: 20px 24px 16px;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #fef2f2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fa-solid fa-circle-xmark" style="color: #ef4444; font-size: 1.2rem;"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="endConversationModalLabel" style="font-size: 1rem; font-weight: 700; color: #111827;">
                                {{ __('End Conversation With') }} <span id="endConversationName" style="color: #ef4444;"></span>
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 20px 24px;">
                        <p style="color: #6b7280; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                            {{ __('The Conversation will be moved to hidden and archived. You and') }}
                            <strong id="endConversationNameBody" style="color: #374151;"></strong>
                            {{ __('will not be able to message each other but may still receive messages in other groups and rooms.') }}
                        </p>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f3f4f6; padding: 16px 24px; gap: 10px;">
                        <input type="hidden" id="endConversationChatId">
                        <button type="button" class="btn" data-bs-dismiss="modal"
                            style="padding: 9px 20px; border-radius: 8px; border: 1.5px solid #d1d5db; color: #374151; font-weight: 600; font-size: 0.88rem; background: #fff; transition: all 0.2s ease;">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" id="confirmEndConversationBtn"
                            style="padding: 9px 20px; border-radius: 8px; border: none; background: #ef4444; color: #fff; font-weight: 600; font-size: 0.88rem; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-regular fa-circle-xmark"></i>
                            {{ __('End Conversation') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Conversation Modal End -->

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('freelancer.offer.send') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="client_id" id="client_id">
                    <input type="hidden" name="pay_by_milestone" id="pay_by_milestone">
                    <input type="hidden" name="pay_at_once" id="pay_at_once" value="pay-at-once">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>{{ __('Send Offer') }}</h3>
                        </div>
                        <div class="modal-body">
                            <x-notice.general-notice
                                :description="__('Notice: Please discuss project requirements and budget with the client before sending an offer to prevent misunderstandings.')"
                                :description1="__('Notice: If pay by milestone you can skip description section')"
                            />
                            <div class="offer_total_price mt-5 setup-bank-form-item setup-bank-form-item-icon">
                                <labe><strong>{{ __('Offer Price') }}</strong></labe>
                                <input type="number" class="form-control" name="offer_price" id="offer_price" placeholder="{{ __('Enter Price') }}">
                                <span class="input-icon">{{ get_static_option('site_global_currency') ?? '' }}</span>
                            </div>
                            <br>

                            <div class="d-flex flex-wrap gap-4 mb-4">

                                <div id="pay_at_once_btn" class="identity-verifying-list active">
                                    <strong>{{ __('Pay at Once') }}</strong>
                                    <span>{{ __('You will get the amount after complete the job.') }}</span>
                                </div>

                                <div id="pay_by_milestone_btn" class="identity-verifying-list">
                                    <strong>{{ __('Pay by Milestones') }}</strong>
                                    <span>{{ __('You will get the amount after complete each milestone.') }}</span>
                                </div>

                            </div>

                            <div class="description_wrapper">
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <div class="single-input">
                                            <label class="label-title">{{ __('Revision') }}</label>
                                            <input type="number" min="1" max="200" class="form-control" name="offer_revision" id="offer_revision" placeholder="{{ __('Enter Revision') }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="single-input">
                                            <x-duration.delivery-time :class="'single-input set_dead_line'" :title="__('Delivery Time')" :name="'offer_deadline'" :id="'offer_deadline'" />
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="single-input">
                                            <label class="label-title">{{ __('Description') }}</label>
                                            <textarea name="offer_description" id="offer_description" rows="5" class="form-control summernote" placeholder="{{ __('Enter a description') }}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="myJob-wrapper-single milestone_wrapper d-none">
                                <div class="myJob-wrapper-single-header profile-border-bottom">
                                    <h4 class="myJob-wrapper-single-title">{{ __('Milestone') }}</h4>
                                </div>
                                <div class="myJob-wrapper-single-milestone milestone-contractor-parent">
                                    <div class="myJob-wrapper-single-milestone-item">
                                        <div class="myJob-wrapper-single-flex flex-between align-items-start">
                                            <div class="myJob-wrapper-single-contents">
                                                <div class="row g-4">
                                                    <div class="col-sm-12">
                                                        <div class="single-input">
                                                            <label class="label-title">{{ __('title') }}</label>
                                                            <input type="text" class="form-control milestone_title" name="milestone_title[]" placeholder="{{ __('Enter Title') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="single-input">
                                                            <label class="label-title">{{ __('Description') }}</label>
                                                            <textarea cols="30" rows="5" class="form-control milestone_description" name="milestone_description[]" placeholder="{{ __('Enter Description') }}"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="single-input">
                                                            <label class="label-title">{{ __('Price') }}</label>
                                                            <input type="number" class="form-control milestone_price" name="milestone_price[]" placeholder="{{ __('Enter Price') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="single-input">
                                                            <label class="label-title">{{ __('Revision') }}</label>
                                                            <input type="number" min="1" max="100" class="form-control milestone_revision" name="milestone_revision[]" placeholder="{{ __('Enter Revision') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="single-input">
                                                            <x-duration.delivery-time :class="'single-input'" :selectClass="'form-control milestone_deadline set_dead_line'" :title="__('Delivery Time')" :name="'milestone_deadline[]'" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn-wrapper remove-milestone-contractor mt-4">
                                            <a href="#" class="btn-profile btn-bg-cancel">{{ __('Remove') }}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-wrapper mt-4">
                                    <a href="javascript:void(0)" class="btn-profile btn-outline-gray add-contract-milestone"><i class="fa-solid fa-plus"></i>{{ __('Add Milestone') }}</a>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-profile btn-outline-gray btn-hover-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn-profile btn-bg-1 send_offer_realtime_validation">{{ __('Send Offer') }}</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <audio id="chat-alert-sound" style="display: none">
            <source src="{{ asset('assets/uploads/chat_image/sound/facebook_chat.mp3') }}" />
        </audio>

        @include('meeting::components.meeting-modal')
        @include('frontend.user.freelancer.order.report-modal')
    </main>
@endsection

@section('script')
    <script src="{{ asset('assets/common/js/helpers.js') }}"></script>
    <script>
        let client_list = { {{ $arr }} };
    </script>
    <x-chat::livechat-js />
    <x-chat::freelancer.freelancer-chat-js />

    <script>
        //:get_client_id
        $(document).on('click','.get_client_id',function(){
            $('#client_id').val($(this).data('client-id'));
        });

        //pay by milestone
        $(document).on('click','#pay_by_milestone_btn',function(){
            $('.milestone_wrapper').removeClass('d-none');
            $('.description_wrapper').addClass('d-none');

            $('#pay_by_milestone').val('pay-by-milestone');
            $('#pay_at_once').val('');

            $( "#pay_by_milestone_btn").addClass( "active" );
            $( "#pay_at_once_btn").removeClass( "active" );
        });

        //pay at once
        $(document).on('click','#pay_at_once_btn',function(){
            $('.description_wrapper').removeClass('d-none');
            $('.milestone_wrapper').addClass('d-none');

            $('#pay_at_once').val('pay-at-once');
            $('#pay_by_milestone').val('');

            $( "#pay_at_once_btn").addClass( "active" );
            $( "#pay_by_milestone_btn").removeClass( "active" );

        });

        //send_offer_realtime_validation
        $(document).on('click','.send_offer_realtime_validation',function(){

            let pay_by_milestone = $('#pay_by_milestone').val();
            let pay_at_once = $('#pay_at_once').val();
            let offer_price = $('#offer_price').val();
            let offer_revision = $('#offer_revision').val();
            let offer_deadline = $('#offer_deadline').val();

            if(offer_price == ''){
                toastr_warning_js("{{ __('Please fill price field') }}")
                return false;
            }

            if(pay_at_once == 'pay-at-once'){
                if(offer_revision == '' || offer_deadline == ''){
                    toastr_warning_js("{{ __('Please fill all fields') }}")
                    return false;
                }
            }

            if(pay_by_milestone == 'pay-by-milestone'){

                let milestone_title = [], milestone_description = [], milestone_price = [], milestone_revision = [], milestone_deadline = [], total_milestone_price = 0;

                $('.milestone_title').each(function() {
                    let value = $(this).val();
                    if (value) {
                        milestone_title.push(value);
                    }
                });

                $('.milestone_description').each(function() {
                    let value = $(this).val();
                    if (value) {
                        milestone_description.push(value);
                    }
                });


                $('.milestone_price').each(function() {
                    let value = $(this).val();
                    if (value) {
                        milestone_price.push(value);
                        total_milestone_price = parseInt(total_milestone_price) + parseInt(value);
                    }
                });

                $('.milestone_revision').each(function() {
                    let value = $(this).val();
                    if (value) {
                        milestone_revision.push(value);
                    }
                });

                $('.milestone_deadline').each(function() {
                    let value = $(this).val();
                    if (value) {
                        milestone_deadline.push(value);
                    }
                });

                if(offer_price != total_milestone_price){
                    toastr_warning_js("{{ __('Total milestone price must be equal to offer price') }}")
                    return false;
                }

                if (offer_price == '' || milestone_title.length === 0 || milestone_description.length === 0 || milestone_price.length === 0 || milestone_revision.length === 0 || milestone_deadline.length === 0) {
                    toastr_warning_js("{{ __('Please fill all fields') }}")
                    return false;
                }
            }
        })

          $(document).on("keydown", "#message", function(e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            $("#freelancer-send-message-to-client").click();
        }
    });
    </script>

    <script>
        @if(count($errors) > 0)
        @foreach($errors->all() as $error)
        toastr.warning("{{ $error }}");
        @endforeach
        @endif
    </script>
    <x-summernote.summernote-js />
@endsection
