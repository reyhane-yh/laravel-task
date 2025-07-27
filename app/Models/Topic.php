<?php

namespace App\Models;

use App\Observers\TopicObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    /** @use HasFactory<\Database\Factories\TopicFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    protected static function booted(): void
    {
        static::observe(TopicObserver::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function children(){
        return $this->hasMany(Topic::class, 'parent');
    }

    public function parent(){
        return $this->belongsTo(Topic::class, 'parent');
    }



}
