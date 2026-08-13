<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = ['ticket_id', 'user_id', 'quantity', 'total_price', 'status'];
    // Define the relationship between Order and Ticket models // step1
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
    // Define the relationship between Order and User models // step1

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    protected $casts = [
    'total_price' => 'decimal:2',
];
}



