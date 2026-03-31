<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        return view('support.index', [
            'openTicketsCount' => auth()->user()->supportTickets()->where('status', 'open')->count(),
            'feedbackCount' => auth()->user()->feedback()->count(),
        ]);
    }

    public function tickets()
    {
        $tickets = auth()->user()->supportTickets()->latest('ticket_date')->get();

        return view('support.tickets', [
            'tickets' => $tickets,
            'ticketStats' => [
                'total' => $tickets->count(),
                'open' => $tickets->where('status', 'open')->count(),
                'resolved' => $tickets->where('status', 'resolved')->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        SupportTicket::create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'status' => 'open',
            'ticket_date' => now(),
        ]);

        return back()->with('success', 'Support ticket sent successfully.');
    }

    public function destroy($id)
    {
        auth()->user()->supportTickets()->findOrFail($id)->delete();

        return back()->with('success', 'Support ticket deleted successfully.');
    }
}
