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
$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/juhepay.php';

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
    $modules[$i]['desc']    = 'juhepay_desc';

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
//        if (!defined('EC_CHARSET'))
//        {
//            $charset = 'utf-8';
//        }
//        else
//        {
//            $charset = EC_CHARSET;
//        }
//
//        $real_method = $payment['juhepay_pay_method'];
//
//        switch ($real_method){
//            case '0':
//                $service = 'pay.alipay.wappay';
//                break;
//            case '1':
//                $service = 'pay.wxpay.sm';
//                break;
//            case '2':
//                $service = 'pay.kj.web';
//                break;
//        }
//
//        $pay_url = 'http://47.90.50.227/smartpayment/pay/gateway';
//        $utils = new Utils();
//
//        $parameter = array(
//            'service'           => $service,
//            'version'           => '1.0',
//            'charset'           => 'UTF-8',
//            'sign_type'         => 'MD5',
//            'merchant_id'       => $payment['juhepay_partner'],
//            'nonce_str'         => $utils->rand_str(32),
//            'notify_url'        => return_url(basename(__FILE__, '.php')),
//            'client_ip'         => $_SERVER['REMOTE_ADDR'], // 终端ip
//            /* 业务参数 */
//            'goods_desc'        => $order['order_sn'],
//            'out_trade_no'      => $order['order_sn'] . '_' . $order['log_id'],
//            'total_amount'      => $order['order_amount'],
//        );
//
//        ksort($parameter);
//        reset($parameter);
//
//        $param = '';
//        $sign  = '';
//
//        foreach ($parameter AS $key => $val)
//        {
//            $param .= "$key=" .urlencode($val). "&";
//            $sign  .= "$key=$val&";
//        }
//
//        $param = substr($param, 0, -1);
//        $sign  = substr($sign, 0, -1). $payment['juhepay_key'];
//
//        $button = "<input type='submit' value='" . $GLOBALS['_LANG']['pay_button'] . "' />";
//        $html = $this->create_html($param,$pay_url,$button);
//        return $html;
    }

    function create_html($params,$pay_url,$button){
        $html = <<<eot
    <br />
    <form style="text-align:center;" id="pay_form" name="pay_form" action="{$pay_url}" method="post" target="_blank">
eot;

        foreach ($params as $k => $v) {
            $html .=  "<input type='hidden' name = '" . $k . "' value ='" . $v . "'/>";
        }

        $html .= $button . "</form><br />";
        return $html;
    }

    /**
     * 响应操作
     */
    function respond()
    {
//        if (!empty($_POST))
//        {
//            foreach($_POST as $key => $data)
//            {
//                $_GET[$key] = $data;
//            }
//        }
//        $payment  = get_payment($_GET['code']);
//        $order_sn = str_replace($_GET['subject'], '', $_GET['out_trade_no']);
//        $order_sn = trim(addslashes($order_sn));
//
//        /* 检查数字签名是否正确 */
//        ksort($_GET);
//        reset($_GET);
//
//        $sign = '';
//        foreach ($_GET AS $key=>$val)
//        {
//            if ($key != 'sign' && $key != 'sign_type' && $key != 'code')
//            {
//                $sign .= "$key=$val&";
//            }
//        }
//
//        $sign = substr($sign, 0, -1) . $payment['alipay_key'];
//        //$sign = substr($sign, 0, -1) . ALIPAY_AUTH;
//        if (md5($sign) != $_GET['sign'])
//        {
//            return false;
//        }
//
//        /* 检查支付的金额是否相符 */
//        if (!check_money($order_sn, $_GET['total_fee']))
//        {
//            return false;
//        }
//
//        if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS')
//        {
//            /* 改变订单状态 */
//            order_paid($order_sn, 2);
//
//            return true;
//        }
//        elseif ($_GET['trade_status'] == 'TRADE_FINISHED')
//        {
//            /* 改变订单状态 */
//            order_paid($order_sn);
//
//            return true;
//        }
//        elseif ($_GET['trade_status'] == 'TRADE_SUCCESS')
//        {
//            /* 改变订单状态 */
//            order_paid($order_sn, 2);
//
//            return true;
//        }
//        else
//        {
//            return false;
//        }
    }
}

?>
