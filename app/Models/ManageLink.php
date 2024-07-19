<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ManageLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'manage_link';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'key', 'title', 'period', 'url', 'active'
    ];
    protected $guard = [
        'created_at', 'updated_at'
    ];
    protected $appends = [
        'is_superadmin',
    ];
    protected $dates = [
        'deleted_at'
    ];
    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;

    public function getIsSuperadminAttribute() {
        $user = Auth::guard('api')->user();
        $is_superadmin = $user->type == 'superadmin' ?? false;
        return $is_superadmin;
    }
}
