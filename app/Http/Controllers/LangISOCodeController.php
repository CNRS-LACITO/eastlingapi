<?php

namespace App\Http\Controllers;
use App\Models\LangISOCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LangISOCodeController extends Controller
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

        $langue = LangISOCode::where('id', $id)->first();
        return response()->json($langue, 200);
    }

            /**
     * Retrieve the form for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getLikes($str)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $langues = LangISOCode::where('code_langue_sujet', 'LIKE', '%'.$str.'%')->orWhere('sujet', 'LIKE', '%'.$str.'%')->get();
        return response()->json($langues, 200);
    }


    //
}
