<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:35 PM
 */

namespace liumapp\payment\common;

abstract class ConfigInterface
{
    // 是否返回原始数据
    public $returnRaw = false;

    // 禁止使用的支付渠道
    public $limitPay;

    // 用于异步通知的地址
    public $notifyUrl;

    // 加密方式
    // 支付宝：默认使用RSA   目前支持RSA2和RSA
    // 微信： 默认使用MD5
    public $signType = 'RSA';

    public function toArray()
    {
        return get_object_vars($this);
    }
}