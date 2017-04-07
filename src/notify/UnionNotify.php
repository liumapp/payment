<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/7/17
 * Time: 11:05 AM
 */

namespace liumapp\payment\notify;

use liumapp\payment\common\UnionConfig;
use liumapp\payment\config\Config;
use liumapp\payment\utils\ArrayUtil;
use liumapp\payment\utils\CertUtil;
use liumapp\payment\utils\Rsa2Encrypt;
use liumapp\payment\utils\RsaEncrypt;

class UnionNotify extends NotifyStrategy
{
    /**
     * AliNotify constructor.
     * @param array $config
     * @throws \ErrorException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        try {
            $this->config = new UnionConfig($config);
        } catch (\ErrorException $e) {
            throw $e;
        }
    }

    /**
     * 获取移除通知的数据  并进行简单处理（如：格式化为数组）
     *
     * 如果获取数据失败，返回false
     *
     * @return array|boolean
     * @author helei
     */
    public function getNotifyData()
    {
        $data = empty($_POST) ? $_GET : $_POST;
        if (empty($data) || ! is_array($data)) {
            return false;
        }

        return $data;
    }

    /**
     * 检查异步通知的数据是否合法
     *
     * 如果检查失败，返回false
     *
     * @param array $data  由 $this->getNotifyData() 返回的数据
     * @return boolean
     * @author helei
     */
    public function checkNotifyData(array $data)
    {
        if (!isset($data['signature'])) {
            return false;
        }
        if ($data ['respMsg'] != 'success') {
            return false;//如果交易没有成功，直接返回错误
        }
        // 公钥
        $public_key = CertUtil::getVerifyCertByCertId ( $data ['certId'] );
//	echo $public_key.'<br/>';
        // 签名串
        $signature_str = $data ['signature'];
        unset ( $data ['signature'] );
        $params_str = $this->createLinkString ( $data, true, false );
        $signature = base64_decode ( $signature_str );
//	echo date('Y-m-d',time());
        $params_sha1x16 = sha1 ( $params_str, FALSE );
        $isSuccess = openssl_verify ( $params_sha1x16, $signature,$public_key, OPENSSL_ALGO_SHA1 );
        if ($isSuccess == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 讲数组转换为string
     *
     * @param $para 数组
     * @param $sort 是否需要排序
     * @param $encode 是否需要URL编码
     * @return string
     */
    protected function createLinkString($para, $sort, $encode) {
        if($para == NULL || !is_array($para))
            return "";

        $linkString = "";
        if ($sort) {
            $para = $this->argSort ( $para );
        }
        while ( list ( $key, $value ) = each ( $para ) ) {
            if ($encode) {
                $value = urlencode ( $value );
            }
            $linkString .= $key . "=" . $value . "&";
        }
        // 去掉最后一个&字符
        $linkString = substr ( $linkString, 0, count ( $linkString ) - 2 );

        return $linkString;
    }

    /**
     * 对数组排序
     *
     * @param $para 排序前的数组
     *        	return 排序后的数组
     */
    protected function argSort($para) {
        ksort ( $para );
        reset ( $para );
        return $para;
    }

    /**
     * 向客户端返回必要的数据
     * @param array $data 回调机构返回的回调通知数据
     * @return array|false
     * @author helei
     */
    protected function getRetData(array $data)
    {
        $retData = $data;
        $retData['channel'] = Config::UNION_CHARGE;

        return $retData;
    }

    /**
     * 支付宝，成功返回 ‘success’   失败，返回 ‘fail’
     * @param boolean $flag 每次返回的bool值
     * @param string $msg 错误原因  后期考虑记录日志
     * @return string
     * @author helei
     */
    protected function replyNotify($flag, $msg = '')
    {
        if ($flag) {
            return '验签成功';
        } else {
            return '验签失败';
        }
    }
}