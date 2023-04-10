<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lang','type','recording_date','recording_place','available_kindOf','available_lang','oai_primary','oai_secondary'
    ];

    protected $casts = [
        'available_kindOf' => 'array',
        'available_lang' => 'array'
    ];

	    /**
	 * Get the owner.
	 */
	public function owner()
	{
	    return $this->belongsTo(User::class);
	}

    /**
     * Get the titles for the document.
     */
    public function titles()
    {
        return $this->hasMany(DocumentTitle::class);
    }

        /**
     * Get the titles for the document.
     */
    public function contributors()
    {
        return $this->hasMany(DocumentContributor::class);
    }

        /**
     * Get the annotations for the document.
     */
    public function annotations()
    {
        return $this->hasMany(Annotation::class)->wherein('type',['T']);
    }

        /**
     * Get the images for the document.
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }


}
