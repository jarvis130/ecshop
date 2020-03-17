<?php

/**
 * ECSHOP 程序说明
 * ===========================================================
 * * 版权所有 2005-2018 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ==========================================================
 * $Author: wangleisvn $
 * $Id: lead.php 16131 2009-05-31 08:21:41Z wangleisvn $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH."includes/cls_certificate.php");
$uri = $ecs->url();
$allow_suffix = array('gif', 'jpg', 'png', 'jpeg', 'bmp');

/*------------------------------------------------------ */
//-- 移动端应用配置
/*------------------------------------------------------ */
if ($_REQUEST['act']== 'list')
{
    /* 检查权限 */
    admin_priv('mobile_setting');
    $smarty->assign('ur_here', $_LANG['banner_ad_mobile']);
    $cert = new certificate;
    $isOpenWap = $cert->is_open_sn('fy');
    if($isOpenWap==false && $_SESSION['yunqi_login'] && $_SESSION['TOKEN'] ){
        $result = $cert->getsnlistoauth($_SESSION['TOKEN'] ,array());
        if($result['status']=='success'){
            $cert->save_snlist($result['data']);
            $isOpenWap = $cert->is_open_sn('fy');
        }
    }
    $tab = !$isOpenWap ? 'open' : 'enter';
    $charset = EC_CHARSET == 'utf-8' ? "utf8" : 'gbk';

    $playerdb = get_flash_xml();
    $num = 0;
    foreach ($playerdb as $key => $val)
    {
        $playerdb[$key]['id'] = $num;
        $num++;
        if (strpos($val['src'], 'http') === false)
        {
            $playerdb[$key]['src'] = $uri . $val['src'];
        }
        $playerdb[$key]['type_name'] = get_type_name($val['type']);
    }

    assign_query_info();
    $flash_dir = ROOT_PATH . 'data/flashaddata/';

    $smarty->assign('playerdb', $playerdb);
    $smarty->assign('group_list',$grouplist);
    $smarty->display('banner_config.html');
} elseif($_REQUEST['act']== 'del') {
    admin_priv('flash_manage');

    $id = (int)$_GET['id'];
    $flashdb = get_flash_xml();
    if (isset($flashdb[$id]))
    {
        $rt = $flashdb[$id];
    }
    else
    {
        $links[] = array('text' => '移动端轮播图配置', 'href' => 'mobile_ad_setting.php?act=list');
        sys_msg($_LANG['id_error'], 0, $links);
    }

    if (strpos($rt['src'], 'http') === false)
    {
        @unlink(ROOT_PATH . $rt['src']);
    }
    $temp = array();
    foreach ($flashdb as $key => $val)
    {
        if ($key != $id)
        {
            $temp[] = $val;
        }
    }
    put_flash_xml($temp);
    $error_msg = '';
    set_flash_data($_CFG['flash_theme'], $error_msg);
    ecs_header("Location: mobile_ad_setting.php?act=list\n");
    exit;
}
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('flash_manage');

    if (empty($_POST['step']))
    {
        $url = isset($_GET['url']) ? $_GET['url'] : (defined('FORCE_SSL_LOGIN') ? 'https://' : 'http://');
        $src = isset($_GET['src']) ? $_GET['src'] : '';
        $sort = 0;
        $img_type = 1;
        $rt = array('act'=>'add','img_url'=>$url,'img_src'=>$src, 'img_sort'=>$sort, 'img_type'=>$img_type);
        $width_height = get_width_height();
        assign_query_info();
        if(isset($width_height['width'])|| isset($width_height['height']))
        {
            $smarty->assign('width_height', sprintf($_LANG['width_height'], $width_height['width'], $width_height['height']));
        }

        $smarty->assign('action_link', array('text' => $_LANG['go_url'], 'href' => 'mobile_ad_setting.php?act=list'));
        $smarty->assign('rt', $rt);
        $smarty->assign('type_list', get_type_list());
        $smarty->assign('ur_here', $_LANG['add_picad']);
        $smarty->display('flashplay_ad_add.htm');
    }
    elseif ($_POST['step'] == 2)
    {
        if (!empty($_FILES['img_file_src']['name']))
        {
            if(!get_file_suffix($_FILES['img_file_src']['name'], $allow_suffix))
            {
                sys_msg($_LANG['invalid_type']);
            }
            $name = date('Ymd');
            for ($i = 0; $i < 6; $i++)
            {
                $name .= chr(mt_rand(97, 122));
            }
            $img_file_src_name_arr = explode('.', $_FILES['img_file_src']['name']);
            $name .= '.' . end($img_file_src_name_arr);
            $target = ROOT_PATH . DATA_DIR . '/afficheimg/' . $name;
            if (move_upload_file($_FILES['img_file_src']['tmp_name'], $target))
            {
                $src = DATA_DIR . '/afficheimg/' . $name;
            }
        }
        elseif (!empty($_POST['img_src']))
        {
            if(!get_file_suffix($_POST['img_src'], $allow_suffix))
            {
                sys_msg($_LANG['invalid_type']);
            }
            $src = $_POST['img_src'];
            if(strstr($src, 'http') && !strstr($src, $_SERVER['SERVER_NAME']))
            {
                $src = get_url_image($src);
            }
        }
        else
        {
            $links[] = array('text' => $_LANG['add_new'], 'href' => 'mobile_ad_setting.php?act=add');
            sys_msg($_LANG['src_empty'], 0, $links);
        }

        if (empty($_POST['img_url']))
        {
            $links[] = array('text' => $_LANG['add_new'], 'href' => 'mobile_ad_setting.php?act=add');
            sys_msg($_LANG['link_empty'], 0, $links);
        }

        // 获取flash播放器数据
        $flashdb = get_flash_xml();

        // 插入新数据
        array_unshift($flashdb, array('src'=>$src, 'url'=>$_POST['img_url'], 'text'=>$_POST['img_text'] ,'sort'=>$_POST['img_sort'],'type'=>$_POST['img_type']));

        // 实现排序
        $flashdb_sort   = array();
        $_flashdb       = array();
        foreach ($flashdb as $key => $value)
        {
            $flashdb_sort[$key] = $value['sort'];
        }
        asort($flashdb_sort, SORT_NUMERIC);
        foreach ($flashdb_sort as $key => $value)
        {
            $_flashdb[] = $flashdb[$key];
        }
        unset($flashdb, $flashdb_sort);

        put_flash_xml($_flashdb);
        $error_msg = '';
        set_flash_data($_CFG['flash_theme'], $error_msg);
        $links[] = array('text' => $_LANG['go_url'], 'href' => 'mobile_ad_setting.php?act=list');
        sys_msg($_LANG['edit_ok'], 0, $links);
    }
}
elseif ($_REQUEST['act'] == 'edit')
{
    admin_priv('flash_manage');

    $id = (int)$_REQUEST['id']; //取得id
    $flashdb = get_flash_xml(); //取得数据
    if (isset($flashdb[$id]))
    {
        $rt = $flashdb[$id];
    }
    else
    {
        $links[] = array('text' => $_LANG['go_url'], 'href' => 'mobile_ad_setting.php?act=list');
        sys_msg($_LANG['id_error'], 0, $links);
    }
    if (empty($_POST['step']))
    {
        $rt['act'] = 'edit';
        $rt['img_url'] = $rt['url'];
        $rt['img_src'] = $rt['src'];
        $rt['img_txt'] = $rt['text'];
        $rt['img_sort'] = empty($rt['sort']) ? 0 : $rt['sort'];
        $rt['img_type'] = $rt['type'];

        $rt['id'] = $id;
        $smarty->assign('action_link', array('text' => $_LANG['go_url'], 'href' => 'mobile_ad_setting.php?act=list'));
        $smarty->assign('rt', $rt);
        $smarty->assign('type_list', get_type_list());
        $smarty->assign('ur_here', $_LANG['edit_picad']);
        $smarty->display('flashplay_ad_add.htm');
    }
    elseif ($_POST['step'] == 2)
    {
        // if (empty($_POST['img_url']))
        // {
        //     //若链接地址为空
        //     $links[] = array('text' => $_LANG['return_edit'], 'href' => 'mobile_ad_setting.php?act=edit&id=' . $id);
        //     sys_msg($_LANG['link_empty'], 0, $links);
        // }

        if (!empty($_FILES['img_file_src']['name']))
        {
            if(!get_file_suffix($_FILES['img_file_src']['name'], $allow_suffix))
            {
                sys_msg($_LANG['invalid_type']);
            }
            //有上传
            $name = date('Ymd');
            for ($i = 0; $i < 6; $i++)
            {
                $name .= chr(mt_rand(97, 122));
            }
            $img_file_src_name_arr = explode('.', $_FILES['img_file_src']['name']);
            $name .= '.' . end($img_file_src_name_arr);
            $target = ROOT_PATH . DATA_DIR . '/afficheimg/' . $name;

            if (move_upload_file($_FILES['img_file_src']['tmp_name'], $target))
            {
                $src = DATA_DIR . '/afficheimg/' . $name;
            }
        }
        else if (!empty($_POST['img_src']))
        {
            $src =$_POST['img_src'];
            if(!get_file_suffix($_POST['img_src'], $allow_suffix))
            {
                sys_msg($_LANG['invalid_type']);
            }
            if(strstr($src, 'http') && !strstr($src, $_SERVER['SERVER_NAME']))
            {
                $src = get_url_image($src);
            }
        }
        else
        {
            $links[] = array('text' => $_LANG['return_edit'], 'href' => 'mobile_ad_setting.php?act=edit&id=' . $id);
            sys_msg($_LANG['src_empty'], 0, $links);
        }

        if (strpos($rt['src'], 'http') === false && $rt['src'] != $src)
        {
            @unlink(ROOT_PATH . $rt['src']);
        }
        $flashdb[$id] = array('src'=>$src,'url'=>$_POST['img_url'],'text'=>$_POST['img_text'],'sort'=>$_POST['img_sort'],'type'=>$_POST['img_type']);

        // 实现排序
        $flashdb_sort   = array();
        $_flashdb       = array();
        foreach ($flashdb as $key => $value)
        {
            $flashdb_sort[$key] = $value['sort'];
        }
        asort($flashdb_sort, SORT_NUMERIC);
        foreach ($flashdb_sort as $key => $value)
        {
            $_flashdb[] = $flashdb[$key];
        }
        unset($flashdb, $flashdb_sort);

        put_flash_xml($_flashdb);
        $error_msg = '';
        set_flash_data($_CFG['flash_theme'], $error_msg);
        $links[] = array('text' => $_LANG['go_url'], 'href' => 'mobile_ad_setting.php?act=list');
        sys_msg($_LANG['edit_ok'], 0, $links);
    }
}

