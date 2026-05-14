<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'images',
        'price',
        'quota',
        'start_date',
        'end_date',
        'certificate_template',
        'is_comment_enabled'
    ];

    protected $casts = [
        'images' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_comment_enabled' => 'boolean',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}
