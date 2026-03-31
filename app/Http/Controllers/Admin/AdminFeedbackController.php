<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;

class AdminFeedbackController extends Controller
{
    public function index()
    {
        return view('admin.feedback', [
            'feedbackItems' => Feedback::with('user')->latest()->get(),
        ]);
    }

    public function destroy($id)
    {
        Feedback::findOrFail($id)->delete();

        return back()->with('success', 'Feedback deleted successfully.');
    }
}
