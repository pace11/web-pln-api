<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

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
    protected $dates = [
        'deleted_at'
    ];
    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;
}
