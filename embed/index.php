<?php 
if(!is_array($_GET)&&count($_GET)>0){ header("Location: ../error.php"); exit();} include("../lib.php"); 
?>
<!DOCTYPE html>
<html>
    
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <base href="<?php echo trim(Root_part()," embed/ ") ?>/" />
        <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
        <link rel="stylesheet" href="//cdn.bootcdn.net/ajax/libs/font-awesome/4.7.0/css/fontawesome.min.css" > 
				<script src="//code.jquery.com/jquery-2.2.4.min.js"></script>
				<link href="//cdn.bootcdn.net/ajax/libs/video.js/7.8.1/video-js.min.css" rel="stylesheet" />
				<script src="//cdn.bootcdn.net/ajax/libs/video.js/7.8.1/video.min.js"></script>
        
        <script type="text/javascript" src="../inc/4.js"></script>
        <link rel="stylesheet" href="../inc/theme.css" type="text/css">
    </head>
    
    <body>
        <div style="max-width:100%;height:auto">
            <?php html5_player($_GET[ 'v']); ?>
            <script>
            videojs('h5player').videoJsResolutionSwitcher();
            </script>
        </div>
    </body>

</html>