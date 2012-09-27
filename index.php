<?php

// requires

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require 'MtHaml/Environment.php';


// haml caching

class HamlCacheConfigs {
  static $cache_file = "tmp/compiled_views.json";
}

function file_read($filename) {
  $file = fopen($filename, "r");
  $contents = fread($file, filesize($filename));
  fclose($file);
  return $contents;
}

function file_write($filename, $contents) {
  $file = fopen($filename, "w");
  fwrite($file, $contents);
  fclose($file);
}

function setup_cache() {
  if ( !file_exists(HamlCacheConfigs::$cache_file) )
    file_write(HamlCacheConfigs::$cache_file, json_encode(array(array())));
}

function is_hash_outdated($key_name, $hash) {
  $hashes = json_decode( file_read(HamlCacheConfigs::$cache_file), true );
  $old_hash = $hashes[$key_name];
  return is_null($old_hash) || $hash != $old_hash;
}

function update_cache($filename, $hash) {
  $hashes[$filename] = $hash;
  $hashes = json_encode($hashes);
  file_write(HamlCacheConfigs::$cache_file, $hashes);
}

function compileHaml($template_name){
  $haml = new MtHaml\Environment('php');
  $template = "views/$template_name.haml";
  $cache_file = "tmp/compiled_views/$template_name.php";
  $hash = hash_file('md5', $template);

  if ( !file_exists($cache_file) || is_hash_outdated($template_name, $hash) ) {
    $compiled = $haml->compileString(file_get_contents($template), $template);
    file_write($cache_file, $compiled);
    update_cache($template_name, $hash);
  } else {
    $compiled = file_read($cache_file);
  }

  return $compiled;
}

setup_cache();

// init & configs

$app = new \Slim\Slim(array(
    'templates.path' => './views'
));


// routes

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


// run

$app->run();