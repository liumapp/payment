<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 8:17 PM
 */

namespace liumapp\payment\charge\union;

use liumapp\payment\common\union\UnionBaseStrategy;

class UnionConsumeCharge extends UnionBaseStrategy
{
    protected function getBuildDataClass ()
    {
        $this->config->txnType = '01';
        $this->config->txnSubType = '01';
        return 'liumapp\payment\common\union\data\charge\UnionConsumeData';
    }
}