<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'posts_id', 'status', 'users_id'
    ];
    protected $guard = [
        'created_at', 'updated_at'
    ];
    protected $appends = ['is_own_notification'];

    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function getIsOwnNotificationAttribute() {
        $user = Auth::guard('api')->user();
        $is_own_notification = $this->user->id == $user->id ?? false;
        return $is_own_notification;
    }
}
