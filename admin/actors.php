<?php

/**
 * ECSHOP 管理中心演员管理
 * ============================================================================
 * * 版权所有 2005-2018 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: actors.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("actors"), $db, 'actor_id', 'actor_name');

/*------------------------------------------------------ */
//-- 演员列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['04_actors_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['05_actors_add'], 'href' => 'actors.php?act=add'));
    $smarty->assign('full_page',    1);

    $performer_list = get_actorlist();

    $smarty->assign('actor_list',   $performer_list['actors']);
    $smarty->assign('filter',       $performer_list['filter']);
    $smarty->assign('record_count', $performer_list['record_count']);
    $smarty->assign('page_count',   $performer_list['page_count']);

    assign_query_info();
    $smarty->display('actors_list.htm');
}

/*------------------------------------------------------ */
//-- 添加演员
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('actors_manage');

    $smarty->assign('ur_here',     $_LANG['05_actors_add']);
    $smarty->assign('action_link', array('text' => $_LANG['04_actors_list'], 'href' => 'actors.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->assign('actor', array('sort_order'=>50, 'is_show'=>1));
    $smarty->display('actors_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('actors_manage');

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;

    /*检查演员名是否重复*/
//    $is_only = $exc->is_only('actor_name', $_POST['actor_name']);
//
//    if (!$is_only)
//    {
//        sys_msg(sprintf($_LANG['actorname_exist'], stripslashes($_POST['actor_name'])), 1);
//    }

    /*对描述处理*/
    if (!empty($_POST['actor_desc']))
    {
        $_POST['actor_desc'] = $_POST['actor_desc'];
    }

     /*处理图片*/
    $img_name = basename($image->upload_image($_FILES['actor_avatar'],'actoravatar'));

     /*处理国家*/
    $country = $_POST['country'];

    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('actors')."(actor_name, country, actor_desc, actor_avatar, is_show, sort_order) ".
           "VALUES ('$_POST[actor_name]', '$country', '$_POST[actor_desc]', '$img_name', '$is_show', '$_POST[sort_order]')";
    $db->query($sql);

    admin_log($_POST['actor_name'],'add','actors');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'actors.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'actors.php?act=list';

    sys_msg($_LANG['actoradd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑演员
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('actors_manage');
    $sql = "SELECT actor_id, actor_name, country, actor_avatar, actor_desc, is_show, sort_order ".
            "FROM " .$ecs->table('actors'). " WHERE actor_id='$_REQUEST[id]'";
    $actor = $db->GetRow($sql);

    $smarty->assign('ur_here',     $_LANG['actor_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['04_actors_list'], 'href' => 'actors.php?act=list&' . list_link_postfix()));
    $smarty->assign('actor',       $actor);
    $smarty->assign('form_action', 'updata');

    assign_query_info();
    $smarty->display('actors_info.htm');
}
elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('actors_manage');
    if ($_POST['actor_name'] != $_POST['old_actorname'])
    {
        /*检查演员名是否相同*/
//        $is_only = $exc->is_only('actor_name', $_POST['actor_name'], $_POST['id']);
//
//        if (!$is_only)
//        {
//            sys_msg(sprintf($_LANG['actorname_exist'], stripslashes($_POST['actor_name'])), 1);
//        }
    }

    /*对描述处理*/
    if (!empty($_POST['actor_desc']))
    {
        $_POST['actor_desc'] = $_POST['actor_desc'];
    }

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
     /*处理国家*/
    $country = $_POST['country'];

    /* 处理图片 */
    $img_name = basename($image->upload_image($_FILES['actor_avatar'],'actoravatar'));
    $param = "actor_name = '$_POST[actor_name]',  country='$country', actor_desc='$_POST[actor_desc]', is_show='$is_show', sort_order='$_POST[sort_order]' ";
    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,actor_avatar = '$img_name' ";
    }

    if ($exc->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['actor_name'], 'edit', 'actor');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'actors.php?act=list&' . list_link_postfix();
        $note = vsprintf($_LANG['actoredit_succed'], $_POST['actor_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑演员名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_actor_name')
{
    check_authz_json('actors_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc->num("actor_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['actorname_exist'], $name));
    }
    else
    {
        if ($exc->edit("actor_name = '$name'", $id))
        {
            admin_log($name,'edit','actor');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['actoredit_fail'], $name));
        }
    }
}

elseif($_REQUEST['act'] == 'add_actor')
{
    $actor = empty($_REQUEST['actor']) ? '' : json_str_iconv(trim($_REQUEST['actor']));

    if(actor_exists($actor))
    {
        make_json_error($_LANG['actor_name_exist']);
    }
    else
    {
        $sql = "INSERT INTO " . $ecs->table('actors') . "(actor_name)" .
               "VALUES ( '$actor')";

        $db->query($sql);
        $actor_id = $db->insert_id();

        $arr = array("id"=>$actor_id, "actor"=>$actor);

        make_json_result($arr);
    }
}
/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('actors_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc->get_name($id);

    if ($exc->edit("sort_order = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','actor');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['actoredit_fail'], $name));
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show')
{
    check_authz_json('actors_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_show='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 删除演员
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('actors_manage');

    $id = intval($_GET['id']);

    /* 删除该演员的图标 */
    $sql = "SELECT actor_avatar FROM " .$ecs->table('actors'). " WHERE actor_id = '$id'";
    $avatar_name = $db->getOne($sql);
    if (!empty($avatar_name))
    {
        @unlink(ROOT_PATH . DATA_DIR . '/actoravatar/' .$avatar_name);
    }

    $exc->drop($id);

    $url = 'actors.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除演员图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_avatar')
{
    /* 权限判断 */
    admin_priv('actors_manage');
    $actor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 取得avatar名称 */
    $sql = "SELECT actor_avatar FROM " .$ecs->table('actors'). " WHERE actor_id = '$actor_id'";
    $avatar_name = $db->getOne($sql);

    if (!empty($avatar_name))
    {
        @unlink(ROOT_PATH . DATA_DIR . '/actoravatar/' .$avatar_name);
        $sql = "UPDATE " .$ecs->table('actors'). " SET actor_avatar = '' WHERE actor_id = '$actor_id'";
        $db->query($sql);
    }
    $link= array(array('text' => $_LANG['actor_edit_lnk'], 'href' => 'actors.php?act=edit&id=' . $actor_id), array('text' => $_LANG['actor_list_lnk'], 'href' => 'actors.php?act=list'));
    sys_msg($_LANG['drop_actor_avatar_success'], 0, $link);
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $actor_list = get_actorlist();
    $smarty->assign('actor_list',   $actor_list['actors']);
    $smarty->assign('filter',       $actor_list['filter']);
    $smarty->assign('record_count', $actor_list['record_count']);
    $smarty->assign('page_count',   $actor_list['page_count']);

    make_json_result($smarty->fetch('actors_list.htm'), '',
        array('filter' => $actor_list['filter'], 'page_count' => $actor_list['page_count']));
}

/**
 * 获取演员列表
 *
 * @access  public
 * @return  array
 */
function get_actorlist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        /* 记录总数以及页数 */
        if (isset($_POST['actor_name']))
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('actors') .' WHERE actor_name = \''.$_POST['actor_name'].'\'';
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('actors');
        }

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查询记录 */
        if (isset($_POST['actor_name']))
        {
            if(strtoupper(EC_CHARSET) == 'GBK')
            {
                $keyword = iconv("UTF-8", "gb2312", $_POST['actor_name']);
            }
            else
            {
                $keyword = $_POST['actor_name'];
            }
            $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('actors')." WHERE actor_name like '%{$keyword}%' ORDER BY sort_order ASC";
        }
        else
        {
            $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('actors')." ORDER BY sort_order ASC";
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
            '<a href="../' . DATA_DIR . '/actoravatar/'.$rows['actor_avatar'].'" target="_brank"><img src="images/image.svg" width="16" height="16" border="0" alt='.$GLOBALS['_LANG']['actor_avatar'].' /></a>';

        $rows['actor_avatar'] = $actor_avatar;

        $arr[] = $rows;
    }

    return array('actors' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
