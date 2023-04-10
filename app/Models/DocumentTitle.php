<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTitle extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lang','title'
    ];

	    /**
	 * Get the document.
	 */
	public function document()
	{
	    return $this->belongsTo(Document::class);
	}
}
