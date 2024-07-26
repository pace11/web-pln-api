<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AccountInfluencerItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'account_influencer_item';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'attachment',
        'realization',
        'value',
        'account_influencer_id',
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

    public function account_influencer() {
        return $this->belongsTo(AccountInfluencer::class, 'account_influencer_id', 'id');
    }
}
