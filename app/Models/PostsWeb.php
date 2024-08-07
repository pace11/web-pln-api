<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PostsWeb extends Model
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
}
