<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PublicInformationItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'public_information_item';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'period_date',
        'attachment',
        'value',
        'public_information_id',
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

    public function public_information() {
        return $this->belongsTo(PublicInformation::class, 'public_information_id', 'id');
    }
}
