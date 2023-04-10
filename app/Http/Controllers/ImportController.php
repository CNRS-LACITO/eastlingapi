<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Document;
use App\Models\Annotation;
use App\Models\Form;
use App\Models\Translation;
use App\Models\Image;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Log;

class ImportController extends Controller
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

    public function hasChild($parentTag,$childTagName){
        $find=false;
        if(sizeof($parentTag->childNodes)>1){
            foreach($parentTag->childNodes as $c){
                if($c->nodeName===$childTagName)
                $find=true;
            }
        }
        return $find;
    }

    public function recursiveParseXML($xmlTag,$o){
    //fonction pour convertir le XML en JSON

        if($xmlTag->nodeName!='#text'){

            if(isset($o->{$xmlTag->nodeName})){

                if(gettype($o->{$xmlTag->nodeName}) == 'object'){
                    $v = $o->{$xmlTag->nodeName};
                    $o->{$xmlTag->nodeName}=array();
                    $o->{$xmlTag->nodeName}[]=$v;
                }

                $obj = new \stdClass();

                if(sizeof($xmlTag->attributes)>0){
                    foreach($xmlTag->attributes as $a){
                        $obj->{$a->nodeName}=$a->nodeValue;
                    }   
                }
                if(sizeof($xmlTag->childNodes)>1){
                    foreach($xmlTag->childNodes as $c){
                        $this->recursiveParseXML($c,$obj);
                    }
                }else{
                        $obj->text=$xmlTag->textContent;
                }
                $o->{$xmlTag->nodeName}[]=$obj;
            }else{
                $o->{$xmlTag->nodeName} = new \stdClass();
                
                $attr = array();

                if(sizeof($xmlTag->attributes)>0){
                    foreach($xmlTag->attributes as $a){
                        $o->{$xmlTag->nodeName}->{$a->nodeName}=$a->nodeValue;
                    }   
                }
                if(sizeof($xmlTag->childNodes)>1 && !$this->hasChild($xmlTag,'FOREIGN')){
                    foreach($xmlTag->childNodes as $c){
                        if($c->nodeName!='#text' && $c->nodeName!='FOREIGN')
                        $this->recursiveParseXML($c,$o->{$xmlTag->nodeName});
                    }
                }else{
                        $o->{$xmlTag->nodeName}->text=$xmlTag->textContent;
                }
            }
        }
    }

    //Get annotations : FORMS, TRANSLATIONS, AUDIO & IMAGE POSITION
    //recursive function to reduce code
    //$images is passed to avoid too many SQL request on images
    public function importAnnotation(&$annotationCreated,$annotationRead,$images,$parentKindOf = null,$parentXmlLang = null){
        //get the audio position

        $log_file_data = ($_ENV['APP_ENV']==='local')?"C:/Users/cream/Desktop/eastlingerror.log":"./errorlog.log";
        //file_put_contents($log_file_data, json_encode($annotationRead) . "\n\n\n", FILE_APPEND);
        Log::info(json_encode($annotationRead));

        //#45
        $currentKindOf = "";
        $currentXmlLang = "";

        //TODO : ne marche que si array, pas objet

        //if(property_exists($annotationRead,"AUDIO")){
        if(isset($annotationRead->AUDIO)){
            $annotationCreated->audioStart = $annotationRead->AUDIO->start;
            $annotationCreated->audioEnd = $annotationRead->AUDIO->end;
        }

        //17/08/2022 update to allow multiple AREA
        //get the image position
        //if(property_exists($annotationRead,"AREA")){
        if(isset($annotationRead->AREA)){

/*
            

            foreach($images as $image){
                if($image->name === $annotationRead->AREA->image) $image_id = $image->id;
            }

            $annotationCreated->image_id = $image_id;
            $annotationCreated->areaCoords = $annotationRead->AREA->coords;

*/
            $imageCoords = [];
            $ratio = 1;
            

            if (gettype($annotationRead->AREA)==="object"){
                $image_id = NULL;

                foreach($images as $image){
                    //if($image->name === $annotationRead->AREA->image) $image_id = $image->id;
                    //On traite le cas avec 1 seule image pour le moment
                    $image_id = $image->id;
                    $ratio = $image->ratio;
                }
                $coords = $annotationRead->AREA->coords.','.$ratio;//TODO ratio image!

                $imageCoords[]=array(
                    'image_id'=>$image_id,
                    'areaCoords'=>$coords
                );

            }else if (gettype($annotationRead->AREA)==="array"){

                foreach($annotationRead->AREA as $area){
                    $image_id = NULL;

                    foreach($images as $image){
                        //if($image->name === $annotationRead->AREA->image) $image_id = $image->id;
                        //On traite le cas avec 1 seule image pour le moment
                        $image_id = $image->id;
                        $ratio = $image->ratio;
                    }

                    $coords = $area->coords.','.$ratio;//TODO ratio image!

                    $imageCoords[]=array(
                        'image_id'=>$image_id,
                        'areaCoords'=>$coords
                    );
                }
            }

            $annotationCreated->imageCoords = $imageCoords;
        }

        //get the forms
        //if(property_exists($annotationRead,"FORM")){
        if(isset($annotationRead->FORM)){
            if (gettype($annotationRead->FORM)=="object"){
                $FormObject = new Form();
                $FormObject->annotation_id = $annotationCreated->id;

                //if(property_exists($annotationRead->FORM,"kindOf")){
                if(isset($annotationRead->FORM->kindOf)){
                    $currentKindOf = $annotationRead->FORM->kindOf;
                }else{
                    $currentKindOf = "phono";
                }
                $FormObject->kindOf = $currentKindOf;

                $FormObject->text = $annotationRead->FORM->text;

                $FormObject->save();

            }else if (gettype($annotationRead->FORM)=="array"){
                foreach($annotationRead->FORM as $form){
                    $FormObject = new Form();
                    $FormObject->annotation_id = $annotationCreated->id;

                    //if(property_exists($form,"kindOf")){
                    if(isset($form->kindOf)){
                        $currentKindOf = $form->kindOf;
                    }else{
                        $currentKindOf = "phono";
                    }

                    $FormObject->kindOf = $currentKindOf;

                    $FormObject->text = $form->text;

                    $FormObject->save();
                }
            } 
        }

        //get the translations
        //if(property_exists($annotationRead,"TRANSL")){
        if(isset($annotationRead->TRANSL)){
            if (gettype($annotationRead->TRANSL)=="object"){
                $TranslObject = new Translation();
                $TranslObject->annotation_id = $annotationCreated->id;
                //#45 : $TranslObject->lang = $annotationRead->TRANSL->{"xml:lang"};
                if(isset($annotationRead->TRANSL->{"xml:lang"})){
                    $currentXmlLang = $annotationRead->TRANSL->{"xml:lang"};
                }else{
                    $currentXmlLang = $parentXmlLang;
                }
                $TranslObject->lang = $currentXmlLang;

                $TranslObject->text = $annotationRead->TRANSL->text;

                $TranslObject->save();
            }else if (gettype($annotationRead->TRANSL)=="array"){
                foreach($annotationRead->TRANSL as $translation){
                    $TranslObject = new Translation();
                    $TranslObject->annotation_id = $annotationCreated->id;

                    //#45 $TranslObject->lang = $translation->{"xml:lang"};
                    if(isset($translation->{"xml:lang"})){
                        $currentXmlLang = $translation->{"xml:lang"};
                    }else{
                        $currentXmlLang = $parentXmlLang;
                    }
                    $TranslObject->lang = $currentXmlLang;

                    $TranslObject->text = $translation->text;

                    $TranslObject->save();
                }
            } 
        }

        $annotationCreated->save();

        //TODO :  FACTORISATION POSSIBLE ?
        $children = 0;

        //get the children (sentence)
        //if(property_exists($annotationRead,"S")) 
        if(isset($annotationRead->S)) foreach($annotationRead->S as $child){
            $childAnnotation = new Annotation();
            $childAnnotation->document_id = $annotationCreated->document_id;
            $childAnnotation->type = 'S';
            $childAnnotation->rank = ++$children;
            $childAnnotation->parent_id = $annotationCreated->id;

            $childAnnotation->save();
            $this->importAnnotation($childAnnotation,$child,$images,$currentKindOf,$currentXmlLang);
        }

        $children = 0;

        //get the children (words)
        //if(property_exists($annotationRead,"W")) foreach($annotationRead->W as $child){
        if(isset($annotationRead->W)) foreach($annotationRead->W as $child){
            $childAnnotation = new Annotation();
            $childAnnotation->document_id = $annotationCreated->document_id;
            $childAnnotation->type = 'W';
            $childAnnotation->rank = ++$children;
            $childAnnotation->parent_id = $annotationCreated->id;

            $childAnnotation->save();
            $this->importAnnotation($childAnnotation,$child,$images,$currentKindOf,$currentXmlLang);
        }

        $children = 0;

        //get the children (morphemes)
        //if(property_exists($annotationRead,"M")) foreach($annotationRead->M as $child){
        if(isset($annotationRead->M)) foreach($annotationRead->M as $child){
            $childAnnotation = new Annotation();
            $childAnnotation->document_id = $annotationCreated->document_id;
            $childAnnotation->type = 'M';
            $childAnnotation->rank = ++$children;
            $childAnnotation->parent_id = $annotationCreated->id;

            $childAnnotation->save();
            $this->importAnnotation($childAnnotation,$child,$images,$currentKindOf,$currentXmlLang);
        }

    }

        /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function postFile(Request $request)
    {
        //return User::findOrFail($id);
        $this->middleware('auth');
        $user = Auth::user();

        //1. Convert the XML into JSON
        $xmlData = simplexml_load_file($request->file);
        $annotationXml = dom_import_simplexml($xmlData);
        $annotationJson = new \stdClass();

        $this->recursiveParseXML($annotationXml,$annotationJson);

        //2. Delete existing annotations
        Annotation::where('document_id',$request->docId)->delete();
        //3. Import annotations from XML
        $textAnnotation = new Annotation();
        $textAnnotation->document_id = $request->docId;
        $textAnnotation->type = 'T';
        $textAnnotation->rank = 1;
        $textAnnotation->parent_id = null;

        $textAnnotation->save();

        $images = Image::where(['document_id' => $request->docId])->get();

        ini_set('max_execution_time', '1200');

        if(property_exists($annotationJson,"TEXT")){
            $this->importAnnotation($textAnnotation,$annotationJson->TEXT,$images);
        }else if(property_exists($annotationJson,"WORDLIST")){
            $this->importAnnotation($textAnnotation,$annotationJson->WORDLIST,$images);
        }

        return json_encode($annotationJson);
    }


    //
}
