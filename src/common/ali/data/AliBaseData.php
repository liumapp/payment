<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 8:00 PM
 */

namespace liumapp\payment\common\ali\data;


use liumapp\payment\common\BaseData;
use liumapp\payment\utils\ArrayUtil;
use liumapp\payment\utils\Rsa2Encrypt;
use liumapp\payment\utils\RsaEncrypt;

abstract class AliBaseData extends BaseData
{
    public function getData()
    {
        $data = parent::getData();

        // 新版需要对数据进行排序
        $data = ArrayUtil::arraySort($data);
        return $data;
    }

    /**
     * 签名算法实现
     * @param string $signStr
     * @return string
     * @author helei
     */
    protected function makeSign($signStr)
    {
        $sign = '';
        switch ($this->signType) {
            case 'RSA':
                $rsa = new RsaEncrypt($this->rsaPrivateKey);

                $sign = $rsa->encrypt($signStr);
                break;
            case 'RSA2':
                $rsa = new Rsa2Encrypt($this->rsaPrivateKey);

                $sign = $rsa->encrypt($signStr);
                break;
            default:
                $sign = '';
        }

        return $sign;
    }
}