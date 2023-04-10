<?php

namespace App\Http\Controllers;
use App\Models\Form;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
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

        $form = Form::where('id', $id)->with(['notes'])->first();
        return response()->json($form, 200);
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

        $form = new Form();
        $form->annotation_id = $request->annotation_id;
        $form->kindOf = $request->kindOf;
        $form->text = $request->text;

        $form->save();

        return response()->json($form, 201);

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

        $form = Form::findOrFail($id);
        $notes = $form->notes;

        foreach($form->notes as $note){
            $note->delete();
        }

        $form->delete();
        $resetFormId = (Form::max('id') === NULL)?0:Form::max('id');
        DB::statement("ALTER TABLE forms AUTO_INCREMENT = $resetFormId;");

        //return response($r,200);
        return response('Form '.$id.' deleted successfully', 200);
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
        $form = Form::findOrFail($id);
        $form->update($request->all());

        return response()->json($form, 200);
    }

        

        /**
     * Retrieve the forms for the given annotation ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getAnnotationForms($annotationId)
    {
        $this->middleware('auth');
        $user = Auth::user();
        $forms = Form::where('annotation_id', $annotationId)->with(['notes'])->get();
        return $forms;
    }

            /**
     * Create a note for the Form.
     *
     * @return formId
     */
    public function createNote($id,Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();
        $form = Form::findOrFail($id);

        $note = new Note();
        $note->lang = $request->lang;
        $note->text = $request->text;

        $form->notes()->save($note);
        return response()->json($form, 201);

    }


    //
}
