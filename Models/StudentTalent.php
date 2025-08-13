<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTalent extends Model
{
    use HasFactory;
    protected $table = 'student_talents';

     protected $fillable = [
        'talent_id',
        'reg_student',
        'skill_level',
        'awards',
        'experiences',
    ];

     protected $casts = [
        'talent_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


     /**
     * Constants for skill levels
     */
    const SKILL_LEVELS = [
        'ระดับโรงเรียน' => 'ระดับโรงเรียน',
        'ระดับจังหวัด' => 'ระดับจังหวัด',
        'ระดับภาค' => 'ระดับภาค',
        'ระดับประเทศ' => 'ระดับประเทศ',
        'ระดับนานาชาติ' => 'ระดับนานาชาติ',
        'อื่นๆ' => 'อื่นๆ',
    ];

    /**
     * Constants for awards
     */
    const AWARDS = [
        'เหรียญทอง' => 'เหรียญทอง',
        'เหรียญเงิน' => 'เหรียญเงิน',
        'เหรียญทองแดง' => 'เหรียญทองแดง',
        'โล่รางวัล' => 'โล่รางวัล',
        'ใบประกาศนียบัตร' => 'ใบประกาศนียบัตร',
        'รางวัลชนะเลิศ' => 'รางวัลชนะเลิศ',
        'รางวัลรองชนะเลิศอันดับ 1' => 'รางวัลรองชนะเลิศอันดับ 1',
        'รางวัลรองชนะเลิศอันดับ 2' => 'รางวัลรองชนะเลิศอันดับ 2',
        'รางวัลชมเชย' => 'รางวัลชมเชย',
        'อื่นๆ' => 'อื่นๆ',
    ];

    /**
     * Get the talent that owns the student talent.
     */
    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class, 'talent_id');
    }

    /**
     * Scope a query to only include talents of a given level.
     */
    public function scopeBySkillLevel($query, $level)
    {
        return $query->where('skill_level', $level);
    }

    /**
     * Scope a query to only include talents with a given award.
     */
    public function scopeByAward($query, $award)
    {
        return $query->where('awards', $award);
    }

    public function scopeByStudent($query, $regStudent)
    {
        return $query->where('reg_student', $regStudent);
    }

    /**
     * Get the skill level display name.
     */
    public function getSkillLevelDisplayAttribute()
    {
        return self::SKILL_LEVELS[$this->skill_level] ?? $this->skill_level;
    }

    /**
     * Get the award display name.
     */
    public function getAwardDisplayAttribute()
    {
        return self::AWARDS[$this->awards] ?? $this->awards;
    }


// ดึงข้อมูลความสามารถของนักเรียนพร้อมข้อมูลความสามารถ
#$studentTalents = StudentTalent::with(['talent', 'talent.talentType'])->get();

// ค้นหาความสามารถของนักเรียนคนใดคนหนึ่ง
#$studentTalents = StudentTalent::byStudent('6501001')->with('talent')->get();

// ค้นหาตามระดับความสามารถ
#$nationalLevelTalents = StudentTalent::bySkillLevel('ระดับประเทศ')->get();

// ค้นหาตามรางวัล
#$goldMedalTalents = StudentTalent::byAward('เหรียญทอง')->get();

}
