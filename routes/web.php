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

/* $router->get('/aaa', function () use ($router) {
    return $router->app->version();
}); */


$router->post('/certificados', ['uses' => 'ApiController@cantidad_concepto_certificado', 'as' => 'certificado']);
$router->post('/fechas', ['uses' => 'ApiController@anio_primera_mat_egresado', 'as' => 'fechas']);
$router->post('/fechas_cred_posgrado', ['uses' => 'ApiController@fecha_primera_mat_egreso_creditos_posgrado', 'as' => 'fechas_cred_posgrado']);



$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/',  ['uses' => 'ApiController@cantidad_concepto_certificado']);  
});
