<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Modules\EBilling\Enums\NotificationType;

class NotificationController
{
    public function index(Request $request)
    {
        $user = $request->user('ebil');
        $notifications = $user
            ->notifications()
            ->when($request->input('filter') === 'unread', function ($query) {
                return $query->whereNull('read_at');
            })
            ->paginate(10);

        return view('e-billing::notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user('ebil');

        $notification = $user->notifications()->where('id', $id)->first();
        $type = NotificationType::tryFrom($notification->type);

        if (! $notification || ! $type) {
            return abort(404);
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        if ($type === NotificationType::INVOICE_PAID) {
            return redirect()->route('e-billing.invoices.show', ['invoice' => $notification->data['invoice_id']]);
        }

        return redirect()->back();
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user('ebil');
        $user->unreadNotifications->markAsRead();

        return redirect()->back();
    }

    public function clear(Request $request)
    {
        $user = $request->user('ebil');
        $user->notifications()->delete();

        return redirect()->back()->with('success', 'Semua notifikasi telah dihapus.');
    }
}
