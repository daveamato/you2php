<?php
/**
 * ChowderTube
 * @author ch0wder
 * @version v2.0
 * @description An open source proxy to YouTube
 */
require_once('./lang.conf.php');
require_once('./config.php');
require('./vendor/autoload.php');
use YouTube\YouTubeDownloader;

 function get_data($url){
    if (!function_exists("curl_init")) {
		$f = file_get_contents($url);
	} else {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_REFERER, 'https://www.youtube.com/');
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.91 Safari/534.30");
		$f = curl_exec($ch);
		curl_close($ch);
	}
   return $f;
}

function get_trending($apikey=APIKEY,$max='18',$pageToken='',$regionCode='US'){
    $apilink='https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&chart=mostPopular&regionCode='.$regionCode.'&maxResults='.$max.'&key='.$apikey.'&pageToken='.$pageToken;
     return json_decode(get_data($apilink),true);
}


 function get_video_info($id,$apikey){
    $apilink='https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id='.$id.'&key='.$apikey;
     return json_decode(get_data($apilink),true);
}


function get_channel_info($cid,$apikey){
   $apilink='https://www.googleapis.com/youtube/v3/channels?part=snippet,contentDetails,statistics&hl=zh&id='.$cid.'&key='.$apikey;
   return json_decode(get_data($apilink),true);
}

function get_related_video($vid,$apikey){
   $apilink='https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=24&relatedToVideoId='.$vid.'&key='.$apikey;
   return json_decode(get_data($apilink),true);
}


function get_channel_video($cid,$pageToken='',$apikey,$regionCode='US'){
   $apilink='https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&maxResults=50&type=video&regionCode='.$regionCode.'&hl=en-US&channelId='.$cid.'&key='.$apikey.'&pageToken='.$pageToken;
   return json_decode(get_data($apilink),true);
}


function videoCategories($apikey,$regionCode='US'){
   $apilink='https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&regionCode='.$regionCode.'&hl=en-US&key='.$apikey;
   return json_decode(get_data($apilink),true);
}

function categorieslist($id){
   $data=array(
   '1' => 'Movie and Animation',
   '2' => 'Car',
   '10' => 'Music',
   '15' => 'Pets and Animals',
   '17' => 'Sports', 
   '18' => 'Short Film',
   '19' => 'Travel and Activities',
   '20' => 'Game',
   '21' => 'Video Blogs',
   '22' => 'Characters and Blogs',
   '23' => 'Comedy',
   '24' => 'Entertainment',
   '25' => 'News & Politics', 
   '26' => 'DIY and Life Encyclopedia',
   '27' => 'Education',
   '28' => 'Science and Technology',
   '30' => 'Movie',
   '31' => 'Anime/Animation',
   '32' => 'Action/Adventure',
   '33' => 'Classic',
   '34' => 'Comedy',
   '35' => 'Documentary',
   '36' => 'Drama',
   '37' => 'Family Film',
   '38' => 'Foreign',
   '39' => 'Horror Film',
   '40' => 'Sci-Fi/Fantasy',
   '41' => 'Thriller',
   '42' => 'Short Film',
   '43' => 'Program',
   '44' => 'Trailer');
     if($id=='all'){
     return $data;    
     }else{
      return $data[$id];   
     }
}

function Categories($id,$apikey,$pageToken='',$order='relevance',$regionCode='US'){
   $apilink='https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&&regionCode='.$regionCode.'&hl=en-US&maxResults=48&videoCategoryId='.$id.'&key='.$apikey.'&order='.$order.'&pageToken='.$pageToken;
   return json_decode(get_data($apilink),true);
}

function get_search_video($query,$apikey,$pageToken='',$type='video',$order='relevance',$regionCode='US'){
   $apilink='https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=48&order='.$order.'&type='.$type.'&q='.$query.'&key='.$apikey.'&pageToken='.$pageToken;
   return json_decode(get_data($apilink),true);
}

function covtime($youtube_time){
    $start = new DateTime('@0');
    $start->add(new DateInterval($youtube_time));
    if(strlen($youtube_time)<=7){
      return $start->format('i:s');
    }else{
     return $start->format('H:i:s');
    }

}

function format_date($time){
    $t=strtotime($time);
    $t=time()-$t;
    $f=array(
    '31536000'=>'Years',
    '2592000'=>'Months',
    '604800'=>'Weeks',
    '86400'=>'Days',
    '3600'=>'Hours',
    '60'=>'Minutes',
    '1'=>'Seconds'
    );
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c .' ' . $v.' ago';
        }
    }
}


function str2time($ts) {
 return date("Y-m-d H:i", strtotime($ts));
}

