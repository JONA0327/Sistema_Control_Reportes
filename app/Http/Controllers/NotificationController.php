<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function redirect(Request $request, string $notification)
    {
        $notif = $request->user()->notifications()->findOrFail($notification);
        $notif->markAsRead();

        return redirect($notif->data['url'] ?? route('dashboard'));
    }

    public function leerTodas(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
