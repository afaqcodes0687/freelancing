<table class="table">
    <thead>
        <tr>
            <th>{{__('ID')}}</th>
            <th>{{__('Meeting Title')}}</th>
            <th>{{__('Participants')}}</th>
            <th>{{__('Schedule')}}</th>
            <th>{{__('Status')}}</th>
            <th>{{__('Meeting Link')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allMeetings as $meeting)
            <tr>
                <td>#{{ $meeting->id }}</td>
                <td>
                    <div class="fw-bold text-dark">{{ $meeting->title }}</div>
                    <small class="text-muted">{{ Str::limit($meeting->description, 50) }}</small>
                </td>
                <td>
                    <div class="d-flex flex-column gap-2">
                        <div class="participant-info">
                            <span class="badge bg-success mb-1 text-white">{{ $meeting->sender->user_type == 1 ? __('Client') : __('Freelancer') }} ({{ __('Host') }})</span>
                            <div class="fw-bold text-dark">{{ $meeting->sender->fullname ?? $meeting->sender->username ?? __('N/A') }}</div>
                            <div class="small text-muted">{{ $meeting->sender->email ?? '' }}</div>
                        </div>
                        <div class="participant-info mt-2 pt-2 border-top">
                            <span class="badge bg-info mb-1 text-white">{{ $meeting->receiver->user_type == 1 ? __('Client') : __('Freelancer') }} ({{ __('Recipient') }})</span>
                            <div class="fw-bold text-dark">{{ $meeting->receiver->fullname ?? $meeting->receiver->username ?? __('N/A') }}</div>
                            <div class="small text-muted">{{ $meeting->receiver->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="date-time-info">
                        <div class="text-brand-green fw-bold">
                            <i class="fa-regular fa-calendar me-1"></i> {{ $meeting->start_time->format('d M, Y') }}
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="fa-regular fa-clock me-1"></i> {{ $meeting->start_time->format('h:i A') }}
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-scheduled">{{ ucfirst($meeting->status) }}</span>
                </td>
                <td>
                    <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fa-solid fa-external-link me-1"></i> {{__('Join/View')}}
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="pagination-wrapper mt-4">
    {{ $allMeetings->links() }}
</div>
