<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Support\Facades\Artisan;

$router->get('/', function () use ($router) {
    return $router->app->version().__DIR__;
});

$prefix = ($_ENV['APP_ENV']==='local')?'api':'';

// API route group
$router->group(['prefix' => $prefix], function () use ($router) {
	
	$router->get('test','AnnotationController@resetTableAutoIncrementId');
	$router->get('hash/{word}', 'AuthController@hash');

	$router->get('phpinfo','Controller@phpinfo');

	//Clear configurations:
	$router->get('/config-clear', function() {
		$status = Artisan::call('config:clear');
		return '<h1>Configurations cleared</h1>';
	});

	//Clear cache:
	$router->get('/cache-clear', function() {
		$status = Artisan::call('cache:clear');
		return '<h1>Cache cleared</h1>';
	});

	//Clear configuration cache:
	$router->get('/config-cache', function() {
		$status = Artisan::call('config:cache');
		return '<h1>Configurations cache cleared</h1>';
	});

	$router->group(['prefix' => 'auth'], function () use ($router) {
	   // Matches "/api/auth/login
	   $router->post('login', 'AuthController@login');
	});

   $router->group(['middleware' => 'auth'], function () use ($router) {

   		$router->post('register', 'AuthController@register');
   		
   		//Documents routes
		$router->get('documents','DocumentController@getUserDocuments');
		$router->get('documents/{id}', 'DocumentController@get');
		$router->get('documents/oai/{oaiPrimary}/{oaiSecondary}', 'DocumentController@getByOAI');
		$router->post('documents', 'DocumentController@create');
		$router->put('documents/{id}', 'DocumentController@update');
		$router->delete('documents/{id}', 'DocumentController@delete');
		$router->get('documents/{docId}/annotations', 'AnnotationController@getDocumentAnnotations');
		$router->get('documents/{docId}/annotations/{id}', 'AnnotationController@get');
		$router->get('documents/{docId}/contributors', 'DocumentContributorController@getDocumentContributors');
		$router->get('documents/{docId}/titles', 'DocumentTitleController@getDocumentTitles');
		//$router->get('documents/{id}/annotationsxml', 'DocumentController@getDocumentAnnotationsXML');
		$router->post('documents/annotationsxml', 'DocumentController@getDocumentAnnotationsXML');
		$router->get('documents/{id}/annotationsjson4latex', 'DocumentController@getDocumentAnnotationsJSON4LATEX');

		//Contributor routes
		$router->get('contributors/{id}', 'DocumentContributorController@get');
		$router->post('contributors', 'DocumentContributorController@create');
		$router->delete('contributors/{id}', 'DocumentContributorController@delete');

		//Title routes
		$router->get('titles/{id}', 'DocumentTitleController@get');
		$router->post('titles', 'DocumentTitleController@create');
		$router->delete('titles/{id}', 'DocumentTitleController@delete');

		//Annotations routes
		$router->get('annotations/{id}', 'AnnotationController@get');
		$router->get('annotations/{annotationId}/forms', 'FormController@getAnnotationForms');
		$router->get('annotations/{annotationId}/translations', 'TranslationController@getAnnotationTranslations');
		$router->get('annotations/{id}/children', 'AnnotationController@getAnnotationChildren');
		$router->post('annotations', 'AnnotationController@create');
		$router->put('annotations/{id}', 'AnnotationController@update');
		$router->delete('annotations/{id}', 'AnnotationController@delete');
		$router->post('annotations/{id}/notes', 'AnnotationController@createNote');
		$router->get('annotations/{id}/notes', 'AnnotationController@getNotes');
		
		//Forms routes
		$router->get('forms/{id}', 'FormController@get');
		$router->post('forms', 'FormController@create');
		$router->put('forms/{id}', 'FormController@update');
		$router->delete('forms/{id}', 'FormController@delete');
		$router->post('forms/{id}/notes', 'FormController@createNote');

		//Translation routes
		$router->get('translations/{id}', 'TranslationController@get');
		$router->post('translations', 'TranslationController@create');
		$router->put('translations/{id}', 'TranslationController@update');
		$router->delete('translations/{id}', 'TranslationController@delete');
		$router->post('translations/{id}/notes', 'TranslationController@createNote');

		//Recording routes
		$router->get('recordings/{id}', 'RecordingController@get');
		$router->post('recordings', 'RecordingController@create');
		$router->put('recordings/{id}', 'RecordingController@update');
		$router->delete('recordings/{id}', 'RecordingController@delete');

		//Images routes
		$router->get('images/{id}', 'ImageController@get');
		$router->post('images', 'ImageController@create');
		$router->put('images/{id}', 'ImageController@update');
		$router->delete('images/{id}', 'ImageController@delete');

		$router->post('import','ImportController@postFile');

		//Notes routes
		$router->get('notes/{id}', 'NoteController@get');
		$router->put('notes/{id}', 'NoteController@update');
		$router->delete('notes/{id}', 'NoteController@delete');

		//Notes routes
		$router->get('langisocodes/{str}', 'LangISOCodeController@getLikes');

	});

});