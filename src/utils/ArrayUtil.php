<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:39 PM
 */
namespace liumapp\payment\utils;

class ArrayUtil
{
    /**
     * 移除空值的key
     * @param $para
     * @return array
     * @author helei
     */
    public static function paraFilter($para)
    {
        $paraFilter = [];
        while (list($key, $val) = each($para)) {
            if ($val == "") {
                continue;
            } else {
                if (! is_array($para[$key])) {
                    $para[$key] = is_bool($para[$key]) ? $para[$key] : trim($para[$key]);
                }

                $paraFilter[$key] = $para[$key];
            }
        }

        return $paraFilter;
    }

    /**
     * 删除一位数组中，指定的key与对应的值
     * @param array $inputs 要操作的数组
     * @param array|string $keys 需要删除的key的数组，或者用（,）链接的字符串
     * @return array
     */
    public static function removeKeys(array $inputs, $keys)
    {
        if (! is_array($keys)) {// 如果不是数组，需要进行转换
            $keys = explode(',', $keys);
        }

        if (empty($keys) || ! is_array($keys)) {
            return $inputs;
        }

        $flag = true;
        foreach ($keys as $key) {
            if (array_key_exists($key, $inputs)) {
                if (is_int($key)) {
                    $flag = false;
                }
                unset($inputs[$key]);
            }
        }

        if (! $flag) {
            $inputs = array_values($inputs);
        }
        return $inputs;
    }

    /**
     * 对输入的数组进行字典排序
     * @param array $param 需要排序的数组
     * @return array
     * @author helei
     */
    public static function arraySort(array $param)
    {
        ksort($param);
        reset($param);

        return $param;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param array $para 需要拼接的数组
     * @return string
     * @throws \Exception
     */
    public static function createLinkstring($para)
    {
        if (! is_array($para)) {
            throw new \Exception('必须传入数组参数');
        }

        reset($para);
        $arg  = "";
        while (list($key, $val) = each($para)) {
            if (is_array($val)) {
                continue;
            }

            $arg.=$key."=".urldecode($val)."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 讲数组转换为string
     *
     * @param $para 数组
     * @param $sort 是否需要排序
     * @param $encode 是否需要URL编码
     * @return string
     */
    function createUnionLinkString($para, $sort, $encode) {
        if($para == NULL || !is_array($para))
            return "";

        $linkString = "";
        if ($sort) {
            $para = argSort ( $para );
        }
        while ( list ( $key, $value ) = each ( $para ) ) {
            if ($encode) {
                $value = urlencode ( $value );
            }
            $linkString .= $key . "=" . $value . "&";
        }
        // 去掉最后一个&字符
        $linkString = substr ( $linkString, 0, count ( $linkString ) - 2 );

        return $linkString;
    }
}