function get_flash_xml()
{
    $banner_scene = 2;  // 广告位

    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('banners') . " WHERE `scene` = " . $banner_scene . " ORDER BY `sort`";
    $flashdb = $GLOBALS['db']->getAll($sql);

    return $flashdb;
}

function put_flash_xml($flashdb)
{
    $banner_scene = 2;  // 广告位

    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('banners') . " WHERE `scene` = " . $banner_scene;
    $GLOBALS['db']->query($sql);

    if (!empty($flashdb))
    {
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('banners') . " (`scene`, `src`, `url`, `text`, `sort`, `type`) VALUES ";
        foreach ($flashdb as $key => $val)
        {
            $sql .= "(" . $banner_scene . ", '" . $val['src'] . "', '" . $val['url'] . "', '" . $val['text'] . "', " . $val['sort'] . ", " . $val['type'] . ") ";
        }
        $GLOBALS['db']->query($sql);
    }
}

function get_url_image($url)
{
    $url_arr = explode('.', $url);
    $ext = strtolower(end($url_arr));
    if($ext != "gif" && $ext != "jpg" && $ext != "png" && $ext != "bmp" && $ext != "jpeg")
    {
        return $url;
    }

    $name = date('Ymd');
    for ($i = 0; $i < 6; $i++)
    {
        $name .= chr(mt_rand(97, 122));
    }
    $name .= '.' . $ext;
    $target = ROOT_PATH . DATA_DIR . '/afficheimg/' . $name;

    $tmp_file = DATA_DIR . '/afficheimg/' . $name;
    $filename = ROOT_PATH . $tmp_file;

    $img = file_get_contents($url);

    $fp = @fopen($filename, "a");
    fwrite($fp, $img);
    fclose($fp);

    return $tmp_file;
}

