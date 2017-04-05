<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 8:05 PM
 */
namespace liumapp\payment\common\union\data;


use liumapp\payment\common\BaseData;
use liumapp\payment\utils\ArrayUtil;
use liumapp\payment\utils\CertUtil;

abstract class UnionBaseData extends BaseData
{

    /**
     * 设置签名
     * @author helei
     */
    public function setSign()
    {
        $this->buildData();

        $this->retData['certId'] = CertUtil::getSignCertIdFromPfx($this->sdkSignCertPath , $this->sdkSignCertPwd);

        $values = ArrayUtil::removeKeys($this->retData, ['sign']);

        $values = ArrayUtil::arraySort($values);

        $signStr = ArrayUtil::createLinkstring($values);

        $this->retData['signature'] = $this->makeSign($signStr);
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
        switch ($this->signMethod) {
            case '01':
                $sha1 = sha1($signStr, FALSE);
                $privateKey = CertUtil::getSignKeyFromPfx($this->sdkSignCertPath , $this->sdkSignCertPwd);
                $sign_flag = openssl_sign ( $sha1, $sign, $privateKey, OPENSSL_ALGO_SHA1 );
                if ($sign_flag) {
                    $sign = base64_encode ( $sign );
                }
                break;
            default:
                $sign = '';
        }
        return $sign;
    }
}