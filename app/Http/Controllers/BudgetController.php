<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\FamilyMember;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function edit($memberId)
    {
        $member = FamilyMember::with('budget')
            ->where('user_id', auth()->id())
            ->findOrFail($memberId);

        return view('budget.edit', [
            'member' => $member,
            'budget' => $member->budget,
        ]);
    }

    public function update(Request $request, $memberId)
    {
        $member = FamilyMember::where('user_id', auth()->id())->findOrFail($memberId);
        $validated = $request->validate([
            'daily_budget' => ['required', 'numeric', 'min:0'],
            'weekly_budget' => ['required', 'numeric', 'min:0'],
            'monthly_budget' => ['required', 'numeric', 'min:0'],
        ]);

        $member->budget()->updateOrCreate(
            ['member_id' => $member->id],
            $validated
        );

        return back()->with('success', 'Budget updated successfully.');
    }

    public function checkBudget(?float $price, ?Budget $budget): array
    {
        if (! $budget || ! $price) {
            return ['within_budget' => true, 'exceeded_period' => null];
        }

        $dailyRemaining = $budget->daily_budget - $budget->daily_spent;
        $weeklyRemaining = $budget->weekly_budget - $budget->weekly_spent;
        $monthlyRemaining = $budget->monthly_budget - $budget->monthly_spent;

        if ($price > $dailyRemaining) {
            return ['within_budget' => false, 'exceeded_period' => 'daily budget'];
        }

        if ($price > $weeklyRemaining) {
            return ['within_budget' => false, 'exceeded_period' => 'weekly budget'];
        }

        if ($price > $monthlyRemaining) {
            return ['within_budget' => false, 'exceeded_period' => 'monthly budget'];
        }

        return ['within_budget' => true, 'exceeded_period' => null];
    }
}
