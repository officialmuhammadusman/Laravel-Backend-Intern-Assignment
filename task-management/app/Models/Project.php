<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
       
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users() 
    {
        return $this->belongsToMany(User::class); // Laravel auto-detects project_user table
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}