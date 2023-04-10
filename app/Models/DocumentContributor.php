<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentContributor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName','lastName','type','document_id'
    ];

    	    /**
	 * Get the document.
	 */
	public function document()
	{
	    return $this->belongsTo(Document::class);
	}
}
