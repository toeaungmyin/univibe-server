<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $unreadNotifications = Auth::user()->unreadNotifications()
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Retrieved 20 latest unread notifications',
            'notifications' => $unreadNotifications,
        ], 200);
    }

    public function markAsRead($notificationId)
    {
        $notifications = Auth::user()->unreadNotifications;

        foreach ($notifications as $notification) {
            if ($notification->id == $notificationId) {
                $notification->markAsRead();
                return response()->json(['message' => 'Notification marked as read successfully']);
            }
        }

        return response()->json(['error' => 'Notification not found'], 404);
    }
}
