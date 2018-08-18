<?php

use Fridde\CountryChecker;

require './vendor/autoload.php';
define('BASE_DIR', __DIR__);

$api_root = 'https://api.what3words.com/v2/reverse';
$settings = parse_ini_file('key.ini');
$key = $settings['key'];

const PAUSE = 0.2 * 1000000;
const ROUNDS = 50;

$client = new \GuzzleHttp\Client();

$SwedenChecker = new CountryChecker('sweden');

$results = [];

foreach(range(1,ROUNDS) as $i){
    $coordinate = $SwedenChecker->createCoordinateInCountry();

    $query = [
        'key' => $key,
        'coords' => implode(',', $coordinate),
        'lang' => 'sv'
    ];

   $response = $client->request('GET', $api_root, ['query' => $query]);
   $results[] = json_decode($response->getBody(), true);
   usleep(PAUSE);
}

$dir = opendir('words');
$files = [];
while(false !== ($file = readdir($dir))) {
    if(pathinfo($file, PATHINFO_EXTENSION) === 'txt'){
        $files[] = $file;
    }
}
if(count($files) > 0){
    $old_words = file_get_contents('words/' . max($files));
}
$old_words = $old_words ?? '';
$new_words = implode('.', array_column($results, 'words'));

$words = $old_words . '.' . $new_words;
$words = array_unique(explode('.', $words));
sort($words);
$words = implode('.', $words);

$file_name = 'words_' . str_replace(['-', ':'], '', date('c'));
$file_name = substr($file_name, 0, strpos($file_name, '+')) . '.txt';

file_put_contents('words/' . $file_name, $words);
