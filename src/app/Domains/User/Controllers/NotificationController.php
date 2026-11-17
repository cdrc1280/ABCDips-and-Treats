<?php

namespace App\Domains\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

/** The back-office notification bell (JSON; the unread count is shared as an Inertia prop). */
class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'items' => $user->notifications()->latest()->limit(20)->get()->map(fn (DatabaseNotification $n) => self::present($n))->all(),
            'unread' => $user->unreadNotifications()->count(),
        ]);
    }

    public function read(Request $request, string $notification): JsonResponse
    {
        $request->user()->notifications()->whereKey($notification)->firstOrFail()->markAsRead();

        return response()->json(['unread' => $request->user()->unreadNotifications()->count()]);
    }

    public function readAll(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['unread' => 0]);
    }

    /**
     * Also reads rows written by the old Filament panel (title/body + actions[].url).
     *
     * @return array{id: string, title: string, body: string, url: ?string, read: bool, created_at: string}
     */
    private static function present(DatabaseNotification $n): array
    {
        $data = $n->data;
        $url = $data['url'] ?? ($data['actions'][0]['url'] ?? null);
        if (is_string($url) && str_contains($url, '/filament')) {
            $url = null;
        }

        return [
            'id' => $n->id,
            'title' => (string) ($data['title'] ?? __('Notification')),
            'body' => trim(str_replace('**', '', (string) ($data['body'] ?? ''))),
            'url' => is_string($url) ? $url : null,
            'read' => $n->read_at !== null,
            'created_at' => $n->created_at?->toIso8601String() ?? '',
        ];
    }
}
