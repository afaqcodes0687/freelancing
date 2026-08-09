@extends('backend.layout.master')
@section('title', 'Affiliate Support Tickets')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <!-- <h4>Affiliate Support Tickets</h4> -->
            <!-- <div>
          <form method="GET" class="d-flex">
            <select name="status" class="form-select me-2">
              <option value="">All</option>
              <option value="pending" {{ request('status')=='pending' ? 'selected':'' }}>Pending</option>
              <option value="open" {{ request('status')=='open' ? 'selected':'' }}>Open</option>
              <option value="resolved" {{ request('status')=='resolved' ? 'selected':'' }}>Resolved</option>
              <option value="closed" {{ request('status')=='closed' ? 'selected':'' }}>Closed</option>
            </select>
            <button class="btn btn-secondary">Filter</button>
          </form>
        </div> -->
        </div>

        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Affiliate</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>When</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $t)
                        <tr id="ticket-{{ $t->id }}">
                            <td>{{ $t->id }}</td>
                            <td>{{ optional($t->affiliate)->first_name ?? '—' }} <br>
                                <small>{{ optional($t->affiliate)->email }}</small></td>
                            <td>{{ $t->subject }}</td>
                            <td>{{ $t->message }}</td>
                            <!-- <td><span class="badge bg-{{ $t->status == 'pending' ? 'warning text-dark' : ($t->status=='resolved'?'success':'secondary') }}">{{ ucfirst($t->status) }}</span></td> -->
                            <td>{{ $t->created_at->diffForHumans() }}</td>
                            <td>
                                <!-- <a href="{{ route('admin.affiliate.support.show', $t->id) }}" class="btn btn-sm btn-primary">View</a> -->
                                <button class="btn btn-sm btn-danger delete-ticket" data-id="{{ $t->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        (function ($) {
            $(document).on('click', '.delete-ticket', function () {
                if (!confirm('Delete ticket?')) return;
                const id = $(this).data('id');
                $.post("{{ url('admin/affiliate/support') }}/" + id + "/delete", { _token: "{{ csrf_token() }}" }, function () {
                    $('#ticket-' + id).remove();
                    toastr.success('Ticket deleted');
                }).fail(() => toastr.error('Delete failed'));
            });
        })(jQuery);
    </script>
@endsection