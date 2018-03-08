<?php
/**
 * Created by PhpStorm.
 * User: lidanyang
 * Date: 16/8/8
 * Time: 下午11:09
 */
namespace HiAR;

require_once "HTTPUtils.php";
require_once "HiARSDK.php";
require_once "config.php.sample";

error_reporting(E_ALL); 

$base64 =$_POST["base64"];
 $base64=str_replace('-', '+', $base64 );
//error_reporting(0);

$base64_image_content =$base64;
//匹配出图片的格式


if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){

$type = $result[2];


$new_file = "enterprisecamera/";

$new_file = $new_file."6.{$type}";

if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
   // echo '新文件保存成功：', $new_file;

          HiARSDK::getInstance()->init(APPKEY, SECRET);

          HiARSDK::getInstance()->sign_in(ACCOUNT, PASSWORD);

               $path='/enterprisecamera/';
              $name=$path. "6.png" ;
           
              $result=  HiARSDK::getInstance()->recognize(__DIR__ . $name, 5958 , 1);
           $result=$result[0];
         
              if($result!=0)
                echo ($result['targetID']);
              else
                 echo ("123");

      

   }else{
  echo '新文件保存失败';
    }
}

?>