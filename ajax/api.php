<?php
    ini_set('display_errors', '1');
    ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
    if(!is_array($_GET)&&count($_GET)<=0){exit();}
    include('../lib.php');
    require('./vendor/autoload.php');
    use YouTube\YouTubeDownloader;
    if($_GET['type'] == 'info'){
      echo json_encode(get_video_info($_GET['v'],APIKEY));
    }elseif ($_GET['type'] == 'downlink') {
    $yt = new YouTubeDownloader();
    $u="https://www.youtube.com/watch?v=".$_GET['v'];
    $links = $yt->getDownloadLinks($u);  
    echo json_encode($links);
  }
?>