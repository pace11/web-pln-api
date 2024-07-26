<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class InternalCommunicationItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'internal_communication_item';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'period_date',
        'attachment',
        'realization',
        'value',
        'internal_communication_id',
        'unit_id',
        'users_id'
    ];
    protected $guard = [
        'created_at', 'updated_at'
    ];
    protected $dates = [
        'deleted_at'
    ];

    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function internal_communication() {
        return $this->belongsTo(InternalCommunication::class, 'internal_communication_id', 'id');
    }
}
