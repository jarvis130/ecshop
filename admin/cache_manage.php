<?php

/**
 * ECSHOP
 * ============================================================================
 * * 版权所有 2005-2018 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: captcha_manage.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_utils.php');
$utils = new Utils();

/* 检查权限 */
admin_priv('shop_config');

/*------------------------------------------------------ */
//-- 获取缓存列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('full_page',    1);

    $cache_list = get_cachelist();

    $smarty->assign('cache_list',   $cache_list['caches']);
    $smarty->assign('filter',       $cache_list['filter']);
    $smarty->assign('record_count', $cache_list['record_count']);
    $smarty->assign('page_count',   $cache_list['page_count']);

    assign_query_info();
    $smarty->display('cache_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $cache_list = get_cachelist();
    $smarty->assign('cache_list',   $cache_list['caches']);
    $smarty->assign('filter',       $cache_list['filter']);
    $smarty->assign('record_count', $cache_list['record_count']);
    $smarty->assign('page_count',   $cache_list['page_count']);

    make_json_result($smarty->fetch('cache_list.htm'), '',
        array('filter' => $cache_list['filter'], 'page_count' => $cache_list['page_count']));
}

/*------------------------------------------------------ */
//-- 刷新
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'refresh')
{
    $url = APP_SERVER_URL . '/ecapi.cache.refresh';
    $data = array(
        'code' => $_POST['code']
    );
    $result = $utils->posturl($url, $data);
    if($result['error_code'] == 0){
        sys_msg('刷新成功');
    }else{
        sys_msg('刷新失败', 1);
    }

}

/**
 * 获取缓存列表
 *
 * @access  public
 * @return  array
 */
function get_cachelist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        /* 记录总数以及页数 */
        if (isset($_POST['name']))
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('caches') .' WHERE `name` like \'%'.$_POST['name'].'%\'';
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('caches');
        }

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查询记录 */
        if (isset($_POST['name']))
        {
            if(strtoupper(EC_CHARSET) == 'GBK')
            {
                $keyword = iconv("UTF-8", "gb2312", $_POST['name']);
            }
            else
            {
                $keyword = $_POST['name'];
            }
            $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('caches')." WHERE `name` like '%{$keyword}%'";
        }
        else
        {
            $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('caches');
        }

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $actor_avatar = empty($rows['actor_avatar']) ? '' :
            '<a href="../' . $rows['actor_avatar'].'" target="_brank"><img src="images/image.svg" width="16" height="16" border="0" alt='.$GLOBALS['_LANG']['actor_avatar'].' /></a>';

        $rows['actor_avatar'] = $actor_avatar;

        $arr[] = $rows;
    }

    return array('caches' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
?>