function convertviewCount($value){
    if($value <= 10000){
    $number = $value;
    }else{
      $number = $value / 1000 ;
      $number = round($number,1).'K';

    }

    return $number;
}

function get_banner($a,$apikey){
   $apilink='https://www.googleapis.com/youtube/v3/channels?part=brandingSettings&id='.$a.'&key='.$apikey;
   $json=json_decode(get_data($apilink),true);
  if (array_key_exists('bannerTabletImageUrl',$json['items'][0]['brandingSettings']['image'])){
  return $json['items'][0]['brandingSettings']['image']['bannerTabletImageUrl'];
 }else{
  return 'https://c1.staticflickr.com/5/4546/24706755178_66c375d5ba_h.jpg';
 }
}
$videotype=array(
    '3GP144P' => array('3GP','144P','3gpp'),
    '360P' => array('MP4','360P','mp4'),
    '720P' => array('MP4','720P','mp4'),
    'WebM360P' => array('webM','360P','webm'),
    'Unknown' => array('N/A','N/A','3gpp'),
    );

require_once(dirname(__FILE__).'/inc/phpQuery.php');
require_once(dirname(__FILE__).'/inc/QueryList.php');
use QL\QueryList;
function get_related_channel($id){
    $channel='https://www.youtube.com/channel/'.$id;
    $rules = array(
    'id' => array('.branded-page-related-channels .branded-page-related-channels-list li','data-external-id'),
    'name' => array('.branded-page-related-channels .branded-page-related-channels-list li .yt-lockup .yt-lockup-content .yt-lockup-title a','text'),
);

return $data = QueryList::Query(get_data($channel),$rules)->data;
}


function random_recommend(){
   $dat=get_data('https://www.youtube.com/?gl=US&hl=en-US'); 
   $rules = array(
    't' => array('#feed .individual-feed .section-list li .item-section li .feed-item-container .feed-item-dismissable .shelf-title-table .shelf-title-row h2 .branded-page-module-title-text','text'),
    'html' => array('#feed .individual-feed .section-list li .item-section li .feed-item-container .feed-item-dismissable .compact-shelf .yt-viewport .yt-uix-shelfslider-list','html'),
        );

    $rules1 = array(
    'id' => array('li .yt-lockup ','data-context-item-id'),
    'title' => array('li .yt-lockup .yt-lockup-dismissable .yt-lockup-content .yt-lockup-title a','text'),
        );

    $data = QueryList::Query($dat,$rules)->data;

    $ldata=array();
    foreach ($data as $v) {
       $d = QueryList::Query($v['html'],$rules1)->data;
       $ldata[]=array(
           't'=> $v['t'],
           'dat' => $d
           );
    }
    array_shift($ldata);
    return $ldata;
}


function video_down($v,$name){
$yt = new YouTubeDownloader();
$links = $yt->getDownloadLinks("https://www.youtube.com/watch?v=$v");
echo '<table class="table table-hover"><thead><tr>
      <th>Format</th>
      <th>Type</th>
      <th>Download</th>
    </tr>
  </thead>';
foreach ($links as $value) {
    global $videotype;
echo ' <tbody>
    <tr>

      <td>'.$videotype[$value['format']][0].'</td>
      <td>'.$videotype[$value['format']][1].'</td>
      <td><a href="'.ROOT_PART.'downvideo.php?v='.$v.'&quality='.$value['format'].'&name='.$name.'&format='.$videotype[$value['format']][2].'" target="_blank" class="btn btn-outline-success btn-sm">Download</a></td>
    </tr></tbody>';
    }
    echo '</table>';
}


function get_thumbnail_code($vid){
$thumblink='https://img.youtube.com/vi/'.$vid.'/maxresdefault.jpg';
$oCurl = curl_init();
$header[] = "Content-type: application/x-www-form-urlencoded";
$user_agent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36";
curl_setopt($oCurl, CURLOPT_URL, $thumblink);
curl_setopt($oCurl, CURLOPT_HTTPHEADER,$header);
curl_setopt($oCurl, CURLOPT_HEADER, true);
curl_setopt($oCurl, CURLOPT_NOBODY, true);
curl_setopt($oCurl, CURLOPT_USERAGENT,$user_agent);
curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt($oCurl, CURLOPT_POST, false);
$sContent = curl_exec($oCurl);
$headerSize = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
curl_close($oCurl);
if($headerSize == '404'){
  return 'https://img.youtube.com/vi/'.$vid.'/hqdefault.jpg';
}else{
  return 'https://img.youtube.com/vi/'.$vid.'/maxresdefault.jpg';
}
}


function Hislist($str,$apikey){
    $str=str_replace('@',',',$str);
    $apilink='https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id='.$str.'&key='.$apikey;
   return json_decode(get_data($apilink),true);
}


