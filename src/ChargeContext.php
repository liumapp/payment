<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:20 PM
 */

namespace liumapp\payment;

use liumapp\payment\config\Config;

class ChargeContext
{
    /**
     * 支付的渠道
     * @var BaseStrategy
     */
    protected $channel;

    /**
     * 设置对应的支付渠道
     * @param string $channel 支付渠道
     *  - @see Config
     * @param array $config 配置文件
     * @throws ErrorException
     */
    public function initCharge($channel, array $config)
    {
        // 初始化时，可能抛出异常，再次统一再抛出给客户端进行处理
        try {
            switch ($channel) {
                case Config::ALI_CHANNEL_QR:
                    $this->channel = new AliQrCharge($config);
                    break;
                case Config::UNI_CONSUME:
                    $this->channel = new UnionConsumeCharge($config);
                    break;
                default:
                    throw new \ErrorException('当前仅支持：支付宝 与 微信');
            }
        } catch (\ErrorException $e) {
            throw $e;
        }
    }

    /**
     * 通过环境类调用支付
     * @param array $data
     *
     * ```php
     * $payData = [
     *      "order_no" => createPayid(),
     *      "amount" => '0.01',// 单位为元 ,最小为0.01
     *      "client_ip" => '127.0.0.1',
     *      "subject" => '测试支付',
     *      "body" => '支付接口测试',
     *      "extra_param"   => '',
     * ];
     * ```
     *
     * @return array
     * @throws ErrorException
     */
    public function charge(array $data)
    {
        if (! $this->channel instanceof BaseStrategy) {
            throw new \ErrorException('请检查初始化是否正确');
        }

        try {
            return $this->channel->handle($data);
        } catch (\ErrorException $e) {
            throw $e;
        }
    }
}