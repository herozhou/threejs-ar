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


$new_file = "camera/";

$new_file = $new_file."6.{$type}";

if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
   // echo '新文件保存成功：', $new_file;


          $option='recoginze';

          //$info;$cid=5958;$target_id;


          HiARSDK::getInstance()->init(APPKEY, SECRET);

          HiARSDK::getInstance()->sign_in(ACCOUNT, PASSWORD);


          //第一步:已用户名为名字创建一个图集，把返回的id存在数据库里
          if ($option=='creatpictures')
          {
              global $info ;
              $info= HiARSDK::getInstance()->createCollection("testCol");
              echo $info['cid'];
              //$info['cid'] 为图集ID
          }
          else if($option=='creatpictures')//在图集中添加识别图片  将识别图和模型路径关联起来
          {
              $filename=$_GET['filename'];

              $path='/upload/';
              $name=$path. "" .$filename;
              global $target_id ;
              $result= HiARSDK::getInstance()->addImage($info['cid'], __DIR__ . $name, "test1", "test");
              HiARSDK::getInstance()->publishCollection($info['cid']);
              echo $result['targetid'];//返回targetid 插入到数据库中
          }
          else  if($option=='recoginze')//识别出来，返回对应的名字，然后搜索数据库获得模型路径
          {
              //$filename=$_GET['filename'];

             // $name='123.jpg';
               $path='/camera/';
              $name=$path. "6.png" ;
              //echo __DIR__ .$name;
           //   $info['cid']="5958";
           // $result=  HiARSDK::getInstance()->recognize(__DIR__ . $name, $info['cid'] , 5);
              $result=  HiARSDK::getInstance()->recognize(__DIR__ . $name, 5958 , 1);
           $result=$result[0];
            // echo $result['targetID'];
           // var_dump($result); 
           // echo gettype($result['retCode']);
              if($result!=0)
                echo ($result['targetID']);
              else
                 echo ("123");

          }


   }else{
  echo '新文件保存失败';
    }
}

//$option=$_GET['option'];
//$option=$_GET['option'];



//var_dump(HiARSDK::getInstance()->addImage(5958, __DIR__ . "/image.png","test","test1"));
//$pub_id = HiARSDK::getInstance()->publishCollection($info['cid']);
//$collectons = HiARSDK::getInstance()->getAccountCollections();

//var_dump($collectons);

//var_dump(HiARSDK::getInstance()->recognize(__DIR__ . "/123.jpg", $collectons , 5));
?>