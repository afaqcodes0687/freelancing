<script>
    @if(request()->has('client_id'))
        $(document).ready(function () {
            $('.chat_item[data-client-id={{ request()->client_id }}]').trigger('click').addClass("active")
        })
    @endif
    /*
    ========================================
        Chat Click and Active Class
    ========================================
    */
    let oldChannelName = "";
    let liveChat, channelName;
    liveChat = new LiveChat();

    $(document).on('click', '.chat_item', function () {
        //: first need to remove all active class and after that add active class to clicked item
        $(this).siblings().removeClass('active');
        $('#freelancer-message-footer').removeClass('d-none');
        $(this).addClass('active');
        $('.chat_wrapper__contact__close, .body-overlay').removeClass('active');
        //: now fetch all old conversation from request with header and body
        fetch_chat_data($(this).attr("data-client-id"));

        $("#chat_body").attr("data-current-user", $(this).attr("data-client-id"))

        channelName = {
            client_id: $(this).attr("data-client-id"),
            freelancer_id: "{{ auth('web')->id() }}",
            type: "freelancer"
        };

        if (client_list["client_id_" + channelName.client_id] != true) {

            //: initialize livechat js
            liveChat.createChannel(channelName.client_id, channelName.freelancer_id, channelName.type);

            liveChat.bindEvent('livechat-client-' + channelName.client_id, function (data) {
                let isActive = $("#chat_body").attr("data-current-user") == data.livechat?.client?.id;
                if (isActive) {
                    $("#chat_body").append(data.messageBlade);
                    scrollToBottom();
                }

                let endpoint = isActive ? "{{ route('freelancer.message.mark.seen') }}" : "{{ route('freelancer.message.mark.delivered') }}";
                $.ajax({
                    url: endpoint,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        message_id: data.message.id
                    }
                });

                if (document.getElementById("chat-alert-sound") != undefined) {
                    var alert_sound = document.getElementById("chat-alert-sound");
                    alert_sound.play();
                }
            });

            // Handle status updates from Client
            liveChat.bindEvent('livechat-client-status-' + channelName.client_id, function (data) {
                let tickSpans = data.message_id == 0
                    ? $('.message-status-ticks')
                    : $('.message-status-ticks[data-status-msg-id="' + data.message_id + '"]');

                tickSpans.each(function () {
                    let tickSpan = $(this);
                    if (data.status === 'seen') {
                        tickSpan.html('<i class="fa-solid fa-check-double text-primary" style="font-size: 12px;"></i>');
                    } else if (data.status === 'delivered') {
                        if (tickSpan.find('.fa-check').length > 0) {
                            tickSpan.html('<i class="fa-solid fa-check-double text-muted" style="font-size: 12px;"></i>');
                        }
                    }
                });
            });

            // edited message from freelancer should also be broadcasted on the same channel so client can see change
            liveChat.bindEvent('livechat-freelancer-edited-' + channelName.freelancer_id, function (data) {
                // find message in DOM by data-message-id and update text
                const messageEl = $("#chat_body [data-message-id=" + data.message_id + "] [data-chat-text]");
                if (messageEl.length > 0) {
                    messageEl.text(data.message);
                }
            });

            client_list["client_id_" + channelName.client_id] = true;
            oldChannelName = channelName;
        }

        $(this).find(".chat_wrapper__contact__list__time .badge").fadeOut();
    });

    $(document).on("click", "#freelancer-send-message-to-client", function () {
        //: prepare chat post data
        let file = $('#freelancer-message-footer #message-file')[0].files[0];
        let form = new FormData();
        form.append('message', $('#freelancer-message-footer #message').val());
        form.append('file', file !== undefined ? file : '');
        form.append('from_user', '2');
        form.append('client_id', $("#livechat-message-header").attr('data-client-id'));
        form.append('from', "chatbox");
        form.append('_token', "{{ csrf_token() }}");

        let messages_ = $('#freelancer-message-footer #message').val();


        @if(moduleExists('SecurityManage'))
            //get security manage module name
            let module_exits = "<?php    echo moduleExists('SecurityManage') ?? '' ?>"
            if (module_exits) {
                let words = JSON.parse('<?php    echo json_encode(\Modules\SecurityManage\Entities\Word::select('word')->where("status", "active")->pluck("word")->toArray()); ?>');

                // Function to check if any word exists in the string
                function checkAnyWordExists(words, messages_) {
                    return words.some(word => messages_.includes(word));
                }
                // Check if any of the words exist in the string
                let anyWordExists = checkAnyWordExists(words, messages_);

                // Function to get all matching words in the string
                function getAllMatchedWords(words, message) {
                    return words.filter(word => message.includes(word));
                }
                // Get all matching words
                let matchedWords = getAllMatchedWords(words, messages_);

                if (anyWordExists) {
                    toastr_warning_js('You can not send restricted words:' + matchedWords);
                    return false;
                }
            }
        @endif

        if (messages_ != '' || file !== undefined) {
            $('#freelancer-message-footer #message').val('');
            $('#freelancer-message-footer #message-file').val('');
            $('#freelancer-message-footer .show_uploaded_file').text('');

            // Optimistic UI Update
            let tempId = 'temp_' + Date.now();

            let tempMessageHtml = `
                <div class="chat-wrapper-details-inner-chat chat-reply optimistic-message" id="${tempId}" style="opacity: 0.7;">
                    <div class="chat-wrapper-details-inner-chat-flex">
                        <div class="chat-wrapper-details-inner-chat-thumb">
                            <img src="${$('.chat-wrapper-details-inner-chat.chat-reply:last .chat-wrapper-details-inner-chat-thumb img').attr('src') || '{{ asset('assets/static/img/author/author.jpg') }}'}" alt="">
                        </div>
                        <div class="chat-wrapper-details-inner-chat-contents">
                            <p class="chat-wrapper-details-inner-chat-contents-para">
                                <span class="chat-wrapper-details-inner-chat-contents-para-span">${messages_}</span>
                            </p>
                            <span class="chat-wrapper-details-inner-chat-contents-time mt-0 d-flex align-items-center justify-content-end" style="font-size: 10px; color: #888;">
                                <span>{{ __('Just now') }}</span>
                                <span class="ms-1"><i class="fa-solid fa-clock text-muted" style="font-size: 10px;"></i></span>
                            </span>
                        </div>
                    </div>
                </div>`;

            $("#chat_body").append(tempMessageHtml);
            scrollToBottom();

            send_ajax_request("post", form, "{{ route("freelancer.message.send") }}", function () { }, function (response) {
                // Replace optimistic message with actual response
                let $tempMsg = $(`#${tempId}`);
                if ($tempMsg.length) {
                    $tempMsg.replaceWith(response);
                } else {
                    $("#chat_body").append(response);
                }

                if (response.status == 'image_not_allow_in_demo') {
                    toastr_warning_js("{{ __('This is demonstration purpose only, you may not able to send files in demo purpose, once your purchase this script you will get access to all settings.') }}");
                }
                scrollToBottom();
            }, function (xhr) {
                // Handle error: maybe show a "failed to send" indicator
                $(`#${tempId}`).css('border', '1px solid red').attr('title', 'Failed to send');

                let msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('Failed to send message') }}";
                toastr_error_js(msg);
                
                setTimeout(() => { $(`#${tempId}`).remove(); }, 3000);
            })
        } else {
            return false;
        }
    });

    $(document).on('click', '#scheduleMeetingBtn', function () {
        let client_id = $('#livechat-message-header').attr('data-client-id');
        let live_chat_id = $('#chat_body').attr('data-live-chat-id') || $('.chat_item.active').attr('data-live-chat-id');

        if (!client_id) {
            toastr_warning_js("{{ __('Please select a client first') }}");
            return;
        }

        $('#meeting_live_chat_id').val(live_chat_id);
        $('#meeting_receiver_id').val(client_id);
        $('#meetingModal').modal('show');
    });

    $(document).on('click', '#chatProfileDropdownToggle', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let dropdown = $('#chatProfileDropdown');
        let isOpen = !dropdown.hasClass('d-none');
        dropdown.toggleClass('d-none');
        $(this).attr('aria-expanded', !isOpen);
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.chat-header-profile-trigger').length) {
            $('#chatProfileDropdown').addClass('d-none');
            $('#chatProfileDropdownToggle').attr('aria-expanded', 'false');
        }
    });

    $(document).on('click', '.chat-favorite-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let btn = $(this);
        let client_id = btn.attr('data-client-id');
        let icon = btn.find('i');
        let label = btn.find('.chat-favorite-label');

        if (!client_id) {
            toastr_warning_js("{{ __('Please select a client first') }}");
            return;
        }

        let formData = new FormData();
        formData.append('client_id', client_id);
        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('freelancer.toggle.favorite') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.is_favorited) {
                    icon.removeClass('fa-regular').addClass('fa-solid');
                    label.text("{{ __('Remove from Favorites') }}");
                    btn.attr('data-is-favorited', '1');
                } else {
                    icon.removeClass('fa-solid').addClass('fa-regular');
                    label.text("{{ __('Add to Favorites') }}");
                    btn.attr('data-is-favorited', '0');
                }
                toastr_success_js(response.message);

                let currentFilter = $('#filterFavoritesBtn').hasClass('active') ? 'favorites' : 'all';
                loadChatList(currentFilter);
            },
            error: function (xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "{{ __('Failed to update favorite') }}";
                toastr_error_js(msg);
            }
        });
    });

    $(document).on('click', '.list-profile-dropdown-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        let trigger = $(this).closest('.list-profile-trigger');
        let dropdown = trigger.find('.list-profile-dropdown');
        
        if (dropdown.length > 0) {
            $('body').append(dropdown);
            trigger.data('dropdown-element', dropdown);
            dropdown.data('trigger-element', trigger);
        } else {
            dropdown = trigger.data('dropdown-element');
        }
        
        if (!dropdown) return;
        
        let isOpen = !dropdown.hasClass('d-none');
        
        // close all other dropdowns
        $('.list-profile-dropdown').addClass('d-none');
        
        if (!isOpen) {
            let offset = trigger.offset();
            dropdown.css({
                position: 'absolute',
                top: offset.top + trigger.outerHeight() + 5,
                left: offset.left,
                zIndex: 999999,
                margin: 0
            });
            dropdown.removeClass('d-none');
        }
    });

    $(document).on('scroll', '.chat-wrapper-contact-list', function() {
        $('.list-profile-dropdown').addClass('d-none');
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.list-profile-trigger').length && !$(e.target).closest('.list-profile-dropdown').length) {
            $('.list-profile-dropdown').addClass('d-none');
        }
    });

    $(document).on('click', '.list-profile-dropdown-item', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', '.list-favorite-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let btn = $(this);
        let client_id = btn.attr('data-client-id');
        let icon = btn.find('i');
        let label = btn.find('.chat-favorite-label');

        if (!client_id) return;

        let formData = new FormData();
        formData.append('client_id', client_id);
        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('freelancer.toggle.favorite') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr_success_js(response.message);

                // if this is the active chat, update the header star
                let activeHeaderId = $('#livechat-message-header').attr('data-client-id');
                if (activeHeaderId == client_id) {
                    let headerBtn = $('.chat-favorite-toggle');
                    let hIcon = headerBtn.find('i');
                    let hLabel = headerBtn.find('.chat-favorite-label');
                    if (response.is_favorited) {
                        hIcon.removeClass('fa-regular').addClass('fa-solid');
                        hLabel.text("{{ __('Remove from Favorites') }}");
                        headerBtn.attr('data-is-favorited', '1');
                    } else {
                        hIcon.removeClass('fa-solid').addClass('fa-regular');
                        hLabel.text("{{ __('Add to Favorites') }}");
                        headerBtn.attr('data-is-favorited', '0');
                    }
                }

                let currentFilter = $('#filterFavoritesBtn').hasClass('active') ? 'favorites' : ($('#filterArchivedBtn').hasClass('active') ? 'archived' : 'all');
                loadChatList(currentFilter);
            },
            error: function (xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "{{ __('Failed to update favorite') }}";
                toastr_error_js(msg);
            }
        });
    });

    $(document).on('click', '.list-report-user', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let clientId = $(this).data('client-id');
        $('#report_to_client_id').val(clientId);
        $('#report_order_id').val(''); // Clear order ID just in case
        $('.list-profile-dropdown').addClass('d-none'); // Hide the dropdown
        let modal = new bootstrap.Modal(document.getElementById('reportModal'));
        modal.show();
    });

    $(document).on('click', '.list-archive-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let btn = $(this);
        let chatId = btn.attr('data-chat-id');
        
        if (!chatId) return;

        let formData = new FormData();
        formData.append('chat_id', chatId);
        formData.append('_token', "{{ csrf_token() }}");

        // close the list dropdown
        $('.list-profile-dropdown').addClass('d-none');

        $.ajax({
            url: "{{ route('freelancer.toggle.archive') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr_success_js(response.message);

                let currentFilter = $('#filterFavoritesBtn').hasClass('active') ? 'favorites' : ($('#filterArchivedBtn').hasClass('active') ? 'archived' : 'all');
                loadChatList(currentFilter);
            },
            error: function (xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "{{ __('Failed to update archive') }}";
                toastr_error_js(msg);
            }
        });
    });

    $(document).on('click', '.list-end-conversation', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let chatId = $(this).attr('data-chat-id');
        let otherName = $(this).attr('data-other-name');

        $('#endConversationChatId').val(chatId);
        $('#endConversationName').text(otherName);
        $('#endConversationNameBody').text(otherName);

        // close the list dropdown
        $('.list-profile-dropdown').addClass('d-none');

        let modal = new bootstrap.Modal(document.getElementById('endConversationModal'));
        modal.show();
    });

    $(document).on('click', '.header-end-conversation', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let chatId = $(this).attr('data-chat-id');
        let otherName = $(this).attr('data-other-name');

        $('#endConversationChatId').val(chatId);
        $('#endConversationName').text(otherName);
        $('#endConversationNameBody').text(otherName);

        // close the header dropdown
        $('#chatProfileDropdown').addClass('d-none');
        $('#chatProfileDropdownToggle').attr('aria-expanded', 'false');

        let modal = new bootstrap.Modal(document.getElementById('endConversationModal'));
        modal.show();
    });

    $(document).on('click', '#confirmEndConversationBtn', function () {
        let chatId = $('#endConversationChatId').val();
        if (!chatId) return;

        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> {{ __('Ending...') }}');

        $.ajax({
            url: "{{ route('freelancer.end.conversation') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                chat_id: chatId
            },
            success: function (response) {
                bootstrap.Modal.getInstance(document.getElementById('endConversationModal')).hide();
                toastr_success_js(response.message ?? "{{ __('Conversation ended successfully') }}");

                // If the ended chat was the active one, clear the chat area
                let activeChatId = $('.chat_item.active').attr('data-live-chat-id');
                if (activeChatId == chatId) {
                    $('#chat_header').addClass('d-none').empty();
                    $('#chat_body').empty();
                    $('#freelancer-message-footer').addClass('d-none');
                }

                let currentFilter = $('#filterFavoritesBtn').hasClass('active') ? 'favorites' : ($('#filterArchivedBtn').hasClass('active') ? 'archived' : 'all');
                loadChatList(currentFilter);
            },
            error: function (xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "{{ __('Failed to end conversation') }}";
                toastr_error_js(msg);
                btn.prop('disabled', false).html('<i class="fa-regular fa-circle-xmark"></i> {{ __('End Conversation') }}');
            }
        });
    });

    $(document).on('click', '#filterAllBtn', function () {
        $('#filterAllBtn').addClass('active');
        $('#filterFavoritesBtn').removeClass('active');
        $('#filterArchivedBtn').removeClass('active');
        loadChatList('all');
    });

    $(document).on('click', '#filterFavoritesBtn', function () {
        $('#filterFavoritesBtn').addClass('active');
        $('#filterAllBtn').removeClass('active');
        $('#filterArchivedBtn').removeClass('active');
        loadChatList('favorites');
    });

    $(document).on('click', '#filterArchivedBtn', function () {
        $('#filterArchivedBtn').addClass('active');
        $('#filterAllBtn').removeClass('active');
        $('#filterFavoritesBtn').removeClass('active');
        loadChatList('archived');
    });

    let chatListRequest = null;

    function loadChatList(filter) {
        if (chatListRequest) {
            chatListRequest.abort();
        }

        // Clean up any dropdowns that were moved to body
        $('body > .list-profile-dropdown').remove();

        let formData = new FormData();
        formData.append('filter', filter);
        formData.append('_token', "{{ csrf_token() }}");

        let $chatList = $('#chatContactList');
        $chatList.css('opacity', '0.5');

        chatListRequest = $.ajax({
            url: "{{ route('freelancer.filter.chats') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $chatList.css('opacity', '1');
                chatListRequest = null;

                if (response.status !== 'ok') {
                    return;
                }

                let activeClientId = $('#livechat-message-header').attr('data-client-id');

                if (response.count === 0) {
                    $chatList.html('<li class="chat-empty-state"><p>{{ __('No chats found') }}</p></li>');
                } else {
                    $chatList.html(response.html);
                    if (activeClientId) {
                        $chatList.find('.chat_item[data-client-id="' + activeClientId + '"]').addClass('active');
                    }
                }
            },
            error: function (xhr, status) {
                $chatList.css('opacity', '1');
                chatListRequest = null;

                if (status === 'abort') {
                    return;
                }

                let msg = xhr.responseJSON?.message ?? "{{ __('Failed to load chats') }}";
                toastr_error_js(msg);
            }
        });
    }

    $(document).on('click', '#confirmScheduleMeeting', function () {
        let form = $('#meetingScheduleForm');
        let formData = new FormData(form[0]);
        let btn = $(this);

        btn.attr('disabled', true).text("{{ __('Scheduling...') }}");

        $.ajax({
            url: "{{ route('meeting.schedule') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#meetingModal').modal('hide');
                toastr_success_js(response.message);
                btn.attr('disabled', false).text("{{ __('Schedule') }}");
                form[0].reset();

                if (response.message_html) {
                    $('#chat_body').append(response.message_html);
                    scrollToBottom();
                }
            },
            error: function (xhr) {
                btn.attr('disabled', false).text("{{ __('Schedule') }}");
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "{{ __('Failed to schedule meeting') }}";
                toastr_error_js(msg);
            }
        });
    });

    // inline edit message: click Edit -> turn into textarea and show Save/Cancel
    $(document).on('click', '.freelancer-edit-message', function () {
        const container = $(this).closest('.chat-wrapper-details-inner-chat');
        const span = container.find('[data-chat-text]');
        const original = span.text();
        if (container.data('editing') === true) { return; }
        container.data('editing', true);
        span.hide();
        const textarea = $('<textarea class="form-control form-message" rows="2"></textarea>').val(original);
        const btnWrap = $('<div class="d-flex gap-2 mt-2"></div>');
        const saveBtn = $('<button class="btn btn-sm btn-primary">{{ __("Save") }}</button>');
        const cancelBtn = $('<button class="btn btn-sm btn-outline-secondary">{{ __("Cancel") }}</button>');
        btnWrap.append(saveBtn, cancelBtn);
        span.after(textarea).after(btnWrap);

        cancelBtn.on('click', function (e) {
            e.preventDefault();
            textarea.remove();
            btnWrap.remove();
            span.show();
            container.data('editing', false);
        });

        saveBtn.on('click', function (e) {
            e.preventDefault();
            const newText = textarea.val();
            if (newText.trim() === '') { return; }
            const form = new FormData();
            form.append('_token', "{{ csrf_token() }}");
            form.append('message_id', container.attr('data-message-id'));
            form.append('message', newText);

            send_ajax_request('post', form, "{{ route('freelancer.message.update') }}", function () { }, function (resp) {
                span.text(resp.message);
                textarea.remove();
                btnWrap.remove();
                span.show();
                container.data('editing', false);
            }, function (xhr) {
                if (xhr?.responseJSON?.status === 'restricted_words') {
                    toastr_warning_js('You can not send restricted words: ' + (xhr.responseJSON.words || []).join(', '));
                }
            });
        });
    });


    $(document).on("click", ".load-more-pagination", function () {
        let el = $(this);
        let page = parseInt(el.attr('data-page'));
        let nextPage = page + 1;

        fetch_chat_data($('#livechat-message-header').attr('data-client-id'), nextPage, function () {
            el.attr("data-page", nextPage);
        });
    });

    function fetch_chat_data(client_id, page = 1, callback) {
        //: hare call a api for fetching data from database if no data available then new item will be inserted
        let formData;

        formData = new FormData();
        formData.append("client_id", client_id);
        formData.append("_token", "{{ csrf_token() }}");
        formData.append("from_user", 2)

        send_ajax_request("post", formData, `{{ route("freelancer.fetch.chat.client.record") }}?page=${page}`, function () {

        }, function (response) {
            $('.unseen_message_count_' + client_id).addClass("d-none")
            $('.reload_unseen_message_count').load(location.href + ' .reload_unseen_message_count')

            if (page > 1) {
                $("#chat_body").children().not(":first").prepend(response.body);
            } else {

                let loadmore = `
                            <div class="pagination d-flex justify-content-center mb-3">
                                <button data-page="1" class="btn btn-info load-more-pagination">{{ __("Load More") }}</button>
                            </div>`;

                $("#chat_body").html((response.allow_load_more ? loadmore : '') + response.body);

                $("#chat_header").html(response.header);

                scrollToBottom();
            }

            $("#vendor-message-footer").removeClass("d-none");
            $("#chat_header").removeClass("d-none");

            if (typeof callback === "function") {
                callback();
            }
        }, function () {

        })
    }

    function scrollToBottom() {
        const scrollingElement = (document.querySelector("#chat_body") || document.body);
        let scrollSmoothlyToBottom = document.querySelector("#chat_body");

        $(scrollingElement).animate({
            scrollTop: scrollSmoothlyToBottom.scrollHeight,
        }, 500);
    }

    (function () {
        /*
        ========================================
            Attach File js
        ========================================
        */

        let uploadImage = document.querySelector(".show_uploaded_file");
        let inputTag = document.querySelector(".inputTag");

        if (inputTag != null) {
            inputTag.addEventListener('change', () => {

                let inputTagFile = document.querySelector(".inputTag").files[0];

                uploadImage.innerText = inputTagFile.name;
            });
        };
    })();

    //toastr warning
    function toastr_warning_js(msg) {
        Command: toastr["warning"](msg, "Warning !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
</script>