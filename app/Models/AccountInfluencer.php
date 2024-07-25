<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AccountInfluencer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'account_influencer';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'period_date', 'target', 'users_id'
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
}
