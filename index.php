<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require 'MtHaml/Environment.php';

$compiled_templates = array();

function compileHaml($template){
  $haml = new MtHaml\Environment('php');
  if ( is_null($compiled_templates["antani"]) ) {
    $compiled = $haml->compileString(file_get_contents($template), $template);
    $compiled_templates["antani"] = $compiled;
  } else {
    $compiled = $compiled_templates["antani"];
  }

  return $compiled;
}

$app = new \Slim\Slim(array(
    'templates.path' => './views'
));

$app->get('/', function(){
  echo "home page!";
});

$app->get('/hello', function(){
  echo compileHaml('views/hello.haml');
});

$app->get('/hi', function() use ($app){
  $app->render('hi.php');
});

$app->get('/hello/:name', function($name){
  echo "Hello, $name";
});

$app->run();