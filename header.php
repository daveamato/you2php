<?php
error_reporting( E_ALL&~E_NOTICE );
require_once("./lang.conf.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $headtitle ?></title>
<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport" />
<meta name="keywords" content="ChowderTube" />
<meta name="description" content="ChowderTube" />
<link rel="stylesheet" href="//cdn.bootcdn.net/ajax/libs/font-awesome/4.7.0/css/fontawesome.min.css" >
<link href="//cdn.bootcdn.net/ajax/libs/Swiper/5.4.1/css/swiper.min.css" rel="stylesheet"> 
<script src="//code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link href="//cdn.bootcdn.net/ajax/libs/video.js/7.8.1/video-js.min.css" rel="stylesheet" />
<script src="//cdn.bootcdn.net/ajax/libs/video.js/7.8.1/video.min.js"></script>
<script type="text/javascript" src="./inc/4.js"></script>
<link rel="stylesheet" href="./inc/theme.css" type="text/css">
<link rel="stylesheet" href="./inc/iconfont.css">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
<nav class="navbar navbar-expand-md bg-primary navbar-dark">
<div class="container">
	<a class="navbar-brand" href="./"><i class="fa d-inline fa-youtube-play"></i><b> <?php echo TITLENAME ?></b></a><button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar2SupportedContent" aria-controls="navbar2SupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
	<div class="collapse navbar-collapse text-center justify-content-end" id="navbar2SupportedContent">
		<ul class="navbar-nav">
		</ul>
		<div id="custom-search-input">
		     <form action="search.php" style="margin-bottom:0px">
			<div class="input-group col-md-12">
			   
				<input type="text" name="q"  class="search-query form-control" id="youtube" placeholder="Search here..."><span class="input-group-btn"><button class="btn btn-danger" type="submit" style="z-index:4"><span class="fa d-inline fa-search"></span></button></span>
				</div>
			</form>
		<script >
$("#youtube").autocomplete({
    source: function(request, response){

        var query = request.term;

        $.ajax({
            url: "./ajax/autocomplete.php?q="+query,
            dataType: 'jsonp',
            success: function(data, textStatus, request) {
               response( $.map( data[1], function(item) {
                    return {
                        label: item[0],
                        value: item[0]
                    }
                }));
            }
        });
    },

});
</script>
		</div>
	</div>
</div>
</nav>
