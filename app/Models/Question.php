<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'body',
        'answer',
        'choice_1',
        'choice_2',
        'choice_3',
        'choice_4',
    ];

    public function topics(){
        return $this->belongsTo(Topic::class);
    }

}
