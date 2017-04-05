<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 8:07 PM
 */
namespace liumapp\payment\common\union\data\charge;


use liumapp\payment\common\union\data\UnionBaseData;
use liumapp\payment\config\Config;
use liumapp\payment\utils\ArrayUtil;

abstract class ChargeBaseData extends UnionBaseData
{
    /**
     * 构建 加密数据
     * @author helei
     */
    protected function buildData()
    {
        $signData = [

            //以下信息非特殊情况不需要改动
            'version' => $this->version,                 //版本号
            'encoding' => $this->encoding,				  //编码方式
            'txnType' => $this->txnType,				      //交易类型
            'txnSubType' => $this->txnSubType,				  //交易子类
            'bizType' => $this->bizType,				  //业务类型
            'frontUrl' =>  $this->frontUrl,  //前台通知地址
            'backUrl' => $this->backUrl,	  //后台通知地址
            'signMethod' => $this->signMethod,	              //签名方法
            'channelType' => $this->channelType,	              //渠道类型，07-PC，08-手机
            'accessType' => $this->accessType,		          //接入类型
            'currencyCode' => $this->currencyCode,	          //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $this->merId,		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $this->orderId,	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => $this->txnTime,	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $this->txnAmt,	//交易金额，单位分，此处默认取demo演示页面传递的参数
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 网关支付构建请求支付的数据
     * @return mixed
     */
    abstract protected function getConsumeContent();


    /**
     * 检查传入的支付业务参数是否正确
     *
     * 如果输入参数不符合规范，直接抛出异常
     *
     */
    protected function checkDataParam()
    {
        $orderId = $this->orderId;
        $txnAmt = $this->txnAmt;

        // 检查订单号是否合法
        if (empty($orderId) || mb_strlen($orderId) > 64) {
            throw new \ErrorException('订单号不能为空，并且长度不能超过64位');
        }

        // 检查金额不能低于0.01，不能大于 100000000.00
        if (bccomp($txnAmt, Config::PAY_MIN_FEE, 2) === -1) {
            throw new \ErrorException('支付金额不能低于 ' . Config::PAY_MIN_FEE . ' 元');
        }
        if (bccomp($txnAmt, Config::PAY_MAX_FEE, 2) === 1) {
            throw new \ErrorException('支付金额不能大于 ' . Config::PAY_MAX_FEE . ' 元');
        }
    }
}