<x-validation.error />
<table class="DataTable_activation">
    <thead>
    <tr>
        <th>{{__('ID')}}</th>
        <th>{{__('Refer Name')}}</th>
        <th>{{__('Refered Name')}}</th>
        <th>{{__('Referral Status')}}</th>
        <th>{{__('Reward Amount')}}</th>
        <th>{{__('Referral Complete')}}</th>
    </tr>
    </thead>
    <tbody>
        @foreach($referrals_email as $referral)
            <tr>
                <td>{{ $referral->id }}</td>
                <td>
                    {{ $referral->referrer->first_name ?? '' }} {{ $referral->referrer->last_name ?? '' }}
                </td>

                <td>
                    {{ $referral->referred->first_name ?? '' }} {{ $referral->referred->last_name ?? '' }}
                </td>

                <td>
                    <span class="badge bg-{{ $referral->status === 'completed' ? 'success' : 'warning' }}">
                        {{ ucfirst($referral->status) }}
                    </span>
                </td>
                <td>${{ number_format($referral->reward_amount, 2) }}</td>
                <td>{{ $referral->completed_at ? $referral->completed_at->format('d M Y') : '—' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$referrals_email"/>
