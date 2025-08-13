<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Talent extends Model
{
    use HasFactory;

     protected $table = 'talents';

      protected $fillable = [
        'talent_type_id',
        'talent_code',
        'talent_name',
    ];

    protected $casts = [
        'talent_type_id' => 'integer',
        'talent_code' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function talentType(): BelongsTo
    {
        return $this->belongsTo(TalentType::class, 'talent_type_id');
    }

     public function studentTalents(): HasMany
    {
        return $this->hasMany(StudentTalent::class, 'talent_id');
    }
}
