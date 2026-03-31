<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbackQuery = auth()->user()->feedback()->latest();
        $feedbackItems = $feedbackQuery->get();
        $ratings = $feedbackItems->whereNotNull('rating');

        return view('support.feedback', [
            'feedbackItems' => $feedbackItems->take(8),
            'feedbackStats' => [
                'total' => $feedbackItems->count(),
                'average_rating' => $ratings->count() > 0 ? round($ratings->avg('rating'), 1) : null,
                'bugs' => $feedbackItems->where('type', 'bug')->count(),
                'suggestions' => $feedbackItems->where('type', 'suggestion')->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:rating,suggestion,bug'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'rating' => $validated['rating'] ?? null,
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Feedback sent successfully.');
    }

    public function destroy($id)
    {
        auth()->user()->feedback()->findOrFail($id)->delete();

        return back()->with('success', 'Feedback deleted successfully.');
    }
}
