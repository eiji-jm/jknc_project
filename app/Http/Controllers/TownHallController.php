<?php

namespace App\Http\Controllers;

use App\Models\TownHallCommunication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class TownHallController extends Controller
{
    public function index()
    {
        $communications = TownHallCommunication::latest()->paginate(10);

        return view('townhall.townhall', compact('communications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'communication_date' => ['nullable', 'date'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'department_stakeholder' => ['nullable', 'string', 'max:255'],
            'to_for' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'cc' => ['nullable', 'string', 'max:255'],
            'additional' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx', 'max:5120'],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('townhall_attachments', 'public');
        }

        $validated['created_by'] = Auth::id();

        $communication = TownHallCommunication::create($validated);

        $communication->ref_no = 'TH-' . str_pad((string) $communication->id, 5, '0', STR_PAD_LEFT);
        $communication->save();

        return redirect()
            ->route('townhall')
            ->with('success', 'Town Hall communication created successfully.');
    }

    public function show($id)
    {
        $communication = TownHallCommunication::findOrFail($id);

        $attachmentType = null;

        if ($communication->attachment) {
            $ext = strtolower(pathinfo($communication->attachment, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'])) {
                $attachmentType = 'image';
            } elseif ($ext === 'pdf') {
                $attachmentType = 'pdf';
            } else {
                $attachmentType = 'file';
            }
        }

        return view('townhall.show', compact('communication', 'attachmentType'));
    }
}
