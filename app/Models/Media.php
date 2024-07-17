<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'url',
        'caption',
        'target_post',
        'status',
        'checked_by_date',
        'checked_by_email',
        'checked_by_remarks',
        'final_checked_by_date',
        'final_checked_by_email',
        'final_checked_by_remarks',
        'approved_by_date',
        'approved_by_email',
        'approved_by_remarks',
        'final_approved_by_date',
        'final_approved_by_email',
        'final_approved_by_remarks',
        'rejected_by_date',
        'rejected_by_email',
        'rejected_by_remarks',
        'final_rejected_by_date',
        'final_rejected_by_email',
        'final_rejected_by_remarks',
        'unit_id',
        'users_id',
    ];
    protected $guard = [
        'created_at', 'updated_at'
    ];
    protected $appends = [
        'is_own_post',
        'is_superadmin',
        'is_creator',
        'is_final_creator',
        'is_checker',
        'is_final_checker',
        'is_approver',
        'is_final_approver'
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

    public function getIsCreatorAttribute() {
        $user = Auth::guard('api')->user();
        $is_creator = $user->placement == 'executor_unit' && $user->type == 'creator' ?? false;
        return $is_creator;
    }

    public function getIsFinalCreatorAttribute() {
        $user = Auth::guard('api')->user();
        $is_creator = $user->placement == 'main_office' && $user->type == 'creator' ?? false;
        return $is_creator;
    }

    public function getIsCheckerAttribute() {
        $user = Auth::guard('api')->user();
        $is_checker = $user->placement == 'executor_unit' && $user->type == 'checker' ?? false;
        return $is_checker;
    }

    public function getIsFinalCheckerAttribute() {
        $user = Auth::guard('api')->user();
        $is_creator = $user->placement == 'main_office' && $user->type == 'checker' ?? false;
        return $is_creator;
    }

    public function getIsApproverAttribute() {
        $user = Auth::guard('api')->user();
        $is_approver = $user->placement == 'executor_unit' && $user->type == 'approver' ?? false;
        return $is_approver;
    }

    public function getIsFinalApproverAttribute() {
        $user = Auth::guard('api')->user();
        $is_approver = $user->placement == 'main_office' && $user->type == 'approver' ?? false;
        return $is_approver;
    }
}
