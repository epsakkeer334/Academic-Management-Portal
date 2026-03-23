<?php

namespace App\Models\Admin;



class Course extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'duration',
        'image',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = ['image_url'];


    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/courses/' . $this->image);
        }
        return asset('admin/assets/img/default-course.png');
    }


    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y') : '-';
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return '-';
        }

        return $this->duration;
    }

    /* Scope to get active institutes only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
