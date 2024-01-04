<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Equipment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipment';

    protected $casts = [
        'parameters' => 'array',
    ];

    // Relationships

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'rooms_equipment', 'equipment_id', 'room_id')
            ->withPivot('amount');
    }

    // Detail attributes
    
    public function getAmountSumAttribute()
    {
        return $this->rooms()->sum('amount');
    }
}
