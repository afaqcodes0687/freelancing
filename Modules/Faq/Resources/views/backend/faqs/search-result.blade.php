<x-validation.error />
<table class="DataTable_activation">
    <thead>
    <tr>
        <th>{{__('ID')}}</th>
        <th>{{__('Category')}}</th>
        <th>{{__('Question')}}</th>
        <th>{{__('Date')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_faqs as $faq)
        <tr>
            <td>{{ $faq->id }}</td>
            <td>
                @if($faq->category)
                    <span class="badge bg-primary">{{ $faq->category->category }}</span>
                @else
                    <span class="badge bg-secondary">{{ __('Global') }}</span>
                @endif
            </td>
            <td>{{ Str::limit($faq->question, 80) }}</td>
            <td>{{ $faq->created_at->toFormattedDateString() }}</td>
            <td>
                <button type="button" class="btn btn-sm btn-warning edit_faq_btn"
                    data-id="{{ $faq->id }}"
                    data-question="{{ $faq->question }}"
                    data-answer="{{ $faq->answer }}"
                    data-category_id="{{ $faq->category_id }}">
                    <i class="fas fa-edit"></i>
                </button>
                <a href="{{ route('admin.faq.delete', $faq->id) }}"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('{{ __('Are you sure?') }}')">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_faqs"/>
