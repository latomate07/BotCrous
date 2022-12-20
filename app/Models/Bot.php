<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    use HasFactory;

    /**
     * Get this model fillable
     * 
     * @var array
     */
    protected $fillable = [
        'receiverProcessed'
    ];
}