function get_width_height()
{
    $curr_template = $GLOBALS['_CFG']['template'];
    $path = ROOT_PATH . 'themes/' . $curr_template . '/library/';
    $template_dir = @opendir($path);

    $width_height = array();
    while($file = readdir($template_dir))
    {
        if($file == 'index_ad.lbi')
        {
            $string = file_get_contents($path . $file);
            $pattern_width = '/var\s*swf_width\s*=\s*(\d+);/';
            $pattern_height = '/var\s*swf_height\s*=\s*(\d+);/';
            preg_match($pattern_width, $string, $width);
            preg_match($pattern_height, $string, $height);
            if(isset($width[1]))
            {
                $width_height['width'] = $width[1];
            }
            if(isset($height[1]))
            {
                $width_height['height'] = $height[1];
            }
            break;
        }
    }

    return $width_height;
}

function get_flash_templates($dir)
{
    $flashtpls = array();
    $template_dir        = @opendir($dir);
    while ($file = readdir($template_dir))
    {
        if ($file != '.' && $file != '..' && is_dir($dir . $file) && $file != '.svn' && $file != 'index.htm')
        {
            $flashtpls[] = get_flash_tpl_info($dir, $file);
        }
    }
    @closedir($template_dir);
    return $flashtpls;
}

function get_flash_tpl_info($dir, $file)
{
    $info = array();
    if (is_file($dir . $file . '/preview.jpg'))
    {
        $info['code'] = $file;
        $info['screenshot'] = '../data/flashaddata/' . $file . '/preview.jpg';
        $arr = array_slice(file($dir . $file . '/cycle_image.js'), 1, 2);
        $info_name = explode(':', $arr[0]);
        $info_desc = explode(':', $arr[1]);
        $info['name'] = isset($info_name[1])?trim($info_name[1]):'';
        $info['desc'] = isset($info_desc[1])?trim($info_desc[1]):'';
    }
    return $info;
}

