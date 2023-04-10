<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
	    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename','name','rank','content','url','ratio'
    ];

    	    /**
	 * Get the document.
	 */
	public function document()
	{
	    return $this->belongsTo(Document::class);
	}
}
