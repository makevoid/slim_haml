<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require 'MtHaml/Environment.php';

$compiled_templates = array();



function compileHaml($template_name){
  $haml = new MtHaml\Environment('php');
  $template = "views/$template_name.haml";
  $cache_file = "tmp/compiled_views/$template_name.haml";
  if ( !file_exists($cache_file) ) {
    $file = fopen($cache_file, "w");
    $compiled = $haml->compileString(file_get_contents($template), $template);
    fwrite($file, $compiled);
    fclose($file);
  } else {
    $compiled = readfile($cache_file);
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
  echo compileHaml('hello');
});

$app->get('/hi', function() use ($app){
  $app->render('hi.php');
});

$app->get('/hello/:name', function($name){
  echo "Hello, $name";
});

$app->run();