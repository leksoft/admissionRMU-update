<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TalentType extends Model
{
    use HasFactory;
    protected $table = 'talent_types';

     protected $fillable = [
        'talent_type_name',
    ];

     protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     public function talents(): HasMany
    {
        return $this->hasMany(Talent::class, 'talent_type_id');
    }

}
