<?php
namespace HiAR;

require_once "HTTPUtils.php";
require_once "HiARSDK.php";
require_once "config.php.sample";


$filename=$_GET['filename'];


HiARSDK::getInstance()->init(APPKEY, SECRET);

HiARSDK::getInstance()->sign_in(ACCOUNT, PASSWORD);


    $path='/upload/enterprise/';
    $name=$path. "" .$filename;
   
    $result= HiARSDK::getInstance()->addImage(5958, __DIR__ . $name, "test1", "test");
    HiARSDK::getInstance()->publishCollection(5958);
    //var_dump($result);
   // echo $result['targetid'];//返回targetid 插入到数据库中
    $url="http://www.herozhou.com:8080/ar/addenterprisetargetid?targetid=".$result;
    $html=file_get_contents($url);
echo $html;

?>