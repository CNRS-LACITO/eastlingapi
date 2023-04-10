<?php

namespace App\Http\Controllers;
use App\Models\Document;
use App\Models\Annotation;
use App\Models\Form;
use App\Models\Translation;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnotationController extends Controller
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
    public function get($docId,$id)
    {
        //return User::findOrFail($id);
        $this->middleware('auth');
        $user = Auth::user();

        $annotation = Annotation::where(['id' => $id,'document_id' => $docId,])->with(['notes','forms.notes','translations.notes','childrenAnnotations'])->first();

        return $annotation;
    }

            /**
     * Retrieve the form for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getNotes($id)
    {
        //return User::findOrFail($id);
        $this->middleware('auth');
        $user = Auth::user();

        $notes = Annotation::where(['id' => $id])->first()->notes;

        return $notes;
    }

        /**
     * Retrieve the user for the given ID.
     *
     * @return formId
     */
    public function create(Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $annotation = new Annotation();

        $annotation->document_id = $request->document_id;
        $annotation->type = $request->type;
        $annotation->rank = $request->rank;
        $annotation->audioStart = $request->audioStart;
        $annotation->audioEnd = $request->audioEnd;
        $annotation->imageCoords = $request->imageCoords;
        $annotation->parent_id = $request->parent_id;

        $annotation->save();

        return response()->json($annotation, 201);

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

        $annotation = Annotation::findOrFail($id);
        $notes = $annotation->notes;

        foreach($annotation->notes as $note){
            $note->delete();
        }
        $annotation->delete();

        $resetAnnotationId = (Annotation::max('id') === NULL)?0:Annotation::max('id');
        DB::statement("ALTER TABLE annotations AUTO_INCREMENT = $resetAnnotationId;");

        return response('Annotation '.$id.' deleted successfully', 200);
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
        $annotation = Annotation::findOrFail($id);

        $annotation->update($request->all());

        return response()->json($annotation, 200);
    }

        /**
     * Retrieve the annotations for the given doc ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDocumentAnnotations($docId)
    {
        $this->middleware('auth');
        $user = Auth::user();
        //$annotations = Annotation::where('document_id', $docId)->get();

        $annotations = Document::where(['id' => $docId])->with(['annotations.notes','annotations.forms.notes','annotations.translations.notes','annotations.childrenAnnotations'])->first();

        return $annotations;
    }

            /**
     * Retrieve the children annotations for the given annotation ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getAnnotationChildren($id)
    {
        $this->middleware('auth');
        $user = Auth::user();
        //$annotations = Annotation::where('document_id', $docId)->get();

        $annotations = Annotation::where(['parent_id' => $id])->with(['notes','forms.notes','translations.notes','childrenAnnotations'])->get();

        return $annotations;
    }
    //

    public function resetTableAutoIncrementId(){

        return (Annotation::max('id') === NULL)?0:Annotation::max('id');
        //ALTER TABLE `table` AUTO_INCREMENT = number;
    }

                /**
     * Create a note for the Form.
     *
     * @return Id
     */
    public function createNote($id,Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();
        $annotation = Annotation::findOrFail($id);

        $note = new Note();
        $note->lang = $request->lang;
        $note->text = $request->text;

        $annotation->notes()->save($note);
        return response()->json($annotation, 201);

    }
}
