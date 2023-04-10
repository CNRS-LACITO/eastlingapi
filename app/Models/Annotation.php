<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type','rank','areaCoords','audioStart','audioEnd','image_id','imageCoords'
    ];

    // User model
    protected $casts = [
        'imageCoords' => 'array'
    ];

    	    /**
	 * Get the document.
	 */
	public function document()
	{
	    return $this->belongsTo(Document::class);
	}

	    	    /**
	 * Get the parent annotation (if type W or M).
	 */
	public function parentAnnotation()
	{
	    return $this->belongsTo(Annotation::class,'parent_id')->where('parent_id',0)->with('parentAnnotation');
	}

	    /**
     * Get the children annotations (if type S or W).
     */
    public function childrenAnnotations()
    {
        return $this->hasMany(Annotation::class,'parent_id')->with(['childrenAnnotations','notes','forms.notes','translations.notes']);
    }

       /**
     * Get the forms.
     */
    public function forms()
    {
        return $this->hasMany(Form::class);
    }

           /**
     * Get the forms.
     */
    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

                /**
     * Get the document.
     */
    public function image()
    {
        //return $this->belongsTo(Image::class);
    }

            /**
     * Get all of the form's notes.
     */
    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
