<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use Illuminate\Http\Request;

class FamilyMemberController extends Controller
{
    private array $allowedAllergies = [
        'gluten',
        'lactose',
        'nuts',
        'pork',
        'egg',
        'soy',
        'sesame',
        'shellfish',
        'halal',
    ];

    public function index()
    {
        $members = auth()->user()->familyMembers()
            ->with(['allergyProfiles', 'budget'])
            ->latest()
            ->get();

        return view('family.index', [
            'members' => $members,
            'allergyOptions' => $this->allowedAllergies,
            'activeMemberId' => session('active_member_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateMember($request);

        $member = auth()->user()->familyMembers()->create([
            'name_member' => $validated['name_member'],
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'avatar' => $request->hasFile('avatar')
                ? famshopStorePublicUpload($request->file('avatar'), 'uploads/avatars')
                : null,
        ]);

        $this->syncAllergiesAndBudget($member, $validated);

        if (! session('active_member_id')) {
            session(['active_member_id' => $member->id]);
        }

        return redirect('/family')->with('success', 'Family member added successfully.');
    }

    public function edit($id)
    {
        $member = auth()->user()->familyMembers()
            ->with(['allergyProfiles', 'budget'])
            ->findOrFail($id);

        return view('family.edit', [
            'member' => $member,
            'allergyOptions' => $this->allowedAllergies,
        ]);
    }

    public function update(Request $request, $id)
    {
        $member = auth()->user()->familyMembers()->findOrFail($id);
        $validated = $this->validateMember($request);

        $member->update([
            'name_member' => $validated['name_member'],
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'avatar' => $request->hasFile('avatar')
                ? famshopStorePublicUpload($request->file('avatar'), 'uploads/avatars')
                : $member->avatar,
        ]);

        $this->syncAllergiesAndBudget($member, $validated);

        return redirect('/family')->with('success', 'Family member updated successfully.');
    }

    public function destroy($id)
    {
        $member = auth()->user()->familyMembers()->findOrFail($id);
        $memberId = $member->id;
        $member->delete();

        if (session('active_member_id') == $memberId) {
            session()->forget('active_member_id');
        }

        return redirect('/family')->with('success', 'Family member deleted successfully.');
    }

    private function validateMember(Request $request): array
    {
        return $request->validate([
            'name_member' => ['required', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'between:0,120'],
            'gender' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'allergies' => ['nullable', 'array'],
            'allergies.*' => ['in:' . implode(',', $this->allowedAllergies)],
            'severity_level' => ['nullable', 'in:mild,moderate,severe'],
            'daily_budget' => ['nullable', 'numeric', 'min:0'],
            'weekly_budget' => ['nullable', 'numeric', 'min:0'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function syncAllergiesAndBudget(FamilyMember $member, array $validated): void
    {
        $member->allergyProfiles()->delete();

        foreach ($validated['allergies'] ?? [] as $allergy) {
            $member->allergyProfiles()->create([
                'allergy_type' => $allergy,
                'severity_level' => $validated['severity_level'] ?? 'moderate',
            ]);
        }

        $member->budget()->updateOrCreate(
            ['member_id' => $member->id],
            [
                'daily_budget' => $validated['daily_budget'] ?? 0,
                'weekly_budget' => $validated['weekly_budget'] ?? 0,
                'monthly_budget' => $validated['monthly_budget'] ?? 0,
            ]
        );
    }
}
