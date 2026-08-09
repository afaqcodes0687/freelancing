<table>
    <thead>
    <tr>
        <th>{{ __('Gateway / Type') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Amount') }}</th>
        <th>{{ __('Date') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_histories as $history)
        <tr>
            <td>
                @if($history->payment_gateway == 'manual_payment')
                    {{ ucfirst(str_replace('_',' ',$history->payment_gateway)) }}
                @elseif($history->payment_gateway == 'withdraw')
                    {{ __('Withdraw') }}
                @else
                    {{ $history->payment_gateway == 'authorize_dot_net' ? __('Authorize.Net') : ucfirst($history->payment_gateway) }}
                @endif
            </td>
            <td>
                @if($history->payment_status == '' || $history->payment_status == 'cancel')
                    <span class="btn btn-danger btn-sm">{{ __('Cancel') }}</span>
                @else
                    <span class="btn btn-{{ $history->payment_status == 'pending' ? 'warning' : 'success' }} btn-sm">{{ $history->payment_status == 'success' ? __('Complete') : ucfirst($history->payment_status) }}</span>
                @endif
            </td>
            <td>
                @if($history->payment_gateway == 'withdraw' && $history->payment_status != 'success')
                    <span class="text-danger">-{{ float_amount_with_currency_symbol($history->amount) }}</span>
                @else
                    <span class="text-success">+{{ float_amount_with_currency_symbol($history->amount) }}</span>
                @endif
            </td>
            <td>{{ $history->created_at->format('d M, Y h:i A') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>


<div class="deposit-history-pagination mt-4">
    <x-pagination.laravel-paginate :allData="$all_histories"/>
</div>
