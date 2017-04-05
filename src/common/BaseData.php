<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:45 PM
 */
namespace liumapp\payment\common;

use liumapp\payment\utils\ArrayUtil;

abstract class BaseData
{

    /**
     * 支付的请求数据
     * @var array $data
     */
    protected $data;

    /**
     * 支付返回的数据
     * @var array $retData
     */
    protected $retData;

    /**
     * BaseData constructor.
     * @param ConfigInterface $config
     * @param array $reqData
     * @throws \ErrorException
     */
    public function __construct(ConfigInterface $config, array $reqData)
    {
        $this->data = array_merge($config->toArray(), $reqData);

        try {
            $this->checkDataParam();
        } catch (\ErrorException $e) {
            throw $e;
        }
    }

    /**
     * 获取变量，通过魔术方法
     * @param string $name
     * @return null|string
     * @author helei
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * 设置变量
     * @param $name
     * @param $value
     * @author helei
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 设置签名
     * @author helei
     */
    public function setSign()
    {
        $this->buildData();

        $values = ArrayUtil::removeKeys($this->retData, ['sign']);

        $values = ArrayUtil::arraySort($values);

        $signStr = ArrayUtil::createLinkstring($values);

        $this->retData['sign'] = $this->makeSign($signStr);
    }

    /**
     * 返回处理之后的数据
     * @return array
     * @author helei
     */
    public function getData()
    {
        return $this->retData;
    }

    /**
     * 签名算法实现  便于后期扩展微信不同的加密方式
     * @param string $signStr
     * @return string
     */
    abstract protected function makeSign($signStr);

    /**
     * 构建用于支付的签名相关数据
     * @return array
     */
    abstract protected function buildData();

    /**
     * 检查传入的参数. $reqData是否正确.
     * @return mixed
     * @throws \ErrorException
     */
    abstract protected function checkDataParam();
}
