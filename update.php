<?php

function echox($msg) { echo $msg . PHP_EOL; exit; }

if(!file_exists('config.json')) echox("config file not found, exiting");
$config = json_decode(file_get_contents('config.json'), true);
if(json_last_error() !== JSON_ERROR_NONE) echox("couldn't read the config file - check format, exiting");
if(!isset($config['api_url']) || empty($config['api_url'])) echox("missing/wrong api url, exiting");
if(!isset($config['installed_keys'])) $config['installed_keys'] = [];
if(!isset($config['keys_file'])) $config['keys_file'] = 'authorized_keys';
function config_save(){ global $config; file_put_contents('config.json', json_encode($config)); }

$keys = json_decode(@file_get_contents($config['api_url'], false, stream_context_create(['http' => $config['api_http_options']])), true);
if(strpos($http_response_header[0], '200') === false) echox("invalid response from api ({$http_response_header[0]}), exiting");
$ids = []; foreach($keys as $key) array_push($ids, $key['id']);

if($config['installed_keys'] == $ids) echox("no changes detected, exiting");

$x = count(array_diff($ids, $config['installed_keys']));
$y = count(array_diff($config['installed_keys'], $ids));
echo "adding $x keys, removing $y keys" . PHP_EOL;

$list = "# this file is synhronized with {$config['api_name']}" . PHP_EOL;
$list .= "# any changes made here will be overwritten" . PHP_EOL . PHP_EOL;
foreach($keys as $key) $list .= $key['key'] . PHP_EOL;

file_put_contents($config['keys_file'], $list);
$config['installed_keys'] = $ids;
config_save();
