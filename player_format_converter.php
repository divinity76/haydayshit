<?php
header("Content-Type: text/plain; charset=utf-8");
error_reporting(E_ALL);
set_time_limit(0);
set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

$fql=file_get_contents("DEPRECATED_HayDayPlayersResponse.json");
print_r(convert_fql_format_to_graphapi_format($fql));


function convert_fql_format_to_graphapi_format($fql){
$graphapi="";
$old_players=json_decode($fql,true,15000,JSON_BIGINT_AS_STRING )['data'];
$new_players=array();
$curlh=hhb_curl_init(array(        CURLOPT_CONNECTTIMEOUT => 100,
        CURLOPT_TIMEOUT => 110,//when torrenting, 10 seconds is not enough sometimes :o
));
foreach($old_players as $player){
$newplayer=array();
//$newplayer["id"]=$player["uid"];

//$foo=stream_context_create(array('http' => array('header'=>'Connection: close')));
////< its a speed thing, as file_get_contents don't give a fuck about content-length, and will keep stalling until the connection is actually closed... i think..
//// using curl or socket_create should be even faster ;)
try{
while(true){
$geturl="http://graph.facebook.com/".$player["uid"]."/?fields=id,name,first_name,gender,locale,username,picture";
$newplayer=json_decode(hhb_curl_exec($curlh,$geturl),true);
if(array_key_exists("error",$newplayer)){
if($newplayer["error"]["code"]!=4){
echo "WARNING: UNKNOWN FACEBOOK ERROR!!! IGNORING ".$player["name"]." (uid ".$player["uid"]." )... url: ".$geturl.PHP_EOL;
var_dump($newplayer);
break;
}
//fuck, we've been rate-limited... sleeping 5 seconds and trying again
echo ",";
sleep(5);
continue;
} else {
break;
}
}
}catch(ErrorException $ex){
echo "Warning: could not get info on user ".$player["name"]." (uid ".$player["uid"]." )...".$ex->getMessage()."... ignoring!".PHP_EOL;
continue;

}
$newplayer["installed"]=true;


array_push($new_players,$newplayer);
echo ".";
sleep(1);//1 second should be sufficient to not get banned
//http://graph.facebook.com/1406559129/?fields=id,name,first_name,gender,locale,username,picture
/*
old:
        {
            "uid": 1406559129,
            "name": "Kevin Furuseth",
            "pic_square": "https:\/\/fbcdn-profile-a.akamaihd.net\/hprofile-ak-xpa1\/t1.0-1\/c247.37.466.466\/s50x50\/5601_10201273892115118_1964412979_n.jpg",
            "first_name": "Kevin"
        }
?fields=id,name,first_name,gender,locale,username,picture
new:
	{
        "id": "1406559129",
        "name": "Kevin Furuseth",
        "first_name": "Kevin",
        "gender": "male",
        "locale": "en_US",
        "username": "kevfuru",
        "installed": true,
        "picture": {
            "data": {
                "is_silhouette": false,
                "url": "https:\/\/fbcdn-profile-a.akamaihd.net\/hprofile-ak-xpa1\/v\/t1.0-1\/c247.37.466.466\/s50x50\/5601_10201273892115118_1964412979_n.jpg?oh=d4ef59597e6e5a5abc5ac434f8ab4457&oe=54BB219C&__gda__=1421714249_46e9a72bd90e41eef06072452b40d1c5"
            }
        }
    }

*/
}
echo PHP_EOL,PHP_EOL;
$graphapi=array();
$graphapi["data"]=$new_players;

$graphapi["paging"]=array();
$graphapi["paging"]["next"]="https://graph.facebook.com/v1.0/100000605585019/friends?fields=id,name,picture,first_name,gender,locale,username,installed&format=json&access_token=CAAEvsj2L4g8BAGXkKZAP0e04VjcPX99WachuToyskWSGniz3IgW48ZAB6rfn7VWuZBrZBFIALgMdfs5s5Ep0o1OLPJtoUXZCbdfWP4GEE9HzIEdMFuONb43Asyv6ZBxoYKgShZCU9b3Fgvqg9S8O0BRZAmozFpkJqBWfIVVNhyAi0LVs2ZAPIN8pYYIwoofI8GgZCUSnCqeskx0CXZCsIio44ifGZCyEZA577HxEaHZBxfbXpNZCoqg4ZBKz6clUrEqWWZB1OfJAjeSkLwluTawZDZD&limit=5000&offset=5000&__after_id=enc_AeznUiGDiOhWPHO1IruPYTKDa9Z90LC-5wpL_mqPnT5ozgrpak5AJ4p4JRNB9rroXBY5R921zyEqcbD0-DtcmC_m"
//not sure what that's supposed to mean..TODO.
$graphapi=json_encode($graphapi,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
return $graphapi;
}













function hhb_curl_init($custom_options_array = array()) {
    if(empty($custom_options_array)){
        $custom_options_array=array();
//i feel kinda bad about this.. argv[1] of curl_init wants a string(url), or NULL
//at least i want to allow NULL aswell :/
    }
    if (!is_array($custom_options_array)) {
        throw new InvalidArgumentException('$custom_options_array must be an array!');
    };
    $options_array = array(
        CURLOPT_AUTOREFERER => true,
        CURLOPT_BINARYTRANSFER => true,
        CURLOPT_COOKIESESSION => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_FORBID_REUSE => false,
        CURLOPT_HTTPGET => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 11,
        CURLOPT_ENCODING=>"",
        //CURLOPT_REFERER=>'example.org',
        //CURLOPT_USERAGE=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36'
    );
    if (!array_key_exists(CURLOPT_COOKIEFILE, $custom_options_array)) {
    	//do this only conditionally because tmpfile() call..
    	 static $curl_cookiefiles_arr=array();//workaround for https://bugs.php.net/bug.php?id=66014
	 $curl_cookiefiles_arr[]=$options_array[CURLOPT_COOKIEFILE] = tmpfile();
	 $options_array[CURLOPT_COOKIEFILE] =stream_get_meta_data($options_array[CURLOPT_COOKIEFILE]);
	 $options_array[CURLOPT_COOKIEFILE]=$options_array[CURLOPT_COOKIEFILE]['uri']; 

    }
    //we can't use array_merge() because of how it handles integer-keys, it would/could cause corruption
    foreach($custom_options_array as $key => $val) {
        $options_array[$key] = $val;
    }
    unset($key, $val, $custom_options_array);
    $curl = curl_init();
    curl_setopt_array($curl, $options_array);
    return $curl;
}
$hhb_curl_domainCache = "";

function hhb_curl_exec($ch, $url) {
    global $hhb_curl_domainCache; //
    //$hhb_curl_domainCache=&$this->hhb_curl_domainCache;
    //$ch=&$this->curlh;
    	if(!is_resource($ch) || get_resource_type($ch)!=='curl')
	{
		throw new InvalidArgumentException('$ch must be a curl handle!');
	}
	if(!is_string($url))
	{
		throw new InvalidArgumentException('$url must be a string!');
	}

    $tmpvar = "";
    if (parse_url($url, PHP_URL_HOST) === null) {
        if (substr($url, 0, 1) !== '/') {
            $url = $hhb_curl_domainCache.'/'.$url;
        } else {
            $url = $hhb_curl_domainCache.$url;
        }
    };

    curl_setopt($ch, CURLOPT_URL, $url);
    $html = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Curl error (curl_errno='.curl_errno($ch).') on url '.var_export($url, true).': '.curl_error($ch));
        // echo 'Curl error: ' . curl_error($ch);
    }
    if ($html === '' && 203 != ($tmpvar = curl_getinfo($ch, CURLINFO_HTTP_CODE)) /*203 is "success, but no output"..*/ ) {
        throw new Exception('Curl returned nothing for '.var_export($url, true).' but HTTP_RESPONSE_CODE was '.var_export($tmpvar, true));
    };
    //remember that curl (usually) auto-follows the "Location: " http redirects..
    $hhb_curl_domainCache = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL), PHP_URL_HOST);
    return $html;
}
