<?php

namespace App\Http\Controllers;
use App\Models\Document;
use App\Models\Recording;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/*
use Lame\Lame;
use Lame\Settings;
*/
use FFMpeg;

class RecordingController extends Controller
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

        $recording = Recording::findOrFail($id);

        return $recording;
    }

    /**
     * Retrieve the user for the given ID.
     *
     * @return recording
     */
    public function create(Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $recording = new Recording();

        $recording->document_id = $request->document_id;
        $recording->type = $request->type;
        $recording->filename = $request->filename;
        $recording->name = $request->name;
        //TODO url et oai si import Cocoon

        if($request->url!=='null'){
        //s'il s'agit d'un import via Cocoon
            $recording->url = $request->url;

        }else{
        // si un fichier est envoyé
            $file = $request->file("resourceFile");//file_get_contents
    ////////MP3 Conversion https://github.com/b-b3rn4rd/phplame
            $extension = strtolower(pathinfo($request->filename, PATHINFO_EXTENSION));

            //si format WAV on compresse en MP3
            if($extension ==="wav"){

                $ffmpegBinPath = 'C:\ffmpeg\bin\ffmpeg.exe';
                $ffprobeBinPath = 'C:\ffmpeg\bin\ffprobe.exe';
                //'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
                //'ffprobe.binaries' => '/usr/local/bin/ffprobe'

                $tmpOriginalFile = str_replace($extension,"",$request->filename).'.wav';
                $tmpCompressedFile = str_replace($extension,"",$request->filename).'.mp3';

                file_put_contents($tmpOriginalFile, $file->getContent());

                $ffmpeg = FFMpeg\FFMpeg::create([
                    'ffmpeg.binaries'  => $ffmpegBinPath,
                    'ffprobe.binaries' => $ffprobeBinPath
                ]);

                $audio = $ffmpeg->open($tmpOriginalFile);
                $audio_format = new FFMpeg\Format\Audio\Mp3();
                // Extract the audio into a new file as mp3

                $audio->save($audio_format, $tmpCompressedFile);

                $content = file_get_contents($tmpCompressedFile);
                $originalContent = file_get_contents($tmpOriginalFile);

                unlink($tmpOriginalFile);
                unlink($tmpCompressedFile);
            }else{
                $content = $file->getContent();
                $originalContent = $content;
            }
            
            $recording->original_content = $originalContent;
            $recording->content = $content;
        }



        $recording->save();

        $recording_created = Recording::select('id','filename','name','type','url',DB::raw('TO_BASE64(content)'))
       ->where(['id' => $recording->id])->first();

        //return response($recording_created, 201);//bug
        return response()->json($recording_created, 201);

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

        $recording = Recording::findOrFail($id);
        $recording->delete();

        $resetRecordingId = (Recording::max('id') === NULL)?0:Recording::max('id');
        DB::statement("ALTER TABLE recordings AUTO_INCREMENT = $resetRecordingId;");

        return response('Recording '.$id.' deleted successfully', 200);
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
        $recording = Recording::findOrFail($id);
        $recording->update($request->all());

        return response()->json($recording, 200);
    }

        /**
     * Retrieve the annotations for the given doc ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDocumentRecordings($docId)
    {
        $this->middleware('auth');
        $user = Auth::user();
        //$recordings = Recording::where('document_id', $docId)->get();

        $recording = Recording::where(['document_id' => $docId])->first();

        return $recording;
    }

    //
}
