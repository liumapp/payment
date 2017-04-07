<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:36 PM
 */

namespace liumapp\payment\common;

use liumapp\payment\utils\ArrayUtil;

class UnionConfig extends ConfigInterface
{
    public $version = '5.0.0'; //版本号

    public $encoding = 'utf-8';

    public $txnType = '02';//交易类型

    public $txnSubType = '01';//交易子类

    public $bizType = '000201'; //业务类型

    public $frontUrl = self::SDK_FRONT_NOTIFY_URL;

    public $backUrl = self::SDK_BACK_NOTIFY_URL;

    public $signMethod = '01';//签名方法

    public $channelType = '08'; //渠道类型，07-pc，08-手机

    public $accessType = '0'; //接入类型

    public $currencyCode = '156'; //交易币种

    public $merId; //商户代码

// 签名证书路径
    public $sdkSignCertPath;

// 签名证书密码
    public $sdkSignCertPwd;

// 密码加密证书（这条一般用不到的请随便配）
    public $sdkEncryptCertPath;

// 验签证书路径（请配到文件夹，不要配到具体文件）
    public $sdkVerifyCertDir;

// 前台请求地址
    const SDK_FRONT_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/frontTransReq.do';

// 后台请求地址
    const SDK_BACK_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/backTransReq.do';

// 批量交易
    const SDK_BATCH_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/batchTrans.do';

//单笔查询请求地址
    const SDK_SINGLE_QUERY_URL = 'https://101.231.204.80:5000/gateway/api/queryTrans.do';

//文件传输请求地址
    const SDK_FILE_QUERY_URL = 'https://101.231.204.80:9080/';

//有卡交易地址
    const SDK_Card_Request_Url = 'https://101.231.204.80:5000/gateway/api/cardTransReq.do';

//App交易地址
    const SDK_App_Request_Url = 'https://101.231.204.80:5000/gateway/api/appTransReq.do';

// 前台通知地址 (商户自行配置通知地址)
    const SDK_FRONT_NOTIFY_URL = 'http://localhost:8085/upacp_demo_b2c/demo/api_01_gateway/FrontReceive.php';

// 后台通知地址 (商户自行配置通知地址，需配置外网能访问的地址)
    const SDK_BACK_NOTIFY_URL = 'http://222.222.222.222/upacp_demo_b2c/demo/api_01_gateway/BackReceive.php';

//文件下载目录
//    const SDK_FILE_DOWN_PATH = __DIR__ . '/../data/';

    /** 以下缴费产品使用，其余产品用不到，无视即可 */
// 前台请求地址
    const JF_SDK_FRONT_TRANS_URL = 'https://101.231.204.80:5000/jiaofei/api/frontTransReq.do';
// 后台请求地址
    const JF_SDK_BACK_TRANS_URL = 'https://101.231.204.80:5000/jiaofei/api/backTransReq.do';
// 单笔查询请求地址
    const JF_SDK_SINGLE_QUERY_URL = 'https://101.231.204.80:5000/jiaofei/api/queryTrans.do';
// 有卡交易地址
    const JF_SDK_CARD_TRANS_URL = 'https://101.231.204.80:5000/jiaofei/api/cardTransReq.do';
// App交易地址
    const JF_SDK_APP_TRANS_URL = 'https://101.231.204.80:5000/jiaofei/api/appTransReq.do';

    public function __construct(array $config)
    {

        try {

            $this->initConfig($config);

        } catch (\ErrorException $e) {

            throw $e;

        }
    }

    private function initConfig (array $config)
    {
        $config = ArrayUtil::paraFilter($config);
        if (isset($config['merId'])) {
            $this->merId = $config['merId'];
        } else {
            throw new \ErrorException('商户号不能为空');
        }
        if (isset($config['sdk_sign_cert_path'])) {
            $this->sdkSignCertPath = $config['sdk_sign_cert_path'];
        } else {
            throw new \ErrorException('商户私钥没找到');
        }
        if (isset($config['sdk_sign_cert_pwd'])) {
            $this->sdkSignCertPwd = $config['sdk_sign_cert_pwd'];
        } else {
            throw new \ErrorException('商户私钥密码未设置');
        }
        if (isset($config['sdk_encrypt_cert_path'])) {
            $this->sdkEncryptCertPath = $config['sdk_encrypt_cert_path'];
        } else {
            throw new \ErrorException('银联公钥证书未找到');
        }
        if (isset($config['sdk_verify_cert_dir'])) {
            $this->sdkVerifyCertDir = $config['sdk_verify_cert_dir'];
        } else {
            throw new \ErrorException('证书目录未设置');
        }
        if (isset($config['frontUrl'])) {
            $this->frontUrl = $config['frontUrl'];
        } else {
            throw new \ErrorException('前台返回地址未设置');
        }
        if (isset($config['backUrl'])) {
            $this->backUrl = $config['backUrl'];
        } else {
            throw new \ErrorException('后台通知地址未设置');
        }


    }
}