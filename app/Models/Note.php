<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lang','text'
    ];
    /**
     * Get the parent notable model (form or translation).
     */
    public function notable()
    {
        return $this->morphTo();
    }
}
