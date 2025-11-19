<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'crime_type',
        'location_id',
        'assigned_station_id',
        'status',
        'is_anonymous',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    /**
     * Get the user that created the report
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the location of the report
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the messages related to this report
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'report_id');
    }

    /**
     * Get the media files for this report
     */
    public function media()
    {
        return $this->hasMany(ReportMedia::class, 'report_id', 'report_id');
    }
}