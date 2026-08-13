<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'venue', 'city', 'starts_at'];

    protected $casts = ['starts_at' => 'datetime'];

    public function tickets(): HasMany
    {
        // Define the relationship between Event and Ticket models //step 1
        return $this->hasMany(Ticket::class);
    }
}