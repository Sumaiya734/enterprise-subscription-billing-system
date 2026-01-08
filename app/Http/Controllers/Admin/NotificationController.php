<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $customers = Customer::select('c_id', 'name')->get();
        $users = User::select('id', 'name', 'email')->get();
        return view('admin.notifications.create', compact('customers', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'customer_id' => 'nullable|exists:customers,c_id',
            'send_to_all' => 'nullable|boolean',
        ]);

        $userIds = [];

        if ($request->filled('send_to_all')) {
            // Send to all users
            $userIds = User::pluck('id')->toArray();
        } elseif ($request->filled('customer_id')) {
            // Find user associated with the customer
            $customer = Customer::find($request->customer_id);
            if ($customer && $customer->user_id) {
                $userIds = [$customer->user_id];
            }
        } elseif ($request->filled('user_id')) {
            $userIds = [$request->user_id];
        }

        if (!empty($userIds)) {
            foreach ($userIds as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'title' => $request->title,
                    'message' => $request->message,
                    'is_read' => false,
                ]);
            }

            $recipientCount = count($userIds);
            $recipientText = $request->filled('send_to_all') ? 'all users' : 
                           ($recipientCount == 1 ? '1 user' : $recipientCount . ' users');

            return redirect()->route('admin.notifications.index')
                ->with('success', "Notification sent successfully to {$recipientText}!");
        }

        return redirect()->back()
            ->with('error', 'No recipients selected for the notification.');
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }
}