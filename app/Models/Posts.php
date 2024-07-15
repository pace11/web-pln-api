<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Posts extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'slug',
        'title',
        'description',
        'thumbnail',
        'posted',
        'banner',
        'status',
        'checked_by_date',
        'checked_by_email',
        'approved_by_date',
        'approved_by_email',
        'rejected_by_date',
        'rejected_by_email',
        'remarks',
        'categories_id',
        'unit_id',
        'users_id',
    ];
    protected $guard = [
        'created_at', 'updated_at'
    ];
    protected $appends = ['is_own_post', 'is_superadmin', 'is_admin', 'is_creator', 'is_checker', 'is_approver'];
    protected $dates = [
        'deleted_at'
    ];
    protected $casts = [
        'posted' => 'boolean',
        'banner' => 'boolean',
    ];

    public $timestamps = true;

    public function categories() {
        return $this->belongsTo(Categories::class, 'categories_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function getIsOwnPostAttribute() {
        $user = Auth::guard('api')->user();
        $is_own_post = $this->user->id == $user->id ?? false;
        return $is_own_post;
    }

    public function getIsSuperadminAttribute() {
        $user = Auth::guard('api')->user();
        $is_superadmin = $user->type == 'superadmin' ?? false;
        return $is_superadmin;
    }

    public function getIsAdminAttribute() {
        $user = Auth::guard('api')->user();
        $is_admin = $user->type == 'admin' ?? false;
        return $is_admin;
    }

    public function getIsCreatorAttribute() {
        $user = Auth::guard('api')->user();
        $is_creator = $user->type == 'creator' ?? false;
        return $is_creator;
    }

    public function getIsCheckerAttribute() {
        $user = Auth::guard('api')->user();
        $is_checker = $user->type == 'checker' ?? false;
        return $is_checker;
    }

    public function getIsApproverAttribute() {
        $user = Auth::guard('api')->user();
        $is_approver = $user->type == 'approver' ?? false;
        return $is_approver;
    }
}
