<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:11 PM
 */

namespace liumapp\payment\config;

final Class Config
{
    const VERSION = '1.0.0';

    //========================  银联  =========================
    const UNI_CONSUME = 'uni_con'; //银联网关支付


    //========================= ali相关接口 =======================//
    // 支付相关常量

    const ALI_CHANNEL_QR = 'ali_qr';// 支付宝 扫码支付

    // 其他操作常量
    const ALI_CHARGE = 'ali_charge';// 支付

    const ALI_REFUND = 'ali_refund';// 退款

    const ALI_RED = 'ali_red';// 红包

    const ALI_TRANSFER = 'ali_transfer';// 转账


    //========================= 金额问题设置 =======================//
    const PAY_MIN_FEE = '0.01';// 支付的最小金额

    const PAY_MAX_FEE = '100000000.00';// 支付的最大金额

    const TRANS_FEE = '50000';// 转账达到这个金额，需要添加额外信息


    //======================= 交易状态常量定义 ======================//
    const TRADE_STATUS_SUCC = 'success';// 交易成功

    const TRADE_STATUS_FAILD  = 'not_pay';// 交易未完成
}