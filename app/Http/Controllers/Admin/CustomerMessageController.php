<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerMessage;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerMessageController extends Controller
{
    /**
     * Display a listing of customer messages
     */
    public function index(Request $request)
    {
        try {
            $query = CustomerMessage::with(['customer.user']);
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }
            
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('message_id', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Get messages with pagination
            $messages = $query->latest()->paginate(15);
            
            // Get statistics
            $stats = [
                'total' => CustomerMessage::count(),
                'open' => CustomerMessage::where('status', 'open')->count(),
                'in_progress' => CustomerMessage::where('status', 'in_progress')->count(),
                'resolved' => CustomerMessage::where('status', 'resolved')->count(),
                'urgent' => CustomerMessage::where('priority', 'urgent')->count(),
            ];
            
            return view('admin.customer-messages.index', compact('messages', 'stats'));
            
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('CustomerMessage index error: ' . $e->getMessage());
            
            // Return with error message
            return view('admin.customer-messages.index', [
                'messages' => collect([]),
                'stats' => [
                    'total' => 0,
                    'open' => 0,
                    'in_progress' => 0,
                    'resolved' => 0,
                    'urgent' => 0,
                ]
            ])->with('error', 'Unable to load messages. Please try again.');
        }
    }
    
    /**
     * Update the status of a customer message
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        try {
            $message = CustomerMessage::findOrFail($id);
            $message->status = $request->status;
            
            if ($request->filled('notes')) {
                $message->admin_reply = $request->notes;
                $message->replied_at = now();
            }
            
            $message->save();
            
            return redirect()->back()->with('success', 'Message status updated successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update message status.');
        }
    }
    
    /**
     * Show a specific customer message
     */
    public function show($id)
    {
        try {
            $message = CustomerMessage::with(['customer'])->findOrFail($id);
            
            return view('admin.customer-messages.show', compact('message'));
        } catch (\Exception $e) {
            return redirect()->route('admin.customer-messages.index')
                ->with('error', 'Customer message not found.');
        }
    }
    
    /**
     * Reply to a customer message
     */
    public function reply(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:open,in_progress,resolved,closed',
                'admin_reply' => 'required|string|max:1000'
            ]);
            
            $message = CustomerMessage::findOrFail($id);
            $message->status = $request->status;
            $message->admin_reply = $request->admin_reply;
            $message->replied_at = now();
            $message->save();
            
            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Reply sent successfully!']);
            } else {
                return redirect()->back()->with('success', 'Reply sent successfully!');
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
            } else {
                return redirect()->back()->with('error', 'Validation failed: ' . json_encode($e->errors()));
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('CustomerMessage reply error: ' . $e->getMessage(), [
                'user_id' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null,
                'message_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to send reply.'], 500);
            } else {
                return redirect()->back()->with('error', 'Failed to send reply.');
            }
        }
    }
    
    /**
     * Get recent customer messages for dashboard widget
     */
    public function getRecentMessages(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            
            $messages = CustomerMessage::with(['customer'])
                ->latest()
                ->take($limit)
                ->get();
                
            return response()->json($messages);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch messages'], 500);
        }
    }
}