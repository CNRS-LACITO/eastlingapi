<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
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
	 * Get the annotation.
	 */
	public function annotation()
	{
	    return $this->belongsTo(Annotation::class);
	}

		    /**
     * Get all of the translation's notes.
     */
    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
