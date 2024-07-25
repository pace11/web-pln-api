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
        'recreated',
        'checked_by_date',
        'checked_by_email',
        'checked_by_remarks',
        'final_checked_by_date',
        'final_checked_by_email',
        'final_checked_by_remarks',
        'final_created_by_date',
        'final_created_by_email',
        'final_created_by_remarks',
        'approved_by_date',
        'approved_by_email',
        'approved_by_remarks',
        'final_approved_by_date',
        'final_approved_by_email',
        'final_approved_by_remarks',
        'final_approved_2_by_date',
        'final_approved_2_by_email',
        'final_approved_2_by_remarks',
        'final_approved_3_by_date',
        'final_approved_3_by_email',
        'final_approved_3_by_remarks',
        'rejected_by_date',
        'rejected_by_email',
        'rejected_by_remarks',
        'final_rejected_by_date',
        'final_rejected_by_email',
        'final_rejected_by_remarks',
        'final_rejected_2_by_date',
        'final_rejected_2_by_email',
        'final_rejected_2_by_remarks',
        'final_rejected_3_by_date',
        'final_rejected_3_by_email',
        'final_rejected_3_by_remarks',
        'categories_id',
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
        'is_checker',
        'is_approver',
        'is_approver_2',
        'is_approver_3',
    ];
    protected $dates = [
        'deleted_at'
    ];
    protected $casts = [
        'posted' => 'boolean',
        'banner' => 'boolean',
        'recreated' => 'boolean',
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

    public function getIsCreatorAttribute() {
        $user = Auth::guard('api')->user();

        if ($user->placement == 'main_office' && $user->type == 'creator') $is_creator = true;

        return $is_creator ?? false;
    }

    public function getIsCheckerAttribute() {
        $user = Auth::guard('api')->user();

        if ($user->placement == 'executor_unit' && $user->type == 'checker') $is_checker = true;

        if ($user->placement == 'main_office' && $user->type == 'checker') $is_checker = true;

        return $is_checker ?? false;
    }

    public function getIsApproverAttribute() {
        $user = Auth::guard('api')->user();

        if ($user->placement == 'executor_unit' && $user->type == 'approver') $is_approver = true;

        if ($user->placement == 'main_office' && $user->type == 'approver') $is_approver = true;

        return $is_approver ?? false;
    }

    public function getIsApprover2Attribute() {
        $user = Auth::guard('api')->user();

        if ($user->placement == 'main_office' && $user->type == 'approver_2') $is_approver_2 = true;

        return $is_approver_2 ?? false;
    }

    public function getIsApprover3Attribute() {
        $user = Auth::guard('api')->user();

        if ($user->placement == 'main_office' && $user->type == 'approver_3') $is_approver_3 = true;

        return $is_approver_3 ?? false;
    }
}
