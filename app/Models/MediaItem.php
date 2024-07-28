<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelpers;

class MediaItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media_item';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'title',
        'attachment_images',
        'attachment_videos',
        'caption',
        'value',
        'media_id',
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
        'is_done_date',
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

    public function media() {
        return $this->belongsTo(Media::class, 'media_id', 'id');
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

        if ($user->placement == 'main_office' && $user->type == 'checker') $is_checker = true;

        return $is_checker ?? false;
    }

    public function getIsDoneDateAttribute() {
        $status = array('prev', 'next');

        if (in_array(CustomHelpers::isPreviousDate($this->media->period_date), $status)) return true;
        return false;
    }
}
