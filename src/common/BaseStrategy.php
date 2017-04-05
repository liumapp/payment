<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:45 PM
 */
namespace liumapp\payment\common;

interface BaseStrategy
{
    /**
     * 处理具体的业务
     * @param array $data
     * @return mixed
     */
    public function handle(array $data);
}