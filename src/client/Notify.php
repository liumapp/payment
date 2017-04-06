<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/6/17
 * Time: 10:16 AM
 */
namespace liumapp\payment\client;

use liumapp\payment\config\Config;
use liumapp\payment\notify\PayNotifyInterface;
use liumapp\payment\NotifyContext;

class Notify
{
    private static $supportChannel = [
        Config::ALI_CHARGE,// 支付宝

        Config::UNI_CONSUME,//银联
    ];

    /**
     * 异步通知类
     * @var NotifyContext
     */
    protected static $instance;

    protected static function getInstance($type, $config)
    {
        if (is_null(self::$instance)) {
            static::$instance = new NotifyContext();

            try {
                static::$instance->initNotify($type, $config);
            } catch (\ErrorException $e) {
                throw $e;
            }
        }

        return static::$instance;
    }

    /**
     * 执行异步工作
     * @param string $type
     * @param array $config
     * @param PayNotifyInterface $callback
     * @return array
     * @throws \ErrorException
     */
    public static function run($type, $config, $callback)
    {
        if (! in_array($type, self::$supportChannel)) {
            throw new \ErrorException('sdk当前不支持该异步方式，当前仅支持：' . implode(',', self::$supportChannel));
        }
        try {
            $instance = self::getInstance($type, $config);

            $ret = $instance->notify($callback);
        } catch (\ErrorException $e) {
            throw $e;
        }

        return $ret;
    }

    /**
     * 返回异步通知的结果
     * @param $type
     * @param $config
     * @return array|false
     * @throws \ErrorException
     */
    public static function getNotifyData($type, $config)
    {
        try {
            $instance = self::getInstance($type, $config);

            return $instance->getNotifyData();
        } catch (\ErrorException $e) {
            throw $e;
        }
    }
}