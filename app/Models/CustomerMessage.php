<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerMessage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'message_id',
        'customer_id',
        'name',
        'email',
        'subject',
        'message',
        'category',
        'status',
        'priority',
        'department',
        'admin_reply',
        'replied_at',
    ];
    
    protected $casts = [
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the customer that owns the message.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'c_id');
    }
    
    /**
     * Generate a unique message ID.
     */
    public static function generateMessageId(): string
    {
        do {
            $id = 'MSG-' . strtoupper(uniqid());
        } while (self::where('message_id', $id)->exists());
        
        return $id;
    }
}