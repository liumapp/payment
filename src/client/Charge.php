<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:10 PM
 */

namespace liumapp\payment\client;

use liumapp\payment\config\Config;

class Charge
{
    private static $supportChannel = [

        Config::ALI_CHANNEL_QR, // 支付宝当面付-扫码支付

        Config::UNI_CONSUME,//银联网关支付

    ];

    /**
     * 异步通知类
     * @var ChargeContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        if (is_null(self::$instance)) {
            static::$instance = new ChargeContext();

            try {
                static::$instance->initCharge($channel, $config);
            } catch (\ErrorException $e) {
                throw $e;
            }
        }

        return static::$instance;
    }

    /**
     * @param string $channel
     * @param array $config
     * @param array $metadata
     *
     * @return mixed
     * @throws \Exception
     */
    public static function run($channel, $config, $metadata)
    {
        if (! in_array($channel, self::$supportChannel)) {
            throw new \ErrorException('sdk当前不支持该支付渠道，当前仅支持：' . implode(',', self::$supportChannel));
        }

        try {
            $instance = self::getInstance($channel, $config);

            $ret = $instance->charge($metadata);
        } catch (\ErrorException $e) {
            throw $e;
        }

        return $ret;
    }

}