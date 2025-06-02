<?php

namespace App\Http\Controllers;

use App\Models\PageLock;
use Illuminate\Http\Request;

class LockController extends Controller
{
    //

    public function manageLock()
    {
        $locks = PageLock::latest()->get();
        return view("manage-lock", compact("locks"));
    }




    public function createLock(Request $request)
    {
        $request->validate([
            'page' => 'required|string',
            'from' => 'required|date',
            'to' => 'required|date|after:from',
        ]);

        // Check overlapping locks for same page
        $overlapping = PageLock::where('page', $request->page)
            ->where(function ($query) use ($request) {
                $query->whereBetween('from', [$request->from, $request->to])
                    ->orWhereBetween('to', [$request->from, $request->to])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('from', '<=', $request->from)
                            ->where('to', '>=', $request->to);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->with('error', 'Overlapping lock already exists for this page and time range.');
        }

        PageLock::create([
            'page' => $request->page,
            'from' => $request->from,
            'to' => $request->to,
            'locked' => $request->has('locked')
        ]);

        return back()->with('success', 'Page locked successfully');
    }



    public function updateLock(Request $request, $id)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after:from',
        ]);

        $lock = PageLock::findOrFail($id);
        $lock->from = $request->from;
        $lock->to = $request->to;
        $lock->locked = $request->locked ? 1 : 0;
        $lock->save();

        return response()->json(['message' => 'Updated successfully']);
    }


    public function deleteLock($id)
    {
        $lock = PageLock::findOrFail($id);
        $lock->delete(); 

        return response()->json(['message' => 'Lock deleted successfully']);
    }

}
