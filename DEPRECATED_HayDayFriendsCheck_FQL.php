<?php
//WARNING: Hay Day changed from FQL to Facebook Graph API recently, this is no longer how Hay Day checks for friends!!
// this method is outdated
header("content-type: text/plain;charset=utf8");
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


$original_str="https://graph.facebook.com//fql?access_token=LOCAAEvsj2L4g8BAHjodXUhp5qXhrZCyXPcOJOxQbew4lPiNsFXTzNTPKvXZBuYL9eoEyEVTshPRX4BXPA0LYZA5feC7ZBNfJ1OLiJeUVkUZB5KvXQ9WZB42nvDH9aeEibcvOznDWVfwZC03RdCcyZAZAMMOi936gF0ysPZBPz1uCobvFJOlOVDLESsZBRYY0SkAFpaPHAxl0IKIS7YX4HV6MSVpArSmm4ByyTJvZCRmICobPsSUwZDZD&format=json&migration_bundle=fbsdk%3A20131203&q=SELECT%20uid%2Cname%2Cpic_square%2Cfirst_name%20FROM%20user%20WHERE%20uid%20IN%20(SELECT%20uid2%20FROM%20friend%20WHERE%20uid1%20%3D%20me())%20and%20is_app_user%3B&sdk=android";
$res=array();
$url="https://graph.facebook.com//fql";
$data=array();
$data["access_token"]="LOCAAEvsj2L4g8BAHjodXUhp5qXhrZCyXPcOJOxQbew4lPiNsFXTzNTPKvXZBuYL9eoEyEVTshPRX4BXPA0LYZA5feC7ZBNfJ1OLiJeUVkUZB5KvXQ9WZB42nvDH9aeEibcvOznDWVfwZC03RdCcyZAZAMMOi936gF0ysPZBPz1uCobvFJOlOVDLESsZBRYY0SkAFpaPHAxl0IKIS7YX4HV6MSVpArSmm4ByyTJvZCRmICobPsSUwZDZD";
$data["format"]="json";
$data["migration_bundle"]="fbsdk:20131203";
$data["sdk"]="android";
	  $data["q"]="SELECT uid,name,pic_square,first_name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) and is_app_user;";
	  //var_dump(parse_str($original_str,$res),$res);
	  $ch=hhb_curl_init();
$encoded_url=$url.'?'.http_build_query($data);
//var_dump($encoded_url);
print_r(	  json_encode(json_decode(hhb_curl_exec($ch,$encoded_url)),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