function set_flash_data($tplname, &$msg)
{
    $flashdata = get_flash_xml();
    if (empty($flashdata))
    {
        $flashdata[] = array(
            'src' => 'data/afficheimg/20081027angsif.jpg',
            'text' => 'ECShop',
            'url' =>'http://www.ecshop.com',
            'type' => 3
        );
        $flashdata[] = array(
            'src' => 'data/afficheimg/20081027wdwd.jpg',
            'text' => 'wdwd',
            'url' =>'https://www.wdwd.com',
            'type' => 3
        );
        $flashdata[] = array(
            'src' => 'data/afficheimg/20081027xuorxj.jpg',
            'text' => 'ECShop',
            // 'url' =>'http://help.ecshop.com/index.php?doc-view-108.htm'
            'url' =>'https://help-ecshop.xyunqi.com/index.php?doc-view-108.htm',
            'type' => 3
        );
    }
    switch($tplname)
    {
        case 'uproll':
            $msg = set_flash_uproll($tplname, $flashdata);
            break;
        case 'redfocus':
        case 'pinkfocus':
        case 'dynfocus':
            $msg = set_flash_focus($tplname, $flashdata);
            break;
        case 'default':
        default:
            $msg = set_flash_default($tplname, $flashdata);
            break;
    }
    return $msg !== true;
}

function set_flash_uproll($tplname, $flashdata)
{
    $data_file = ROOT_PATH . DATA_DIR . '/flashaddata/' . $tplname . '/data.xml';
    $xmldata = '<?xml version="1.0" encoding="' . EC_CHARSET . '"?><myMenu>';
    foreach ($flashdata as $data)
    {
        $xmldata .= '<myItem pic="' . $data['src'] . '" url="' . $data['url'] . '" type="' . $data['type'] . '" />';
    }
    $xmldata .= '</myMenu>';
    file_put_contents($data_file, $xmldata);
    return true;
}

function set_flash_focus($tplname, $flashdata)
{
    $data_file = ROOT_PATH . DATA_DIR . '/flashaddata/' . $tplname . '/data.js';
    $jsdata = '';
    $jsdata2 = array('url' => 'var pics=', 'txt' => 'var texts=', 'link' => 'var links=');
    $count = 1;
    $join = '';
    foreach ($flashdata as $data)
    {
        $jsdata .= 'imgUrl' . $count . '="' . $data['src'] . '";' . "\n";
        $jsdata .= 'imgtext' . $count . '="' . $data['text'] . '";' . "\n";
        $jsdata .= 'imgLink' . $count . '=escape("' . $data['url'] . '");' . "\n";
        $jsdata .= 'imgType' . $count . '="' . $data['type'] . '";' . "\n";
        if ($count != 1)
        {
            $join = '+"|"+';
        }
        $jsdata2['url'] .= $join . 'imgUrl' . $count;
        $jsdata2['txt'] .= $join . 'imgtext' . $count;
        $jsdata2['link'] .= $join . 'imgLink' . $count;
        $jsdata2['type'] .= $join . 'imgType' . $count;
        ++$count;
    }
    file_put_contents($data_file, $jsdata . "\n" . $jsdata2['url'] . ";\n" . $jsdata2['link'] . ";\n" . $jsdata2['txt'] . ";\n" . $jsdata2['type'] . ";");
    return true;
}

function set_flash_default($tplname, $flashdata)
{
    $data_file = ROOT_PATH . DATA_DIR . '/flashaddata/' . $tplname . '/data.xml';
    $xmldata = '<?xml version="1.0" encoding="' . EC_CHARSET . '"?><bcaster>';
    foreach ($flashdata as $data)
    {
        $xmldata .= '<item item_url="' . $data['src'] . '" link="' . $data['url'] . '" type="' . $data['type'] . '" />';
    }
    $xmldata .= '</bcaster>';
    file_put_contents($data_file, $xmldata);
    return true;
}

