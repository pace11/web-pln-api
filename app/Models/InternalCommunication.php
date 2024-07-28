<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\CustomHelpers;

class InternalCommunication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'internal_communication';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'period_date', 'target', 'users_id'
    ];
    protected $guard = [
        'created_at', 'updated_at'
    ];
    protected $appends = [
        'date_status',
    ];
    protected $dates = [
        'deleted_at'
    ];

    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function getDateStatusAttribute() {
        return CustomHelpers::isPreviousDate($this->period_date);
    }
}
