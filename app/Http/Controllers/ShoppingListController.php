<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use App\Models\ShoppingList;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function index()
    {
        $members = auth()->user()->familyMembers()->get();
        $activeMemberId = session('active_member_id');
        $items = collect();

        if ($activeMemberId) {
            FamilyMember::where('user_id', auth()->id())->findOrFail($activeMemberId);
            $items = ShoppingList::where('member_id', $activeMemberId)->latest()->get();
        }

        return view('list.index', [
            'members' => $members,
            'items' => $items,
            'activeMemberId' => $activeMemberId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'integer'],
            'item_name' => ['required', 'string', 'max:255'],
        ]);

        FamilyMember::where('user_id', auth()->id())->findOrFail($validated['member_id']);

        ShoppingList::create($validated);

        return back()->with('success', 'Shopping list item added.');
    }

    public function toggle($id)
    {
        $item = ShoppingList::whereHas('member', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);

        $item->update(['is_checked' => ! $item->is_checked]);

        return back()->with('success', 'Shopping list updated.');
    }

    public function destroy($id)
    {
        ShoppingList::whereHas('member', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id)->delete();

        return back()->with('success', 'Shopping list item deleted.');
    }
}
