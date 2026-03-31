<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminSupportController extends Controller
{
    public function index()
    {
        return view('admin.support', [
            'tickets' => SupportTicket::with('user')->latest('ticket_date')->get(),
            'hasReplyColumn' => Schema::hasColumn('support_tickets', 'reply_message'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $rules = [
            'status' => ['required', 'in:open,in_progress,closed'],
        ];

        if (Schema::hasColumn('support_tickets', 'reply_message')) {
            $rules['reply_message'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        $ticket->status = $validated['status'];

        if (array_key_exists('reply_message', $validated)) {
            $ticket->reply_message = $validated['reply_message'];
        }

        $ticket->save();

        return back()->with('success', 'Support ticket updated successfully.');
    }

    public function destroy($id)
    {
        SupportTicket::findOrFail($id)->delete();

        return back()->with('success', 'Support ticket deleted successfully.');
    }
}
