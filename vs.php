<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

$vv = isset($_GET['vv']) ? $_GET['vv'] : die('no ID given');

require('./vendor/autoload.php');
use YouTube\YouTubeDownloader;



$yt = new YouTubeDownloader();

$links = $yt->getDownloadLinks('https://www.youtube.com/watch?v='.$vv);


if (isset($links['22'])) {
	$url = $links['22']['url'];
} elseif (isset($links['18'])) {
	$url = $links['18']['url'];
} else {
	$url = $links[0]['url'];
}

header("Location: $url");