/**
 *  获取用户自定义广告列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function ad_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;
        $filter = array();
        $filter['sort_by'] = 'add_time';
        $filter['sort_order'] = 'DESC';

        /* 过滤信息 */
        $where = 'WHERE 1 ';

        /* 查询 */
        $sql = "SELECT ad_id, CASE WHEN ad_type = 0 THEN '图片'
                                   WHEN ad_type = 1 THEN 'Flash'
                                   WHEN ad_type = 2 THEN '代码'
                                   WHEN ad_type = 3 THEN '文字'
                                   ELSE '' END AS type_name, ad_name, add_time, CASE WHEN ad_status = 1 THEN '启用' ELSE '关闭' END AS status_name, ad_type, ad_status
                FROM " . $GLOBALS['ecs']->table("ad_custom") . "
                $where
                ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']. " ";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    /* 格式化数据 */
    foreach ($row AS $key => $value)
    {
        $row[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
    }

    $arr = array('ad' => $row, 'filter' => $filter);

    return $arr;
}

/**
 * 修改自定义相状态
 *
 * @param   int     $ad_id       自定义广告 id
 * @param   int     $ad_status   自定义广告 状态 0，关闭；1，开启。
 * @access  private
 * @return  Bool
 */
function modfiy_ad_status($ad_id, $ad_status = 0)
{
    $return = false;

    if (empty($ad_id))
    {
        return $return;
    }

    /* 查询自定义广告信息 */
    $sql = "SELECT ad_type, content, url, ad_status FROM " . $GLOBALS['ecs']->table("ad_custom") . " WHERE ad_id = $ad_id LIMIT 0, 1";
    $ad = $GLOBALS['db']->getRow($sql);

    if ($ad_status == 1)
    {
        /* 如果当前自定义广告是关闭状态 则修改其状态为启用 */
        if ($ad['ad_status'] == 0)
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table("ad_custom") . " SET ad_status = 1 WHERE ad_id = $ad_id";
            $GLOBALS['db']->query($sql);
        }

        /* 关闭 其它自定义广告 */
        $sql = "UPDATE " . $GLOBALS['ecs']->table("ad_custom") . " SET ad_status = 0 WHERE ad_id <> $ad_id";
        $GLOBALS['db']->query($sql);

        /* 用户自定义广告开启 */
        $sql = "UPDATE " . $GLOBALS['ecs']->table("shop_config") . " SET value = 'cus' WHERE id =337";
        $GLOBALS['db']->query($sql);
    }
    else
    {
        /* 如果当前自定义广告是关闭状态 则检查是否存在启用的自定义广告 */
        /* 如果无 则启用系统默认广告播放器 */
        if ($ad['ad_status'] == 0)
        {
            $sql = "SELECT COUNT(ad_id) FROM " . $GLOBALS['ecs']->table("ad_custom") . " WHERE ad_status = 1";
            $ad_status_1 = $GLOBALS['db']->getOne($sql);
            if (empty($ad_status_1))
            {
                $sql = "UPDATE " . $GLOBALS['ecs']->table("shop_config") . " SET value = 'sys' WHERE id =337";
                $GLOBALS['db']->query($sql);
            }
            else
            {
                $sql = "UPDATE " . $GLOBALS['ecs']->table("shop_config") . " SET value = 'cus' WHERE id =337";
                $GLOBALS['db']->query($sql);
            }
        }
        else
        {
            /* 当前自定义广告是开启状态 关闭之 */
            /* 如果无 则启用系统默认广告播放器 */
            $sql = "UPDATE " . $GLOBALS['ecs']->table("ad_custom") . " SET ad_status = 0 WHERE ad_id = $ad_id";
            $GLOBALS['db']->query($sql);

            $sql = "UPDATE " . $GLOBALS['ecs']->table("shop_config") . " SET value = 'sys' WHERE id =337";
            $GLOBALS['db']->query($sql);
        }
    }

    return $return = true;
}

/**
 * 查询类型列表
 */
function get_type_list(){
    $type_list = array(
        1 => '视频',
        2 => '演员',
        3 => '外部链接'
    );
    return $type_list;
}

/**
 * 查询类型名
 */
function get_type_name($type){
    $type_list = get_type_list();
    $type_name = $type_list[$type];
    return $type_name;
}
?>
