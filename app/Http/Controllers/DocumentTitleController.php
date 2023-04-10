<?php

namespace App\Http\Controllers;
use App\Models\Document;
use App\Models\DocumentTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentTitleController extends Controller
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

        $title = DocumentTitle::findOrFail($id);

        return $title;
    }

    /**
     * Create a contributor for the given document ID.
     *
     * @return contributor
     */
    public function create(Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $title = new DocumentTitle();
        $title->lang = $request->lang;
        $title->title = $request->title;
        $title->document_id = $request->document_id;

        $title->save();

        return response()->json($title, 201);
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

        $title = DocumentTitle::findOrFail($id);
        $title->delete();

        $resetDocumentTitleId = (DocumentTitle::max('id') === NULL)?0:DocumentTitle::max('id');
        DB::statement("ALTER TABLE document_titles AUTO_INCREMENT = $resetDocumentTitleId;");

        return response('Title '.$id.' deleted successfully', 200);
    }

            /**
     * Retrieve the form for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDocumentTitles($docId)
    {
        //return User::findOrFail($id);
        $this->middleware('auth');
        $user = Auth::user();

        $titles = DocumentTitle::where('document_id',$docId)->get();

        return $titles;
    }


    //
}