$CountryID=array(
    'DZ' => '阿尔及利亚',
    'AR' => '阿根廷',
    'AE' => '阿拉伯联合酋长国',
    'OM' => '阿曼',
    'AZ' => '阿塞拜疆',
    'EG' => '埃及',
    'IE' => '爱尔兰',
    'EE' => '爱沙尼亚',
    'AT' => '奥地利',
    'AU' => '澳大利亚',
    'PK' => '巴基斯坦',
    'BH' => '巴林',
    'BR' => '巴西',
    'BY' => '白俄罗斯',
    'BG' => '保加利亚',
    'BE' => '比利时',
    'IS' => '冰岛',
    'PR' => '波多黎各',
    'PL' => '波兰',
    'BA' => '波斯尼亚和黑塞哥维那',
    'DK' => '丹麦',
    'DE' => '德国',
    'RU' => '俄罗斯',
    'FR' => '法国',
    'PH' => '菲律宾',
    'FI' => '芬兰',
    'CO' => '哥伦比亚',
    'GE' => '格鲁吉亚共和国',
    'KZ' => '哈萨克斯坦',
    'KR' => '韩国',
    'NL' => '荷兰',
    'ME' => '黑山共和国',
    'CA' => '加拿大',
    'CN' => '中国',
    'GH' => '加纳',
    'CZ' => '捷克共和国',
    'ZW' => '津巴布韦',
    'QA' => '卡塔尔',
    'KW' => '科威特',
    'HR' => '克罗地亚',
    'KE' => '肯尼亚',
    'LV' => '拉脱维亚',
    'LB' => '黎巴嫩',
    'LT' => '立陶宛',
    'LY' => '利比亚',
    'LU' => '卢森堡公国',
    'RO' => '罗马尼亚',
    'MY' => '马来西亚',
    'MK' => '马其顿',
    'US' => 'United States',
    'PE' => '秘鲁',
    'MA' => '摩洛哥',
    'MX' => '墨西哥',
    'ZA' => '南非',
    'NP' => '尼泊尔',
    'NG' => '尼日利亚',
    'NO' => '挪威',
    'PT' => '葡萄牙',
    'JP' => '日本',
    'SE' => '瑞典',
    'CH' => '瑞士',
    'RS' => '塞尔维亚',
    'SN' => '塞内加尔',
    'SA' => '沙特阿拉伯',
    'LK' => '斯里兰卡',
    'SK' => '斯洛伐克',
    'SI' => '斯洛文尼亚',
    'TW' => '台湾',
    'TH' => '泰国',
    'TZ' => '坦桑尼亚',
    'TN' => '突尼斯',
    'TR' => '土耳其',
    'UG' => '乌干达',
    'UA' => '乌克兰',
    'ES' => '西班牙',
    'GR' => '希腊',
    'HK' => '香港',
    'SG' => '新加坡',
    'NZ' => '新西兰',
    'HU' => '匈牙利',
    'JM' => '牙买加',
    'YE' => '也门',
    'IQ' => '伊拉克',
    'IL' => '以色列',
    'IT' => '意大利',
    'IN' => '印度',
    'ID' => '印尼',
    'GB' => 'United. Kingdom',
    'JO' => '约旦',
    'VN' => '越南',
    'CL' => '智利',
    );
function get_country($c){
    global $CountryID;
    return  $CountryID[$c];
}

function strencode($string,$key='09KxDsIIe|+]8Fo{YP<l+3!y#>a$;^PzFpsxS9&d;!l;~M>2?N7G}`@?UJ@{FDI') {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string,$i,1));
        if (@$j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        @$j++;
    @$hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return 'Urls://'.$hash;
}
function strdecode($string,$key='09KxDsIIe|+]8Fo{YP<l+3!y#>a$;^PzFpsxS9&d;!l;~M>2?N7G}`@?UJ@{FDI') {
    $string= ltrim($string, 'Urls://');
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if (@$j == $keyLen) { @$j = 0; }
        $ordKey = ord(substr($key,@$j,1));
        @$j++;
        @$hash .= chr($ordStr - $ordKey);
    }
    return $hash;
}


