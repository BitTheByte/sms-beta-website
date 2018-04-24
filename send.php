<?php
error_reporting(0);

//=================KEY==================//
$api_keys = ["https://[HASH]@api.blower.io"];
$gsecret = "";
//=================KEY==================//



//=================POST==================//
$num = $_POST['num'];
$text = $_POST['msg'];
$sender = $_POST['sender'];
$captcha_token = $_POST['token'];
//=================POST==================//


$text = str_replace(".","&Break&",$text);
$text = str_replace("facebook","fac****k",$text);
$text = str_replace("confirmation code","******",$text);


$valid_key = "";
$is_banned = "";
function getRealIpAddr(){
    if (!empty($_SERVER['HTTP_CLIENT_IP']))  
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function check_api($api_key){
    $api_key = $api_key."/messages";
    $payload = array('to' => "", 'message' => "test_api");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$api_key);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $resp = curl_exec ($ch);
    if($resp == "Not authorized"){
        return "false";
    }
    else{
        return "true";
    }
}

function check_captcha($token_){
    $URL = "https://www.google.com/recaptcha/api/siteverify";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=$gsecret&response=".$token_);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    $c = curl_exec ($ch);
    $c = str_replace('false','"false"',$c);
    $c = str_replace('true','"true"',$c);
    $c = json_decode($c,true);
    return $c['success'];
    curl_close ($ch);
}

function send_msg($num,$text,$sender,$key,$captcha_token){
    $key = $key."/messages";
    $payload = array('to' => $num, 'message' => $text);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$key);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);                       //
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $banned_list = file_get_contents('banned.txt');
    $json = json_decode($banned_list,true);
    foreach ($json['users'] as $value) {
      if($value == $sender){
          $is_banned = "true";
      }
    }
    if($is_banned == "true")
    {
        echo('{"token":"true","send":"false","banned":"true"}');
    }else{
        if(check_captcha($captcha_token) == "true"){
            $resp = curl_exec ($ch);
            if(json_decode($resp,true)['message'] == "ok"){
                 echo('{"send":"true","token":"true","banned":"false"}');
                 log_msg($num,$text,$sender);
            }else{
                 echo('{"send":"false","token":"true","banned":"false"}');
            }
            
        }else{
             echo('{"send":"false","token":"false","banned":"false"}');
        }
    }
  
}
function log_msg($number,$msg,$name){
    $ip = getRealIpAddr();
    file_put_contents('log.htm',"<br>-- MESSAGE START -- <br>By: $name <br>To Number: $number <br>ip: $ip <br>Message: $msg<br>-- MESSAGE END --<br>", FILE_APPEND);
}

if(isset($num) && isset($text)){
    preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $text, $match);
    if(isset($match[0][0])){
        echo "url founded";
        exit;
    }
}

foreach($api_keys as $key => $value){
    if(check_api($value) != "false"){
        $valid_key = $value;
    }
}

if($valid_key != ""){
    send_msg($num,$text,$sender,$valid_key,$captcha_token);
}else{
    echo "NO DATA FOUND";
}


?>