<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'institute_id',
        'status',
        'display_order',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
