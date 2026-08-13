<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'type', 'price', 'quantity_available'];
     protected $casts = [
        'price' => 'decimal:2',
    ];
    
    // Define the relationship between Ticket and Event models // step1
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
   // Define the relationship between Ticket and Order models // step1
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}