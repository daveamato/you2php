<?php

require_once "lang.conf.php";

header("HTTP/1.0 404 Not Found");
$headtitle='ERROR！';
include("./header.php");?>

<div class="container-fluid" style="height: 480px;
    background-color: #dbdbdb;">
    <div class="container" style="height: 100%">
        <div class="row" style="height: 100%">
 <div class="col-12 justify-content-center align-self-center text-center">
     <img src="//wx3.sinaimg.cn/large/b0738b0agy1fm04l0cw4ej203w02s0sl.jpg" class="p-2" >
      <h2>The requested content was not found!</h2>
      <p>Sorry, but please try again.</p>
  </div>

  </div>
    </div>

</div>


<?php
include("./footer.php");
?>
