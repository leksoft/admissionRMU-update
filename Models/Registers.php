<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registers extends Model
{
    public $timestamps = false;
    protected $table = 'register';
    protected $primaryKey = 'reg_id';
    protected $fillable = [
        'reg_student',
        'reg_project',
        'reg_year',
        'reg_course',
        'reg_status_register',
        'reg_status_pay',
        'reg_status_exam',
        'reg_status_exam_no',
        'reg_status_exam_no_status',
        'reg_status_confirm',
        'reg_code',
        'reg_datetime',
        'reg_date',
        'reg_status',
        'reg_history',
    ];
}
