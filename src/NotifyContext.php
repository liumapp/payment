<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/6/17
 * Time: 10:06 AM
 */
namespace liumapp\payment;


use liumapp\payment\config\Config;
use liumapp\payment\notify\AliNotify;
use liumapp\payment\notify\NotifyStrategy;
use liumapp\payment\notify\PayNotifyInterface;

class NotifyContext
{
    /**
     * 支付的渠道
     * @var NotifyStrategy
     */
    protected $notify;


    /**
     * 设置对应的通知渠道
     * @param string $channel 通知渠道
     *  - @see Config
     *
     * @param array $config 配置文件
     * @throws \ErrorException
     * @author helei
     */
    public function initNotify($channel, array $config)
    {
        try {
            switch ($channel) {
                case Config::ALI_CHARGE:
                    $this->notify = new AliNotify($config);
                    break;
                default:
                    throw new \ErrorException('当前仅支持：ALI_CHARGE WX_CHARGE 两个常量');
            }
        } catch (\ErrorException $e) {
            throw $e;
        }
    }

    /**
     * 返回异步通知的数据
     * @return array|false
     */
    public function getNotifyData()
    {
        return $this->notify->getNotifyData();
    }

    /**
     * 通过环境类调用支付异步通知
     *
     * @param PayNotifyInterface $notify
     * @return array
     * @throws \ErrorException
     * @author helei
     */
    public function notify(PayNotifyInterface $notify)
    {
        if (! $this->notify instanceof NotifyStrategy) {
            throw new \ErrorException('请检查初始化是否正确');
        }

        return $this->notify->handle($notify);
    }

}
