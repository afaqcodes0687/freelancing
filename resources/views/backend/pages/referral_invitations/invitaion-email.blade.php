<x-validation.error />
<table class="DataTable_activation">
    <thead>
    <tr>
        <th>{{__('ID')}}</th>
        <th>{{__('User ID')}}</th>
        <th>{{__('Email')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($referrals as $referral)
        <tr>
            <td>{{ $referral->id }}</td>
            <td>
                {{ $referral->user?->first_name }} {{ $referral->user?->last_name }}
            </td>

             <td>
                {{ $referral->email }} <br>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$referrals"/>
