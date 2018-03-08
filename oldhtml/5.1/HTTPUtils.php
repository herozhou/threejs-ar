<?php
/**
 * HTTP处理相关工具包
 *
 * @package bilibili
 * @author MagicBear<magicbearmo@gmail.com>
 * @version 1.0
 */
namespace HiAR;

class HTTPUtils
{
	
	/**
	 * 使用CURL库读取指定地址信息
	 * @param string $url 要读取的URL地址
	 * @param string $header 额外的头信息
	 * @param string &$error 错误
	 * @return string 地址內容 失败时为FALSE
	 */
	public static function get($url, $header, &$error = null)
	{
		$ch	= curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, FALSE);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 300);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11');

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		$response	= curl_exec($ch);
		if ($response === false)
		{
			$error = curl_error($ch);
		}
		@curl_close($ch);
		unset($ch);
		
		return json_decode($response, true);
	}

	/**
	 * 使用CURL库POST到指定地址
	 * @param string $url 要读取的URL地址
	 * @param array $data 要提交的信息
	 * @param string $header 额外的头信息
	 * @param string &$error 错误
	 * @return string 地址內容 失败时为FALSE
	 */
	public static function post($url, $data, $header, &$error = null)
	{
		$data	= http_build_query($data);

		$ch	= curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, TRUE);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 300);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	 
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11');

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);

		$response	= curl_exec($ch);
		if ($response === false)
		{
			$error = curl_error($ch);
		}
		@curl_close($ch);
		unset($ch);
		
		return json_decode($response, true);
	}

	public static function postFile($url, $data, $header, &$error = null)
	{
		//$data	= http_build_query($data);

		$ch	= curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, TRUE);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 300);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11');

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);

		$response	= curl_exec($ch);
		if ($response === false)
		{
			$error = curl_error($ch);
		}
		@curl_close($ch);
		unset($ch);

		return json_decode($response, true);
	}

    public static function delete($url, $data, $header, &$error = null)
    {
        $data	= http_build_query($data);

        $ch	= curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11');

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $response	= curl_exec($ch);
        if ($response === false)
        {
            $error = curl_error($ch);
        }
        @curl_close($ch);
        unset($ch);

        return json_decode($response, true);
    }
}
?>