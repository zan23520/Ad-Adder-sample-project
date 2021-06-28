<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

use Slim\Exception\NotFoundException;

require __DIR__ . '/vendor/autoload.php'; // "/../vendo

//vključi kontroler za Projekt
include_once("Models/model_project.php");

//vključi kontroler za Materiale
include_once("Models/model_material.php");

//vključi razred za ajaxResponse pakete
include_once("Models/class_ajaxResponse.php");

$app = AppFactory::create();

//potrebno za delovanje v subdirectory
$app->setBasePath('/ProjektPraksa');


$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

/*
function getPost($request){
    $postData = json_decode($request->getBody(), true);
    var_dump($postData);
    return $postData['post']; 
} */

/**
 * Get user's projects, URL TESTING
 * @param {number} userId 
 */
$app->get('/getProjects/{userId}', function (Request $request, Response $response, array $args) 
{
    //pridobi get data: userId
    //naredi poizvedbo s project modelom
    //vrni ustrezen response

    $project = new Projects();
    $package = new ajaxResponse();

    $params = json_decode($request->getBody(), true);
    //$params = $request->getQueryParams(); EMPTY!!
    $userId = $params['userId'];

    $projects = $project->getProjects($userId);

    $returnData = $package->createJson(
        true, 
        "Projekti sprejeti.",
        $projects
    );
 
    $response->getBody()->write($returnData);
    return $response;
});

/**
 * Get user's project, URL TESTING
 * @param {number} projectId 
 */
$app->get('/getMaterials/{projectId}', function (Request $request, Response $response, array $args) 
{
    //pridobi post data: projectId, (userId -optional)
    //naredi poizvedbi za ime projekta in njegove materiale z material modelom
    //vrni ustrezen response

    $material = new Materials();
    $package  = new ajaxResponse();

    $projectId = $args['projectId'];

    $materials = $material->getMaterials($projectId);

    $returnData = $package->createJson(
        true, 
        "Materiali sprejeti.",
        $materials
    );

    $response->getBody()->write($returnData);
    return $response;
});

/**
 * Get user's projects
 * @param {number} userId 
 */
$app->post('/Projects', function (Request $request, Response $response, $args) // use ($app)
{
    //pridobi post data: userId
    //naredi poizvedbo s project modelom
    //vrni ustrezen response

    $project = new Projects();
    $package = new ajaxResponse();

    $params = json_decode($request->getBody(), true);

    $userId = $params['userId'];

    $projects = $project->getProjects($userId);

    $returnData = $package->createJson(
        true, 
        "Projekti sprejeti.",
        $projects
    );
 
    $response->getBody()->write($returnData);
    return $response;
});

/**
 * Get user's project's materials
 * @param {number} projectId
 * @param {number} userId - optional?
 */
$app->post('/Materials', function (Request $request, Response $response, array $args) 
{
    //pridobi post data: projectId, (userId -optional)
    //naredi poizvedbi za ime projekta in njegove materiale z material modelom
    //vrni ustrezen response

    $material = new Materials();
    $package  = new ajaxResponse();

    $params = json_decode($request->getBody(), true);
    //var_dump($params);
    //$params = $request->getBody();
    //$postData = json_decode($params['post'], true);
    $projectId = $params['projectId'];
    //$userId = GET FROM SESSION!!!!!!!!!!!

    $materials = $material->getMaterials($projectId);

    $returnData = $package->createJson(
        true, 
        "Materiali sprejeti.",
        $materials
    );

    $response->getBody()->write($returnData);
    return $response;
});

/**
 * Create a new project for user
 * @param {number} userId
 * @param {string} name
 */
$app->post('/NewProject', function (Request $request, Response $response, array $args) 
{
    //pridobi post data: userId, name
    //naredi poizvedbo, vstavi nov projekt s project model
    //vrni ustrezen response

    $project = new Projects();
    $package = new ajaxResponse();

    $params = json_decode($request->getBody(), true);
    $userId = $params['userId'];
    $name   = $params['name'];

    $projects = $project->newProject($userId, $name);

    if ($projects) {
        $msg = "Projekt ustvarjen.";
    } else {
        $msg = "Napaka pri vnosu.";
    }

    $returnData = $package->createJson(true, $msg);

    $response->getBody()->write($returnData);
    return $response;
});

/**
 * Update project's name
 * @param {number} projectId
 * @param {string} newName
 */
$app->post('/UpdateProject', function (Request $request, Response $response, array $args) 
{
    //pridobi post data: projectId, name
    //naredi poizvedbo, spremeni ime projekta s project model
    //vrni ustrezen response

    $project = new Projects();
    $package = new ajaxResponse();

    $params    = json_decode($request->getBody(), true);
    $projectId = $params['projectId'];
    $newName   = $params['newName'];

    $projects = $project->updateProject($projectId, $newName);

    if ($projects) {
        $msg = "Projekt spremenjen.";
    } else {
        $msg = "Napaka pri spremembi.";
    }
    
    $returnData = $package->createJson(true, $msg);

    $response->getBody()->write($returnData);
    return $response;
});

/**
 * Delete project and its materials
 * @param {number} adId
 * @param {number} projectId
 */
$app->post('/DeleteProject', function (Request $request, Response $response, array $args) 
{
    //pridobi post data: projectId
    //naredi poizvedbo, zbriši DB podatke in datoteke materialov projekta
    //glede na uspešnost vrni ustrezen response

    $project = new Projects();
    $package = new ajaxResponse();

    $params    = json_decode($request->getBody(), true);
    $projectId = $params['projectId'];
    //$userId    = $params['userId'];

    $delete = $project->deleteProject($projectId); //, $userId);

    if ($delete) {
        $msg = "Projekt izbrisan.";
    } else {
        $msg = "Napaka pri izbrisu.";
    }

    $returnData = $package->createJson(true, $msg);

    $response->getBody()->write($returnData);
    return $response;
});


/**
 * Update material's name and comment
 * @param {number} matId
 * @param {string} newName
 * @param {string} newName
 */
$app->post('/UpdateMaterial', function (Request $request, Response $response, array $args) 
{
    //pridobi post data: matId, newName, komentar
    //naredi poizvedbo, spremeni ime materiala in komentar
    //vrni ustrezen response

    $project = new Projects();
    $package = new ajaxResponse();

    $params  = json_decode($request->getBody(), true);
    $matId   = $params['matId'];
    $newName = $params['newName'];
    $comment = $params['comment'];

    $projects = $project->updateProject($matId, $newName, $comment);

    if ($projects) {
        $msg = "Zakomentirano!";
    } else {
        $msg = "Napaka pri spremembi.";
    }
    
    $returnData = $package->createJson(true, $msg);

    $response->getBody()->write($returnData);
    return $response;
});

/**
 * Create a new project for user
 * @param {number} adId
 * @param {number} projectId
 */ //DATOTEKE ŠE NE IZBRIŠEM!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$app->post('/DeleteMaterial', function (Request $request, Response $response, array $args) 
{
    //pridobi post data: adId, projectId
    //naredi poizvedbo, zbriši podatke in datoteko materiala z material model
    //glede na uspešnost vrni ustrezen response

    $material = new Materials();
    $package  = new ajaxResponse();

    $params    = json_decode($request->getBody(), true);
    $projectId = $params['projectId'];
    $adId      = $params['adId'];

    $delete = $material->DeleteMaterial($adId, $userId);

    if ($delete) {
        $msg = "Material izbrisan.";
    } else {
        $msg = "Napaka pri izbrisu.";
    }

    $returnData = $package->createJson(true, $msg);

    $response->getBody()->write($returnData);
    return $response;
});


$app->run();

// EOF