<?php

$providers = [
  [
    'api_name' => 'github',
    'api_url' => 'https://api.github.com/users/%username%/keys',
    'api_http_options' => [
      'method' => 'GET',
      'header' => [
        'User-Agent: PHP'
      ]
    ],
    'params' => ['username']
  ],
  [
    'api_name' => 'gitea',
    'api_url' => '%server%/api/v1/users/%username%/keys',
    'api_http_options' => [
      'method' => 'GET',
      'header' => [
        'Authorization: token %token%'
      ]
    ],
    'params' => ['server', 'username', 'token']
  ]
];

if(file_exists('config.json')) echo 'warning: by continuing this setup, you will replace your existing config.json file! press ctrl-c to exit' . PHP_EOL;

echo 'available key sources' . PHP_EOL;
foreach($providers as $key => $val) echo $key . ') ' . $val['api_name'] . PHP_EOL;
while(!isset($cli_provider) || !is_numeric($cli_provider) || !array_key_exists($cli_provider,$providers)){
  echo 'choose the number: ';
  $cli_provider = stream_get_line(STDIN, 1024, PHP_EOL);
}

$config = [];

if(isset($providers[$cli_provider]['params'])){
  $config = $providers[$cli_provider];
  $params = $config['params']; unset($config['params']);

  $cli_param = [];
  foreach($params as $param){
    while(!isset($cli_param[$param])){
      echo "enter the value for the param '{$param}': ";
      $cli_param[$param] = stream_get_line(STDIN, 1024, PHP_EOL);
    }

    $config['api_url'] = str_replace('%'.$param.'%', $cli_param[$param], $config['api_url']);
    if(isset($config['api_http_options']['header'])) foreach($config['api_http_options']['header'] as $key => $val){
      $config['api_http_options']['header'][$key] = str_replace('%'.$param.'%', $cli_param[$param], $config['api_http_options']['header'][$key]);
    }
  }
}

$default_file = getenv('HOME') . '/.ssh/authorized_keys';
while(!isset($cli_file)){
  echo "path of your authorized_keys file [{$default_file}]: ";
  $cli_file = stream_get_line(STDIN, 1024, PHP_EOL);
}

$config['keys_file'] = !empty($cli_file) ? $cli_file : $default_file;

echo 'writing new config into config.json' . PHP_EOL;
file_put_contents('config.json', json_encode($config));

echo 'done' . PHP_EOL;
