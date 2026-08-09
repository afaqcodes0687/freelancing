@extends('backend.layout.master')
@section('title','Ticket #'.$ticket->id)

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h4>Ticket #{{ $ticket->id }} — {{ $ticket->subject }}</h4>
    <div>
      <select id="change-status" class="form-select">
        <option value="pending" {{ $ticket->status=='pending'?'selected':'' }}>Pending</option>
        <option value="open" {{ $ticket->status=='open'?'selected':'' }}>Open</option>
        <option value="resolved" {{ $ticket->status=='resolved'?'selected':'' }}>Resolved</option>
        <option value="closed" {{ $ticket->status=='closed'?'selected':'' }}>Closed</option>
      </select>
    </div>
  </div>

  <div class="card-body">
    <h5>From: {{ optional($ticket->affiliate)->first_name ?? '—' }} <small>({{ optional($ticket->affiliate)->email }})</small></h5>
    <p><strong>Message:</strong><br>{{ $ticket->message }}</p>

    <hr>

    <h5>Admin Reply</h5>
    <div id="admin-reply-area">
      @if($ticket->admin_reply)
        <div class="alert alert-success">{!! nl2br(e($ticket->admin_reply)) !!}</div>
      @else
        <div class="alert alert-secondary">No reply yet.</div>
      @endif
    </div>

    <form id="admin-reply-form">
      @csrf
      <div class="mb-3">
        <textarea name="reply" id="reply" rows="6" class="form-control" placeholder="Write reply..."></textarea>
      </div>
      <button class="btn btn-success" type="submit">Send Reply & Mark Resolved</button>
    </form>
  </div>
</div>
@endsection

@section('script')
<script>
(function($){
  // Change status
  $('#change-status').on('change', function(){
    const status = $(this).val();
    $.post("{{ route('admin.affiliate.support.status', $ticket->id) }}", {_token: "{{ csrf_token() }}", status}, function(res){
      if(res.status === 'success') toastr.success(res.msg || 'Status updated');
      else toastr.warning(res.msg || 'Could not update');
    }).fail(()=> toastr.error('Server error'));
  });

  // Send reply (AJAX)
  $('#admin-reply-form').on('submit', function(e){
    e.preventDefault();
    const reply = $('#reply').val().trim();
    if(!reply){ toastr.warning('Reply is required'); return; }
    const $btn = $(this).find('button[type=submit]').prop('disabled', true).text('Sending...');
    $.post("{{ route('admin.affiliate.support.reply', $ticket->id) }}", {_token: "{{ csrf_token() }}", reply}, function(res){
      if(res.status === 'success'){
        toastr.success(res.msg || 'Reply sent');
        $('#admin-reply-area').html('<div class="alert alert-success">'+ $('<div/>').text(reply).html().replace(/\n/g,'<br>') +'</div>');
        $('#reply').val('');
      } else {
        toastr.warning(res.msg || 'Failed');
      }
    }).fail(()=> toastr.error('Server error'))
    .always(()=> $btn.prop('disabled', false).text('Send Reply & Mark Resolved'));
  });

})(jQuery);
</script>
@endsection
