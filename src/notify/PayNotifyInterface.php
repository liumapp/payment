<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/6/17
 * Time: 9:54 AM
 */

namespace liumapp\payment\notify;

interface PayNotifyInterface
{
    /**
     * 异步回调检验完成后，回调客户端的业务逻辑
     *  业务逻辑处理，必须实现该类。
     *
     * @param array $data 返回的数据
     *
     * @return boolean
     * @author helei
     */
    public function notifyProcess(array $data);
}
