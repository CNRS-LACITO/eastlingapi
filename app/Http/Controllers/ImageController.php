<?php

namespace App\Http\Controllers;
use App\Models\Document;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

        /**
     * Retrieve the form for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function get($id)
    {
        //return User::findOrFail($id);
        $this->middleware('auth');
        $user = Auth::user();

        $image = Image::findOrFail($id);

        return $image;
    }

        /**
     * Retrieve the user for the given ID.
     *
     * @return image
     */
    public function create(Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $image = new Image();

        $image->document_id = $request->document_id;
        $image->rank = $request->rank;
        $image->filename = $request->filename;
        $image->name = $request->name;
        $image->ratio = $request->ratio;


        if($request->url!=='null'){
            //s'il s'agit d'un import via Cocoon
            $image->url = $request->url;
        }else{
            $file = $request->file("resourceFile");
            $content = $file->getContent();
            $image->content = $content;
        }

        
        $image->save();
        //TODO : cas import Cocoon
        
        $image_created = Image::select('id','filename','name','rank',DB::raw('TO_BASE64(content)','ratio'))
       ->where(['id' => $image->id])->first();

        //return response()->json($image->id, 201);
        return response()->json($image_created, 201);

    }
            /**
     * Delete the document for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id)
    {

        $this->middleware('auth');
        $user = Auth::user();

        $image = Image::findOrFail($id);
        $image->delete();

        $resetImageId = (Image::max('id') === NULL)?0:Image::max('id');
        DB::statement("ALTER TABLE images AUTO_INCREMENT = $resetImageId;");

        return response('Image '.$id.' deleted successfully', 200);
    }

                /**
     * Update the document for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {

        $this->middleware('auth');
        $user = Auth::user();
        $image = Image::findOrFail($id);
        $image->update($request->all());

        return response()->json($image, 200);
    }

        /**
     * Retrieve the annotations for the given doc ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDocumentImages($docId)
    {
        $this->middleware('auth');
        $user = Auth::user();
        //$images = Image::where('document_id', $docId)->get();

        $image = Image::where(['document_id' => $docId])->first();

        return $image;
    }

    //
}
