<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    // Relationships

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'rooms_equipment', 'room_id', 'equipment_id')
            ->withPivot('amount');
    }
}
