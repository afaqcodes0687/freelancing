@php
    $profile_percent_completed = 0;

    if (Auth::guard('web')->check()) {
        if (Auth::guard('web')->user()->user_type == 1) {
            $profile_percent_completed = calculate_client_profile_completion(Auth::id());
        } else {
            $profile_percent_completed = calculate_profile_completion(Auth::id());
        }
    }
@endphp

@if(Auth::guard('web')->check())
    <style>
        .progress-container {
            width: 200px; 
        }
    </style>

    <strong>Profile Status</strong>
    <div class="progress-container">
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: {{$profile_percent_completed}}%;" aria-valuenow="{{$profile_percent_completed}}" aria-valuemin="0" aria-valuemax="100">
                {{$profile_percent_completed}}%
            </div>
        </div>
    </div>
@endif
