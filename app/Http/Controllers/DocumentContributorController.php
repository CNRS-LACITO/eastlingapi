<?php

namespace App\Http\Controllers;
use App\Models\Document;
use App\Models\DocumentContributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentContributorController extends Controller
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

        $contributor = DocumentContributor::findOrFail($id);

        return $contributor;
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

        $contributor = new DocumentContributor();
        $contributor->firstName = $request->firstName;
        $contributor->lastName = $request->lastName;
        $contributor->type = $request->type;
        $contributor->document_id = $request->document_id;

        $contributor->save();

        return response()->json($contributor, 201);
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

        $contributor = DocumentContributor::findOrFail($id);
        $contributor->delete();

        $resetDocumentContributorId = (DocumentContributor::max('id') === NULL)?0:DocumentContributor::max('id');
        DB::statement("ALTER TABLE document_contributors AUTO_INCREMENT = $resetDocumentContributorId;");

        return response('Contributor '.$id.' deleted successfully', 200);
    }

            /**
     * Retrieve the form for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDocumentContributors($docId)
    {
        //return User::findOrFail($id);
        $this->middleware('auth');
        $user = Auth::user();

        $contributors = DocumentContributor::where('document_id',$docId)->get();

        return $contributors;
    }


    //
}
