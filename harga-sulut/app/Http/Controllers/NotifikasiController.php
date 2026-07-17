<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifs = auth()->user()->notifikasis()
            ->latest()
            ->paginate(20);

        auth()->user()->notifikasis()->unread()->update(['is_read' => true, 'read_at' => now()]);

        return view('notifikasi.index', compact('notifs'));
    }

    public function markRead(int $id): JsonResponse
    {
        $notif = Notifikasi::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $notif->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        auth()->user()->notifikasis()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function unreadCount(): JsonResponse
    {
        $notifs = auth()->user()->notifikasis()
            ->where('is_read', false)
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'judul'    => $n->judul,
                'pesan'    => $n->pesan,
                'tipe'     => $n->tipe,
                'url'      => $n->url,
                'is_read'  => $n->is_read,
                'time_ago' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'count'  => auth()->user()->unread_notif_count,
            'notifs' => $notifs,
        ]);
    }
}
