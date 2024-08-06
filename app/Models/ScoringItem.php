<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelpers;

class ScoringItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'scoring_item';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'attachment',
        'realization',
        'value',
        'scoring_id',
        'unit_id',
        'users_id',
    ];
    protected $guard = [
        'created_at', 'updated_at'
    ];
    protected $appends = [
        'is_own_post',
        'is_creator',
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

    public function scoring() {
        return $this->belongsTo(Scoring::class, 'scoring_id', 'id');
    }

    public function getIsOwnPostAttribute() {
        $user = Auth::guard('api')->user();
        $is_own_post = $this->user->id == $user->id ?? false;
        return $is_own_post;
    }

    public function getIsCreatorAttribute() {
        $user = Auth::guard('api')->user();

        if ($user->placement == 'main_office' && $user->type == 'creator') $is_creator = true;

        return $is_creator ?? false;
    }

    public function getIsDoneDateAttribute() {
        $status = array('prev', 'next');

        if (in_array(CustomHelpers::isPreviousDate($this->scoring->period_date), $status)) return true;
        return false;
    }
}
