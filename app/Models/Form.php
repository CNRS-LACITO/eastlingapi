<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kindOf','text'
    ];

        	    /**
	 * Get the annotation.
	 */
	public function annotation()
	{
	    return $this->belongsTo(Annotation::class);
	}

	    /**
     * Get all of the form's notes.
     */
    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
