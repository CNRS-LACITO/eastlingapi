<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recording extends Model
{
    //
	    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename','name','content','original_content','type','url','oai_primary'
    ];

    	    /**
	 * Get the document.
	 */
	public function document()
	{
	    return $this->belongsTo(Document::class);
	}
}
