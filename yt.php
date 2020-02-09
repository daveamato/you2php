<?php
if (file_exists('sig1.php')) {
    unlink('sig1.php');
}
function quality($itag) {
        switch ($itag) {
            case "17":
                return "144P";
                break;
            case "278":
                return "144P";
                break;
            case "36":
                return "240P";
                break;
            case "242":
                return "240P";
                break;
            case "18":
                return "360P";
                break;
            case "243":
                return "360P";
                break;
            case "43":
                return "360P";
                break;
            case "35":
                return "480P";
                break;
            case "44":
                return "480P";
                break;
            case "135":
                return "<img src='https://i.imgur.com/sAiZdSr.png'></img> 480P";
                break;
            case "244":
                return "480P";
                break;
            case "22":
                return "720P";
                break;
            case "136":
                return "720P";
                break;
            case "247":
                return "720P";
                break;
            case "137":
                return "1080P";
                break;
            case "248":
                return "1080P";
                break;
            case "299":
                return "1080P (60 FPS)";
                break;
            case "138":
                return "2K";
                break;
            case "264":
                return "2K";
                break;
            case "271":
                return "2K";
                break;
            case "266":
                return "4K";
                break;
            case "313":
                return "4K (60 FPS)";
                break;
            case "139":
                return "<img src='https://i.imgur.com/H6TF3Sc.png'></img> 48 Kbps";
                break;
            case "140":
                return "<img src='https://i.imgur.com/cYJzY9F.png'></img> 128 Kbps";
                break;
            case "141":
                return "<img src='https://i.imgur.com/cYJzY9F.png'></img> 128 Kbps";
                break;              
            case "171":
                return "<img src='https://i.imgur.com/H6TF3Sc.png'></img> 128 Kbps";
                break;
            case "249":
                return "<img src='https://i.imgur.com/sAiZdSr.png'></img> 50k";
                break;
            case "250":
                return "<img src='https://i.imgur.com/H6TF3Sc.png'></img> 70k";
                break;
            case "251":
                return "<img src='https://i.imgur.com/H6TF3Sc.png'></img> 160k";
                break;              
            default:
                return $itag;
                break;
        }
    } 
if (isset($_GET['url'])) {
    parse_str( parse_url( $_GET['url'], PHP_URL_QUERY ), $vars );
    $id=$vars['v'];
 
}else{
    echo 'no url';
}

$a= file_get_contents('https://www.youtube.com/embed/'.$id);

$ccc=explode('jsbin/player_ias', $a);
$ddd=explode('/', $ccc[1]);
$jdl=explode('<title>', $a);
$jdl=explode('</title>', $jdl[1]);
$judul=$jdl[0];



function getchiper($decipherScript){
        $decipherPatterns = explode('.split("")', $decipherScript);
        unset($decipherPatterns[0]);
        foreach ($decipherPatterns as $value) {
            $value = explode('.join("")', explode('}', $value)[0]);
            if (count($value) === 2) {
                $value = explode(';', $value[0]);
                array_pop($value);
                unset($value[0]);
                $decipherPatterns = implode(';', $value);
                break;
            }
        }
        preg_match_all('/(?<=;).*?(?=\[|\.)/', $decipherPatterns, $deciphers);
        if ($deciphers && count($deciphers[0]) >= 2) {
            $deciphers = $deciphers[0][0];
        $deciphersObjectVar = $decipherPatterns ;
        $decipher = explode($deciphers . '={', $decipherScript)[1];
        $decipher = str_replace(["\n", "\r"], '', $decipher);
        $decipher = explode('}};', $decipher)[0];
        $decipher = explode('},', $decipher);
        // Convert deciphers to object
        $deciphers = [];

        foreach ($decipher as &$function) {
            $deciphers[explode(':function', $function)[0]] = explode('){', $function)[1];
        }
        // Convert pattern to array
        $decipherPatterns = str_replace($deciphersObjectVar . '.', '', $decipherPatterns);
        $decipherPatterns = str_replace($deciphersObjectVar . '[', '', $decipherPatterns);
        $decipherPatterns = str_replace(['](a,', '(a,'], '->(', $decipherPatterns);
        $decipherPatterns = explode(';', $decipherPatterns);
        $patterns =$decipherPatterns;
            $deciphers =$deciphers; 
        for ($i=0; $i < count($patterns); $i++) {
            $executes = explode('->', $patterns[$i]);
            $execute=explode('.', $executes[0]);
            $number = intval(str_replace(['(', ')'], '', $executes[1]));
            $execute = $deciphers[$execute[1]];
            switch ($execute) {
                case 'a.reverse()':
                    $processSignature = '$reverse';
                break;
                case 'var c=a[0];a[0]=a[b%a.length];a[b]=c':   
                    $processSignature= '$length';
                break;
                case 'var c=a[0];a[0]=a[b%a.length];a[b%a.length]=c':
                $processSignature= '$lengtha';
                break;
                case 'a.splice(0,b)':
                    $processSignature= '$splice';
                break;
                default:
                    die("\n==== Decipher dictionary was not found ====");

                break;
            }
        $myfile = fopen("sig1.php", "a+") or die("Unable to open file!");
        if ($i==0) {
            fwrite($myfile, '<?php $a = str_split($s);');
        }
        fwrite($myfile, $processSignature.'($a,'.$number.');');
        fclose($myfile);
        }
        }
        }

        function sig($s){
        $reverse=function(&$a){
                $a = array_reverse($a);
            };
            $splice=function(&$a, $b){
                 $a = array_slice($a, $b);
            };
            $length = function(&$a, $b){
                $c = $a[0];
                $a[0] = $a[$b % count($a)];
                $a[$b] = $c;
            };
            $lengtha = function(&$a, $b){
                $c = $a[0];
        $a[0] = $a[$b%count($a)];
        $a[$b%count($a)] = $c;
            };

            
        include('sig1.php');
        return join('',$a);
        }

$gsts= file_get_contents('https://www.youtube.com/yts/jsbin/player_ias'.$ddd[0].'/en_US/base.js');
getchiper($gsts);
$data = file_get_contents("https://www.youtube.com/get_video_info?video_id=".$id."&asv=3&el=detailpage&hl=en_US");
parse_str($data,$info);
    $streams = $info['player_response']; 
        $jsn_str=str_replace("\u0026","&",$streams);
        $streamin_data_json=json_decode($jsn_str, true);
$stream=$streamin_data_json["streamingData"]["formats"][0];
if (isset($stream["cipher"])) {
    parse_str($stream["cipher"],$dturl);
 echo '<a href="'.$dturl['url'].'&sig='.sig($dturl['s']).'">Download</a> '.quality($stream["itag"]).'</br>';
}else{
echo '<a href="'.$stream['url'].'">Download</a> '.quality($stream["itag"]).'</br>';
}

 

foreach ($streamin_data_json["streamingData"]["adaptiveFormats"] as $stream) {
if (isset($stream["cipher"])) {
    parse_str($stream["cipher"],$dturl);
    if(quality($stream["itag"]) == "480P") {
        $LocationUrl = $dturl['url'].'&sig='.sig($dturl['s']);
    header("Location: $LocationUrl");
    exit();
    }
    
 //echo '<a href="'.$dturl['url'].'&sig='.sig($dturl['s']).'">Download</a> '.quality($stream["itag"]).'</br>';
//}else{
//echo '<a href="'.$stream['url'].'">Download</a> '.quality($stream["itag"]).'</br>';
//}
  
 
}

?>