function shareit($id,$title='ChowderTube'){
    $pic=ROOT_PART.'/thumbnail.php?vid='.$id;
    $url=ROOT_PART.'watch-'.$id.'.html';
    $title=str_replace('&','||',$title);
    $title=str_replace('"',' ',$title);
     $title=str_replace("'",' ',$title);
    $des='【ChowderTube】《'.$title.'》';
    return "<div id='share'>
  <a class='icoqz' href='https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=".$url."&desc=".$des."&title=".$titlel."
&pics=".$pic."' target='blank' title='QQ'><i class='iconfont icon-qqkongjian icofontsize'></i></a>

  <a class='icotb' href='http://tieba.baidu.com/f/commit/share/openShareApi?title=".$title."&url=".$url."&to=tieba&type=text&relateUid=&pic=".$pic."&key=&sign=on&desc=&comment=".$title."' target='blank' title='Share to Baidu'><i class='iconfont icon-40 icofontsize'></i></a>

  <a class='icowb' href='http://service.weibo.com/share/share.php?url=".$url."&title=".$des."&pic=".$pic."&sudaref=".$title."' target='blank' title='Share to Weibo'><i class='iconfont icon-weibo icofontsize'></i></a>

  <a class='icobi' href='https://member.bilibili.com/v/#/text-edit' target='blank' title='Share to BiliBili'><i class='iconfont icon-bilibili icofontsize'></i></a>

  <a class='icowx' href='http://api.addthis.com/oexchange/0.8/forward/wechat/offer?url=".ROOT_PART."watch.php?v=".$id."' target='blank' title='Share to AddThis' ><i class='iconfont icon-weixin icofontsize'></i></a>
</div>
 <div class='form-group'><div class='d-inline-block h6 pt-3 col-12'>
   Info：
 </div>
    <textarea style='resize:none;height: auto' class='form-control d-inline align-middle col-12 icoys icontext' id='inputs' type='text' rows='5' placeholder='Default Input'><iframe height=498 width=510 src=&quot;".ROOT_PART."embed/?v=".$id."&quot; frameborder=0 &quot;allowfullscreen&quot;></iframe></textarea>
    
    <button type='submit' class='btn btn-primary align-middle col-12 mt-2' onclick='copytext1()'>Copy</button></div>";
    
}

function html5_player($id){
		use YouTube\YouTubeDownloader;
    $yt = new YouTubeDownloader();
    $links = $yt->getDownloadLinks('https://www.youtube.com/watch?v='.$id);
    if(count($links)!=1){
        echo'<video id="h5player"  class="video-js vjs-fluid mh-100 mw-100" loop="loop" width="100%" preload="auto"  webkit-playsinline="true" playsinline="true" x-webkit-airplay="true" controls="controls" controls preload="auto" width="100%" poster="'.Root_part().'thumbnail.php?type=maxresdefault&vid='.$id.'" data-setup=\'\'>';
        if(array_key_exists('22',$links)){
        echo '<source src="./vs.php?vv='.$id.'&quality=720" type=\'video/mp4\' res="720" label=\'720P\'/>';
            };
        echo '<source src="./vs.php?vv='.$id.'&quality=360" type=\'video/mp4\' res="360" label=\'360P\'/>';
     $slink='https://www.youtube.com/api/timedtext?type=list&v='.$id;
     $vdata=get_data($slink);
     @$xml = simplexml_load_string($vdata);
     $array1=json_decode(json_encode($xml), true);
     $arr=array();
    
     if(array_key_exists('track',$array1) && array_key_exists('0',$array1['track'])){
         if (array_key_exists('track', $array1) && array_key_exists('0', $array1['track'
    									   ])) {
    	foreach ($array1['track'] as $val) {if ($val['@attributes']['lang_code'] == 'en') {
    			$arr[$val['@attributes']['lang_code']] = "
    <track kind='captions' src='".ROOT_PART."tracks.php?vtt={$id}&lang=" . $val['@attributes']
    ['lang_code'] . "' srclang='" . $val['@attributes']['lang_code'] . "' label='" .
    				   $val['@attributes']['lang_original'] . "'/>";
    		}
    	}
    	foreach ($arr as $k => $v) {
    	    switch ($k) {
    			case 'en':
    				$arr[$k] = substr_replace($v, ' default ', -2,0);
    				break;
    		}
    		break;
    	}
    	foreach($arr as $vl ){
          echo $vl.PHP_EOL;
      }
    }
     }elseif(array_key_exists('track',$array1)){
     echo "<track kind='captions' src='".ROOT_PART."tracks.php?vtt={$id}&lang=".$array1['track']['@attributes']['lang_code']."' srclang='".$array1['code']."' label='".$array1['track']['@attributes']['lang_original']."' default />";
     }

    echo '</video>';
    }else{
        echo '<img src="'.ROOT_PART.'inc/2.svg" class="w-100" onerror="this.onerror=null; this.src="'.ROOT_PART.'inc/2.gif"">';
        }
}


function Root_part(){
$http=isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$part=rtrim($_SERVER['SCRIPT_NAME'],basename($_SERVER['SCRIPT_NAME']));
$domain=$_SERVER['SERVER_NAME'];
 return "$http"."$domain"."$part";
}
?>
