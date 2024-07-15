<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unit';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'title',
    ];
    protected $guard = [
        'created_at', 'updated_at'
    ];
    protected $dates = [
        'deleted_at'
    ];

    public $timestamps = true;

    public function user() {
        return $this->hasMany(User::class);
    }

    public function posts() {
        return $this->hasMany(Posts::class);
    }
}
