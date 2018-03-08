<?php
/**
 * Created by PhpStorm.
 * User: lidanyang
 * Date: 16/8/8
 * Time: 下午9:56
 */

namespace HiAR;

class HiARSDK
{
    const DOMAIN = "https://api.hiar.io";

    const PUBLISH_STATUS_NOT_FINISH     = 0;
    const PUBLISH_STATUS_ALL_SUCCESS    = 1;
    const PUBLISH_STATUS_PART_SUCCESS   = 2;

    const TWO_MB        = 2097152;

    private static $instance = null;

    /**
     * @return HiARSDK
     */
    public static function getInstance()
    {
        if(HiARSDK::$instance == null)
        {
            HiARSDK::$instance = new HiARSDK();
        }
        return HiARSDK::$instance;
    }

    public function __construct()
    {

    }

    public $appkey;
    private $secret;

    private $token;
    private $expire;


    /**
     * 设置默认的Appkey和Secret
     * @param $appkey
     * @param $secret
     */
    public function init($appkey, $secret)
    {
        $this->appkey = $appkey;
        $this->secret = $secret;
    }

    /**
     * 登录
     * @param $account
     * @param $password
     * @return string
     */
    public function sign_in($account, $password)
    {
        $url = "/v1/account/signin";

        $param['account'] = $account;
        $param['password'] = $password;

        $rc = HTTPUtils::post(self::DOMAIN . $url, $param, $this->make_header() , $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        $this->token  = $rc['token'];
        $this->expire = time() + $rc['expire'];

        return $rc['info'];
    }

    //************ 应用操作 *****************************//

    public function getAppCollections($appkey = "")
    {
        if(empty($appkey)) $appkey = $this->appkey;
        $url = "/v1/app/{$appkey}/collection/";

        $rc = HTTPUtils::get(self::DOMAIN . $url, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }
        return $rc['collectionIds'];
    }

    //************ 应用操作 end *****************************//


    //************ 图集操作 *****************************//

    /**
     * 创建图集
     * @param string $name          图集名称
     * @param string $description   图集描述
     * @param string $appkey        是否绑定Appkey
     * @return array 返回结果
     */
    public function createCollection($name, $description = "", $appkey = "")
    {
        $url = "/v1/collection/";


        $param['name'] = $name;
        if( !empty($description) )
        {
            $param['desc'] = $description;
        }

        if( !empty($appkey) )
        {
            $param['appkey'] = $appkey;
        }

        $rc = HTTPUtils::post(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }
        unset($rc['retCode']);
        unset($rc['comment']);
        return $rc;
    }

    /**
     * 修改图集名称
     * @param $cid   string   图集ID
     * @param $name  string   新的图集名称
     * @return int      返回结果
     */
    public function changeCollectionName($cid, $name)
    {
        $url = "/v1/collection/{$cid}/name";

        $param['name'] = $name;
        $param['cid'] = $cid;

        $rc = HTTPUtils::post(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['retCode'];
    }

    /**
     * 删除图集
     * @param $cid      string   图集ID
     * @return mixed  返回0代表成功
     */
    public function deleteCollection($cid)
    {
        $url = "/v1/collection/{$cid}";

        $param['cid'] = $cid;

        $rc = HTTPUtils::delete(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['retCode'];
    }


    /**
     * 获取图集信息
     * @param $cid    string   图集ID
     * @return array 成功返回图集信息数组
     */
    public function getCollection($cid)
    {
        $url = "/v1/collection/{$cid}";

        $rc = HTTPUtils::get(self::DOMAIN . $url, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }
        unset($rc['retCode']);
        unset($rc['comment']);
        return $rc;
    }

    /**
     * 获取账户所有图集信息
     * @return array 图集ID的数组
     */
    public function getAccountCollections()
    {
        $url = "/v1/collection/";

        $rc = HTTPUtils::get(self::DOMAIN . $url, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }
        return $rc['collectionIds'];
    }

    /**
     * 发布图集
     * @param $cid      string   图集ID
     * @return mixed   成功返回发布id
     */
    public function publishCollection($cid)
    {
        $url = "/v1/collection/{$cid}/publish";

        $param['cid'] = $cid;

        $rc = HTTPUtils::post(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['pubid'];
    }


    /**
     * 获取发布状态
     * @param $cid      string   图集ID
     * @param $pubid    string   发布ID
     * @return mixed   成功返回int的状态值
     */
    public function getCollectionPublishStatus($cid, $pubid)
    {
        $url = "/v1/collection/{$cid}/publish/{$pubid}";

        $rc = HTTPUtils::get(self::DOMAIN . $url, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['status'];
    }

    /**
     * 关联图集和指定应用
     * @param $cid           string   图集ID
     * @param $appkey        string   指定应用的Appkey
     * @return mixed           成功返回0
     */
    public function bindCollectionToApp($cid, $appkey = "")
    {
        if(empty($appkey)) $appkey = $this->appkey;

        $url = "/v1/collection/{$cid}/app/{$appkey}";

        $param['cid'] = $cid;
        $param['appkey'] = $appkey;

        $rc = HTTPUtils::post(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['retCode'];
    }

    /**
     * 取消关联图集和指定应用
     * @param $cid           string   图集ID
     * @param $appkey        string   指定应用的Appkey
     * @return mixed           成功返回0
     */
    public function unbindCollectionWithApp($cid, $appkey)
    {
        if(empty($appkey)) $appkey = $this->appkey;

        $url = "/v1/collection/{$cid}/app/{$appkey}";

        $param['cid'] = $cid;
        $param['appkey'] = $appkey;

        $rc = HTTPUtils::delete(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['retCode'];
    }


    /**
     * 查询图集关联的所有应用
     * @param $cid  string   图集ID
     * @return array    关联的Appkey数组
     */
    public function getRelationApp($cid)
    {
        $url = "/v1/collection/{$cid}/app/";

        $rc = HTTPUtils::get(self::DOMAIN . $url, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }
        return $rc['appkeys'];
    }

    //************ 图集操作 end *****************************//

    //************ 图片操作 *****************************//

    /**
     * 添加图片
     * @param $cid              string   图集ID
     * @param $image            string   图片路径
     * @param string $name      string   图片名称
     * @param string $meta      string   图片描述信息
     * @return mixed            成功返回图片id
     */
    public function addImage($cid, $image, $name = "", $meta = "")
    {
        if(strlen($meta) > self::TWO_MB)
        {
            return ['error' => "meta larger than 2MB"];
        }

        if( !file_exists($image) || filesize($image) > self::TWO_MB )
        {
            return ['error' => "image larger than 2MB or not exists"];
        }

        $url = "/v1/collection/{$cid}/target";

        $param['cid'] = $cid;
        $param['image'] = new \CURLFile(realpath($image));

        if( !empty($name) )
        {
            $param['name'] = $name;
        }

        if( !empty($meta) )
        {
            $param['meta'] = $meta;
        }

        $rc = HTTPUtils::postFile(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }
        return $rc['targetid'];
    }

    /**
     * 修改图片名称
     * @param $cid              string   图集ID
     * @param $target_id        string   图片ID
     * @param $name             string   新图片名称
     * @return mixed              成功返回0
     */
    public function changeImageName($cid, $target_id, $name)
    {

        $url = "/v1/collection/{$cid}/target/{$target_id}/name";

        $param['cid'] = $cid;
        $param['target_id'] = $target_id;
        $param['name'] = $name;

        $rc = HTTPUtils::post(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['retCode'];
    }

    /**
     * 删除图片
     * @param $cid              string   图集ID
     * @param $target_id        string   图片ID
     * @return mixed            成功返回0
     */
    public function deleteImage($cid, $target_id)
    {
        $url = "/v1/collection/{$cid}/target/{$target_id}";

        $param['cid'] = $cid;
        $param['target_id'] = $target_id;

        $rc = HTTPUtils::delete(self::DOMAIN . $url, $param, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['retCode'];
    }

    /**
     * 获取图片列表
     * @param $cid              string   图集ID
     * @param int $from         起始位置,默认为0
     * @param string $count     获取数目,默认为10
     * @param string $cond      排序条件,默认值"date", 支持创建日期"date",名称"name", 识别度"score"
     * @param string $order     排序顺序,默认"desc", 支持降序"desc", 升序"asc"
     * @return array            图片列表
     */
    public function getImageList($cid, $from = 0, $count = "10", $cond="date", $order="desc" )
    {
        $url = "/v1/collection/{$cid}/target?";

        $param['from'] = $from;
        $param['count'] = $count;
        $param['cond'] = $cond;
        $param['order'] = $order;

        $rc = HTTPUtils::get(self::DOMAIN . $url . http_build_query($param), $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }

        return $rc['targets'];
    }

    /**
     * 获取图片信息
     * @param $cid              string   图集ID
     * @param $target_id        string   图片ID
     * @return array            图片信息
     */
    public function getImageInfo($cid, $target_id)
    {
        $url = "/v1/collection/{$cid}/target/{$target_id}";

        $rc = HTTPUtils::get(self::DOMAIN . $url, $this->make_header(), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            $rc['error'] = $error;
            return $rc;
        }
        unset($rc['retCode']);
        unset($rc['comment']);
        return $rc;
    }

    //************ 图片操作 end *****************************//

    /**
     * 识别图片
     * @param $collectionIds array 识别图集的ID
     * @param $number int 返回识别图片的数量,最大为10,默认为5
     * @param $image string 待识别图片的url(本地)
     * @return array 识别图片结果
     */
    public function recognize($image, $collectionIds, $number = 5)
    {
        $url = "/v1/reco/recognize";

        $data['appKey'] = $this->appkey;
        $data['timestamp'] = strval(time());
        $data['nonce'] = $this->randStr();
        $data['number'] = $number;

      //  $data['collectionIds'] = \implode("," , $collectionIds;);
        $data['collectionIds']=$collectionIds;
        $param['data'] = base64_encode(http_build_query($data));
        $param['image'] = new \CURLFile(realpath($image));

        $rc = HTTPUtils::postFile(self::DOMAIN . $url, $param,
            $this->make_sign($data['timestamp'],$data['nonce'] ), $error);
        if( !$rc || $rc['retCode'] != 0 )
        {
            return 0;
        }

        return $rc['items'];
    }


    private function make_header()
    {
        $header = [];
        if( !empty($this->token) && time() < $this->expire )
        {
            $header[]         = "token: {$this->token}";
        }
        return $header;
    }

    private function make_sign($time, $nonce)
    {
        $param[] = $this->appkey;
        $param[] = $this->secret;
        $param[] = $time;
        $param[] = $nonce;

        sort($param, SORT_STRING);
        $payload = hash_hmac('sha1', \implode("" , $param) , $this->secret, true);
        $sign = base64_encode($payload);
        $header[] = "HiARAuthorization: {$sign}";
        return $header;
    }

    private function randStr($len = 8)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $chars_len = strlen($chars);
        mt_srand((double)microtime() * 1000000 * getmypid());
        $password = "";
        while (strlen($password) < $len) {
            $password .= substr($chars, (mt_rand() % $chars_len), 1);
        }
        return $password;
    }
}