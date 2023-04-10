<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LangISOCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'langue_sujet','code_langue_sujet','sujet'
    ];

    protected $table = "langisocodes";

}
