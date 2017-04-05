<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 8:08 PM
 */

namespace liumapp\payment\common\union\data\charge;


class UnionConsumeData extends ChargeBaseData
{
    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @return string
     */
    protected function getConsumeContent()
    {
        $content = [

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

            'merId' => $this->merId,		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $this->orderId,	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => $this->txnTime,	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $this->txnAmt,	//交易金额，单位分，此处默认取demo演示页面传递的参数
        ];

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}