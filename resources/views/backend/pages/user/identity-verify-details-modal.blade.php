<!-- State Edit Modal -->
<div class="modal fade" id="userIdentityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Verify User Identity') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="user_identity_details"></div>
                    
                    <hr class="mt-4 mb-4">
                    
                    <div class="notification-section">
                        <h5 class="mb-3">{{ __('Notify User for Missing Details') }}</h5>
                        
                        <div class="form-group">
                            <p class="small mb-2" style="color:#309400">{{ __('Explain professionaly why the verification was declined or what is missing.') }}</p>
                            <textarea class="form-control" id="additional_message" rows="3" placeholder="{{ __('Type your message here...') }}"></textarea>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-info text-white send_missing_details_notification" style="color: #309400;">
                                <i class="fas fa-paper-plane mr-2" style="color:#309400"></i> {{ __('Send Notification') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mt-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    @can('user-identity-decline')
                    <x-btn.submit :title="__('Decline Verify Identity')" :class="'btn btn-danger mt-4 pr-4 pl-4 user_identity_decline'" />
                    @endcan
                    @can('user-identity-status-update')
                    <x-btn.submit :title="__('Update Verify Identity Status')" :class="'btn btn-primary mt-4 pr-4 pl-4 user_verify_status'" />
                    @endcan
                </div>
            </form>
        </div>
    </div>
</div>
