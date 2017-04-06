<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:29 PM
 */

namespace liumapp\payment\charge\ali;

use liumapp\payment\common\ali\AliBaseStrategy;
use liumapp\payment\common\AliConfig;

class AliQrCharge extends AliBaseStrategy
{

    protected function getBuildDataClass()
    {
        $this->config->method = AliConfig::QR_PAY_METHOD;
        return 'liumapp\payment\common\ali\data\charge\QrChargeData';
    }

    /**
     * 处理扫码支付的返回值
     * @param array $ret
     *
     * @throws \ErrorException
     * @return string  可生产二维码的uri
     * @author helei
     */
    protected function retData(array $ret)
    {
        $url = parent::retData($ret);

        // 发起网络请求
        try {
            $data = $this->sendReq($url);
        } catch (\ErrorException $e) {
            throw $e;
        }

        return $data['qr_code'];
    }

}

