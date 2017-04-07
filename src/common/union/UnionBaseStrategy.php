<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:48 PM
 */
namespace liumapp\payment\common\union;

use liumapp\payment\common\BaseData;
use liumapp\payment\common\BaseStrategy;
use liumapp\payment\common\UnionConfig;

abstract class UnionBaseStrategy implements BaseStrategy
{
    /**
     * @var UnionConfig
     */
    protected $config;

    /**
     * @var BaseData
     */
    protected $reqData;

    abstract protected function getBuildDataClass ();

    public function __construct(array $config)
    {
        mb_internal_encoding("UTF-8");
        try {
            $this->config = new UnionConfig($config);
        } catch (\ErrorException $e) {
            throw $e;
        }
    }

    public function handle(array $data)
    {

        $buildClass = $this->getBuildDataClass();
        try {
            $this->reqData = new $buildClass($this->config, $data);
        } catch (\ErrorException $e) {
            throw $e;
        }
        $this->reqData->setSign();

        $data = $this->reqData->getData();

        return $this->retData($data);
    }

    /**
     * 处理银联的data并返回页面
     * @param array $data
     * @return string|array
     * @author helei
     */
    protected function retData(array $data)
    {
        // <body onload="javascript:document.pay_form.submit();">
        $encodeType = isset ( $data ['encoding'] ) ? $data ['encoding'] : 'UTF-8';
        $reqUrl = UnionConfig::SDK_FRONT_TRANS_URL;
        $html = <<<eot
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={$encodeType}" />
</head>
<body onload="javascript:document.pay_form.submit();">
    <form id="pay_form" name="pay_form" action="{$reqUrl}" method="post">
	
eot;
        foreach ( $data as $key => $value ) {
            $html .= "    <input type=\"hidden\" name=\"{$key}\" id=\"{$key}\" value=\"{$value}\" />\n";
        }
        $html .= <<<eot
   <!-- <input type="submit" type="hidden">-->
    </form>
</body>
</html>
eot;
        return $html;
    }

}



