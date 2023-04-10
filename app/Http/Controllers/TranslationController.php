<?php

namespace App\Http\Controllers;
use App\Models\Translation;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
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
        $this->middleware('auth');
        $user = Auth::user();

        $translation = Translation::where('id', $id)->with(['notes'])->first();
        return response()->json($translation, 200);
    }

        /**
     * Create the translation for the given annotation ID.
     *
     * @return formId
     */
    public function create(Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $translation = new Translation();
        $translation->annotation_id = $request->annotation_id;
        $translation->lang = $request->lang;
        $translation->text = $request->text;

        $translation->save();

        return response()->json($translation, 201);

    }
            /**
     * Delete the translation for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id)
    {

        $this->middleware('auth');
        $user = Auth::user();

        $translation = Translation::findOrFail($id);

        $notes = $translation->notes;

        foreach($translation->notes as $note){
            $note->delete();
        }
        
        $translation->delete();

        $resetTranslationId = (Translation::max('id') === NULL)?0:Translation::max('id');
        DB::statement("ALTER TABLE translations AUTO_INCREMENT = $resetTranslationId;");

        return response('Translation '.$id.' deleted successfully', 200);
    }

                /**
     * Update the translation for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {

        $this->middleware('auth');
        $user = Auth::user();
        $translation = Translation::findOrFail($id);
        $translation->update($request->all());

        return response()->json($translation, 200);
    }

        

        /**
     * Retrieve the translations for the given annotation ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getAnnotationTranslations($annotationId)
    {
        $this->middleware('auth');
        $user = Auth::user();
        $translations = Translation::where('annotation_id', $annotationId)->with(['notes'])->get();
        return $translations;
    }

                /**
     * Create a note for the Translation.
     *
     * @return translation
     */
    public function createNote($id,Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();
        $translation = Translation::findOrFail($id);

        $note = new Note();
        $note->lang = $request->lang;
        $note->text = $request->text;

        $translation->notes()->save($note);
        return response()->json($translation, 201);

    }


    //
}
