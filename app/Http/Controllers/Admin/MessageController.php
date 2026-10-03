<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Support\WhatsApp;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::latest();

        if (in_array($request->status, ContactMessage::STATUSES, true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $term = '%' . trim($request->q) . '%';
            $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('message', 'like', $term));
        }

        $messages = $query->paginate(20)->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        $whatsappUrl = WhatsApp::replyUrl($message->phone, $message->name, $message->locale);

        return view('admin.messages.show', compact('message', 'whatsappUrl'));
    }

    public function update(Request $request, ContactMessage $message)
    {
        $request->validate(['status' => ['required', 'in:' . implode(',', ContactMessage::STATUSES)]]);
        $message->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }

    public function export()
    {
        $rows = ContactMessage::latest()->get();

        // Spreadsheet apps run cells starting with = + - @ as formulas, and visitors type these fields freely.
        $safe = fn ($v) => is_string($v) && $v !== '' && str_contains('=+-@', $v[0]) ? "'" . $v : $v;

        return response()->streamDownload(function () use ($rows, $safe) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['id', 'received', 'status', 'name', 'email', 'phone', 'service', 'budget', 'message']);

            foreach ($rows as $m) {
                fputcsv($out, array_map($safe, [
                    $m->id, $m->created_at->toDateTimeString(), $m->status, $m->name, $m->email,
                    $m->phone, $m->service, $m->budget, $m->message,
                ]));
            }

            fclose($out);
        }, 'wmstudios-messages-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
