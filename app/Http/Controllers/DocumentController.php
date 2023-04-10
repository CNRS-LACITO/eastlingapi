<?php

namespace App\Http\Controllers;
use App\Models\Document;
use App\Models\Image;
use App\Models\Recording;
use App\Models\Annotation;
use App\Models\DocumentContributor;
use App\Models\DocumentTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
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
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function get($id)
    {
        //return User::findOrFail($id);
        $this->middleware('auth');
        $user = Auth::user();
        //$images = Image::where(['document_id' => $id])->get();
        $images = Image::select('id','filename','name','rank',DB::raw('TO_BASE64(content)'),'url','ratio')
       ->where(['document_id' => $id])->get();
        //$contributors = Image::where(['document_id' => $id])->get();
        $recording = Recording::select('id','filename','name','type',DB::raw('TO_BASE64(content)'),'url')
       ->where(['document_id' => $id])->first();


       if(($recording !== NULL) && ($recording['TO_BASE64(content)'] === NULL) && ($recording->url !== NULL)){

            $data=file_get_contents($recording->url);
            $base64 = base64_encode($data);

            $recording['TO_BASE64(content)']=$base64;
        }


        $document = Document::where(['id' => $id,'user_id' => $user->id])->with(['titles','contributors','annotations.notes','annotations.forms.notes','annotations.translations.notes','annotations.childrenAnnotations'])->first();

        if($document){
            $document->images = $images;
            $document->recording = $recording;
        }

        return $document;
    }

            /**
     * Retrieve the user for the given OAIs.
     *
     * @param  string  $id
     * @return Response
     */
    public function getByOAI($oaiPrimary,$oaiSecondary)
    {
        //return User::findOrFail($id);
        $this->middleware('auth');
        $user = Auth::user();


        //$document = Document::where(['oai_primary' => $oaiPrimary,'oai_secondary' => $oaiSecondary,'user_id' => $user->id])->with(['titles','contributors','annotations.notes','annotations.forms.notes','annotations.translations.notes','annotations.childrenAnnotations'])->first();

        $document = Document::where(['oai_primary' => $oaiPrimary,'oai_secondary' => $oaiSecondary,'user_id' => $user->id])->first();

        if($document){
            /*
            $images = Image::select('id','filename','name','rank',DB::raw('TO_BASE64(content)'))
           ->where(['document_id' => $document->id])->get();
            //$contributors = Image::where(['document_id' => $id])->get();
            $recording = Recording::select('id','filename','name','type',DB::raw('TO_BASE64(content)'))
           ->where(['document_id' => $document->id])->first();

           if($document){
                $document->images = $images;
                $document->recording = $recording;
            }
*/
            return $document;

        }else{
            return response()->json(array(), 204); 
        }
    }

        /**
     * Retrieve the user for the given ID.
     *
     * @return docId
     */
    public function create(Request $request)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $request["user_id"] = $user->id;

        $document = new Document();
        $document->user_id = $user->id;
        $document->type = $request->type;
        $document->lang = $request->lang;
        $document->oai_primary = $request->oai_primary;
        $document->oai_secondary = $request->oai_secondary;

        $document->save();

        return response()->json($document, 201);

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

        $document = Document::findOrFail($id);
        Annotation::where('document_id',$document->id)->delete();
        Image::where('document_id',$document->id)->delete();
        Recording::where('document_id',$document->id)->delete();
        DocumentContributor::where('document_id',$document->id)->delete();
        DocumentTitle::where('document_id',$document->id)->delete();
        $document->delete();

        $resetDocumentId = (Document::max('id') === NULL)?0:Document::max('id');
        $resetAnnotationId = (Annotation::max('id') === NULL)?0:Annotation::max('id');
        $resetImageId = (Image::max('id') === NULL)?0:Image::max('id');
        $resetRecordingId = (Recording::max('id') === NULL)?0:Recording::max('id');
        $resetDocumentContributorId = (DocumentContributor::max('id') === NULL)?0:DocumentContributor::max('id');
        $resetDocumentTitleId = (DocumentTitle::max('id') === NULL)?0:DocumentTitle::max('id');

        DB::statement("ALTER TABLE documents AUTO_INCREMENT = $resetDocumentId;");
        DB::statement("ALTER TABLE annotations AUTO_INCREMENT = $resetAnnotationId;");
        DB::statement("ALTER TABLE images AUTO_INCREMENT = $resetImageId;");
        DB::statement("ALTER TABLE recordings AUTO_INCREMENT = $resetRecordingId;");
        DB::statement("ALTER TABLE document_contributors AUTO_INCREMENT = $resetDocumentContributorId;");
        DB::statement("ALTER TABLE document_titles AUTO_INCREMENT = $resetDocumentTitleId;");

        return response('Document '.$id.' deleted successfully', 200);
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
        $document = Document::findOrFail($id);
        $document->update($request->all());
        //$document->save();
        return response()->json($document, 200);
    }

        

        /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getUserDocuments(){
        $this->middleware('auth');
        $user = Auth::user();
        $documents = Document::where('user_id', $user->id)->with('titles')->get();
        return $documents;
    }

    public function recursiveBuildAnnotationsXML($annotation,&$xmlAnnotations){

        foreach($annotation->childrenAnnotations as $childAnnotation){

            $xmlChildrenAnnotation = $xmlAnnotations->addChild($childAnnotation->type);
            $xmlChildrenAnnotation->addAttribute('id','eastling-annotations_'.$childAnnotation->id);

//__________AREA
            if(sizeof($childAnnotation->imageCoords)>0){
                foreach($childAnnotation->imageCoords as $imageCoord){
                    $xmlArea = $xmlChildrenAnnotation->addChild('AREA');
                    $xmlArea->addAttribute('image','eastling-images_'.$imageCoord['image_id']);
                    $xmlArea->addAttribute('shape','rect');
                    $coords = array_slice(explode(',',$imageCoord['areaCoords']), 0, 4);

                    foreach($coords as &$coord){
                        $coord = round($coord);
                    }
                    $xmlArea->addAttribute('coords',implode(',',$coords));
                }
                
            }
//__________AUDIO
            if($childAnnotation->audioStart !== null && $childAnnotation->audioEnd !== null){
                $xmlAudio = $xmlChildrenAnnotation->addChild('AUDIO');
                $xmlAudio->addAttribute('start',$childAnnotation->audioStart);
                $xmlAudio->addAttribute('end',$childAnnotation->audioEnd);
            }

//__________TRANSL
            if(sizeof($childAnnotation->translations)>0){
                foreach($childAnnotation->translations as $translation){
                    $xmlTextItem = $xmlChildrenAnnotation->addChild('TRANSL',$translation['text']);
                    $xmlTextItem->addAttribute('xml_colons_lang',$translation['lang']);
                }
            }

//__________FORM
            if(sizeof($childAnnotation->forms)>0){
                foreach($childAnnotation->forms as $form){
                    $xmlTextItem = $xmlChildrenAnnotation->addChild('FORM',$form['text']);
                    $xmlTextItem->addAttribute('kindOf',$form['kindOf']);
                }
            }

//__________NOTE
            if(sizeof($childAnnotation->notes)>0){
                foreach($childAnnotation->notes as $note){
                    $xmlTextItem = $xmlChildrenAnnotation->addChild('NOTE');
                    $xmlTextItem->addAttribute('xml_colons_lang',$note['lang']);
                    $xmlTextItem->addAttribute('message',$note['text']);
                }
            }

//__________CHILDREN
            if(sizeof($childAnnotation->childrenAnnotations)>0){
                $this->recursiveBuildAnnotationsXML($childAnnotation,$xmlChildrenAnnotation);
            }

//____END LOOP CHILDREN

        }
    }

    public function buildAnnotationsXML($document){
        $root = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL."\t".'<!DOCTYPE '.$document->type.' SYSTEM "https://cocoon.huma-num.fr/schemas/Archive.dtd">'.PHP_EOL;
        $root.='<'.$document->type.' id="eastling-documents_'.$document->id.'" xml:lang="'.$document->lang.'"></'.$document->type.'>';
        
        //$root = '<'.$document->type.' id="eastling-documents_'.$document->id.'" xml:lang="'.$document->lang.'"></'.$document->type.'>';
        $xmlAnnotations = new \SimpleXMLElement($root);

        //$xmlAnnotations->addChild('!DOCTYPE '.$document->type.' SYSTEM "https://cocoon.huma-num.fr/schemas/Archive.dtd"');

        //$textNode = $xmlAnnotations->addChild($document->type.' id="eastling-documents_'.$document->id.'" xml:lang="'.$document->lang);


        $xmlAnnotations->addChild('HEADER');

        $this->recursiveBuildAnnotationsXML($document->annotations[0],$xmlAnnotations);

        $xml=str_replace('_colons_', ':', $xmlAnnotations->asXml());

        $dom = new \DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);

        return $dom->saveXML();
    }

    public function buildMetadataXML($document){
        $root = '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE '.$document->type.' SYSTEM "https://cocoon.huma-num.fr/schemas/Archive.dtd">';
        $root.='<'.$document->type.' id="eastling-documents_'.$document->id.'" xml:lang="'.$document->lang.'"></'.$document->type.'>';
        
        $xmlAnnotations = new \SimpleXMLElement($root);
        $xmlAnnotations->addChild('HEADER');

        $this->recursiveBuildAnnotationsXML($document->annotations[0],$xmlAnnotations);

        $xml=str_replace('_colons_', ':', $xmlAnnotations->asXml());

        $dom = new \DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);

        return $dom->saveXML();
    }

        /**
     * export the document for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDocumentAnnotationsXML($id)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $document = Document::where(['id' => $id,'user_id' => $user->id])->with(['titles','contributors','annotations.forms.notes','annotations.translations.notes','annotations.childrenAnnotations'])->first();

        $xml = $this->buildAnnotationsXML($document);
            
        return response($xml)->header('Content-Type', 'xml');
          
        
    }

            /**
     * export the document for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDocumentAnnotationsJSON4LATEX($id)
    {
        $this->middleware('auth');
        $user = Auth::user();

        $document = Document::where(['id' => $id,'user_id' => $user->id])->with(['titles','contributors','annotations.forms.notes','annotations.translations.notes','annotations.childrenAnnotations'])->first();

        $arr = array();

        $arr["langue"] = $document->lang;

        $arr["livret"] = array();
        $arr["livret"]["chemin"] = "../langues/livrets/".$document->lang;
        $arr["livret"]["nom complet"] = $document->titles[0]->title;

        $arr["livret"]["paramètres"] = array();
        $arr["livret"]["paramètres"]["transcription du texte"] = array('test');
        $arr["livret"]["paramètres"]["traduction du texte"] = array('test');
        $arr["livret"]["paramètres"]["transcription des phrases"] = array('test');
        $arr["livret"]["paramètres"]["traduction des phrases"] = array('test');
        $arr["livret"]["paramètres"]["transcription des mots"] = array('test');
        $arr["livret"]["paramètres"]["traduction des mots"] = array('test');
        $arr["livret"]["paramètres"]["transcription des morphèmes"] = array('test');
        $arr["livret"]["paramètres"]["traduction des morphèmes"] = array('test');
        $arr["livret"]["paramètres"]["notes"] = array('test');

        $arr["livret"]["textes"]["introduction"] = array();
        $arr["livret"]["textes"]["principal"] = array();

        $arr["livret"]["resources"] = array();
        $arr["livret"]["resources"][] = array("identifiant" => "eastling_".$document->lang.$document->type.$document->id);
        //$arr["livret"]["resources"]["segments"] = "all";
        $arr["livret"]["resources"][] = array("contenu" => $this->buildAnnotationsXML($document));



        return response()->json($arr, 200);
          
        
    }


    //
}
