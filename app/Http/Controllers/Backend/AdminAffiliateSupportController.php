<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AffiliateSupport;
use App\Models\AffiliateProgram;
use Illuminate\Support\Facades\Mail;
use App\Mail\BasicMail;
use Illuminate\Support\Facades\Validator;

class AdminAffiliateSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = AffiliateSupport::with('affiliate')->orderBy('created_at','desc');

        if ($request->filled('status') && in_array($request->status, ['pending','open','resolved','closed'])) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(25)->withQueryString();
        return view('backend.pages.affilate-support.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = AffiliateSupport::with('affiliate')->findOrFail($id);
        return view('backend.pages.affilate-support.show', compact('ticket'));
    }

    // reply by admin (AJAX)
    public function reply(Request $request, $id)
    {
        $ticket = AffiliateSupport::findOrFail($id);

        $v = Validator::make($request->all(), [
            'reply' => 'required|string'
        ]);

        if ($v->fails()) {
            return response()->json(['status'=>'error','msg'=>$v->errors()->first()], 422);
        }

        $ticket->admin_reply = $request->reply;
        $ticket->status = 'resolved';
        $ticket->save();

        // send email to affiliate if exists
        if ($ticket->affiliate && $ticket->affiliate->email) {
            try {
                Mail::to($ticket->affiliate->email)->send(new BasicMail([
                    'subject' => 'Reply to your support request: '.$ticket->subject,
                    'message' => $request->reply
                ]));
            } catch (\Exception $e) {
                \Log::error('Support reply mail failed: '.$e->getMessage());
            }
        }

        return response()->json(['status'=>'success','msg'=>'Reply sent and ticket marked resolved.']);
    }

    // change status (AJAX)
    public function changeStatus(Request $request, $id)
    {
        $ticket = AffiliateSupport::findOrFail($id);
        $new = $request->get('status');
        if (!in_array($new, ['pending','open','resolved','closed'])) {
            return response()->json(['status'=>'error','msg'=>'Invalid status'], 422);
        }
        $ticket->status = $new;
        $ticket->save();
        return response()->json(['status'=>'success','msg'=>'Status updated.']);
    }

    public function destroy(Request $request, $id)
    {
        $ticket = AffiliateSupport::findOrFail($id);
        $ticket->delete();
        return redirect()->route('admin.affiliate.support.index')->with('success','Ticket deleted.');
    }
}
