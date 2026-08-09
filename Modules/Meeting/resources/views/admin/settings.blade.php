@extends('backend.layout.master')
@section('site-title')
    {{__('Google Meeting Settings')}}
@endsection
@section('content')
    <div class="col-lg-8 offset-lg-2 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{$error}}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card shadow-sm border-0 radius-10">
                    <div class="card-body p-4">
                        <h4 class="header-title mb-4" style="color: #309400;"><i class="fa-solid fa-video me-2"></i>{{__('Meeting Provider Settings')}}</h4>
                        
                        <form action="{{ route('admin.meeting.settings.update') }}" method="POST">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="preferred_meeting_provider" class="mb-2"><strong>{{__('Default Meeting Provider')}}</strong></label>
                                <select name="preferred_meeting_provider" id="preferred_meeting_provider" class="form-control">
                                    <option value="google" @if($preferredProvider == 'google') selected @endif>{{ __('Google Meet (Requires User API)') }}</option>
                                    <option value="jitsi" @if($preferredProvider == 'jitsi') selected @endif>{{ __('Jitsi Meet (Free & Instant)') }}</option>
                                </select>
                                <p class="text-muted mt-2 small" style="line-height: 1.5;">
                                    <i class="fa-solid fa-circle-info me-1 text-info"></i>
                                    {{ __('Choose which platform will be used by freelancers and clients. Jitsi is recommended as it requires zero configuration for users.') }}
                                </p>
                            </div>

                            <div class="mt-4 mb-4 p-3 bg-light radius-10 border-left" style="border-left: 4px solid #309400 !important;">
                                <h6 class="header-title small text-brand-green mb-2">{{__('Why Jitsi Meet?')}}</h6>
                                <p class="text-muted small mb-0">{{__('No API keys, no verification, and no "Request to Join" (Knocking) issues. Users can join instantly.')}}</p>
                            </div>
                            
                            <div class="btn-wrapper mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2" style="background-color: #309400; border: none; font-weight: 600;">{{__('Update Settings')}}</button>
                            </div>
                        </form>
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
                @if(session()->has('msg'))
                    let type = "{{ session('type') }}";
                    let msg = "{{ session('msg') }}";
                    if(type === 'success'){
                        toastr.success(msg);
                    }else{
                        toastr.error(msg);
                    }
                @endif
            });
        })(jQuery);
    </script>
@endsection
