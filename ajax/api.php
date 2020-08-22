<?php
    //include("./lib.php");
    if(!is_array($_GET)&&count($_GET)<=0){exit();}
    include('../lib.php');
    require('../vendor/autoload.php');
    use YouTube\YouTubeDownloader;
    if($_GET['type'] == 'info'){
      echo json_encode(get_video_info($_GET['v'],APIKEY));
    }else{
    $yt = new YouTubeDownloader();
    $u="https://www.youtube.com/watch?v=".$_GET['v'];
    $links = $yt->getDownloadLinks($u);  
    echo json_encode($links);
  }
?>