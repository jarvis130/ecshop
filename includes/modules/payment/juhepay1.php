<?php

/**
 * ECSHOP 聚合支付插件
 * ============================================================================
 * * 版权所有 2005-2018 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: douqinghua $
 * $Id: alipay.php 17217 2011-01-19 06:29:08Z douqinghua $
 */
require(ROOT_PATH . 'includes/safety.php');
include_once(ROOT_PATH . 'includes/cls_utils.php');

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/juhepay1.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'juhepay1_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'VIDEO';

    /* 网址 */
    $modules[$i]['website'] = '';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'juhepay_partner',           'type' => 'text',   'value' => ''),
        array('name' => 'juhepay_key',               'type' => 'text',   'value' => ''),
        array('name' => 'juhepay_pay_method',        'type' => 'text',   'value' => ''),
        array('name' => 'juhepay_alipay_quota',      'type' => 'text',   'value' => ''),
        array('name' => 'juhepay_wxpay_quota',       'type' => 'text',   'value' => ''),
        array('name' => 'juhepay_kjpay_quota',       'type' => 'text',   'value' => '')
    );

    return;
}

/**
 * 类
 */
class juhepay
{
    function __construct()
    {
        $this->juhepay();
    }

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function juhepay()
    {
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {

    }

    /**
     * 响应操作
     */
    function respond()
    {

    }
}

?>
