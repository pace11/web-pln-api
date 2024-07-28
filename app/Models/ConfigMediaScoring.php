<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\CustomHelpers;

class ConfigMediaScoring extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'config_media_scoring';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'title', 'key', 'value'
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
