<?php
namespace feehi\libs;
/**
 * ChannelController implements the CRUD actions for Channel model.
 */
class Constants
{

    const TabSize = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    public static function getSortNum()
    {
        return time();
    }

    const YesNo_Yes = 1;
    const YesNo_No = 0;
    public static function getYesNoItems($key = null)
    {
        $items = [
            self::YesNo_Yes => '是', 
            self::YesNo_No => '否'
        ];
        return self::getItems($items, $key);
    }

    public static function getWebsiteStatusItems($key = null)
    {
        $items = [
            self::YesNo_Yes => '正常',
            self::YesNo_No => '禁用'
        ];
        return self::getItems($items, $key);
    }

    const COMMENT_INITIAL = 0;
    const COMMENT_PUBLISH = 1;
    const COMMENT_RUBISSH = 2;
    public static function getCommentStatusItems($key = null)
    {
        $items = [
            self::COMMENT_INITIAL => '待审核',
            self::COMMENT_PUBLISH => '已通过',
            self::COMMENT_RUBISSH => '不通过',
        ];
        return self::getItems($items, $key);
    }

    const TARGET_BLANK = '_blank';
    const TARGET_SELF = '_self';
    public static function getTargetOpenMethod($key = null)
    {
        $items = [
            self::TARGET_BLANK => '是',
            self::TARGET_SELF => '否'
        ];
        return self::getItems($items, $key);
    }


    const HTTP_METHOD_ALL = 'all';
    const HTTP_METHOD_GET = 'get';
    const HTTP_METHOD_POST = 'post';
    public static function getHttpMethodItems($key = null)
    {
        $items = [
            self::HTTP_METHOD_ALL => 'all',
            self::HTTP_METHOD_GET => 'get',
            self::HTTP_METHOD_POST => 'post',
        ];
        return self::getItems($items, $key);
    }

    const PUBLISH_YES = 1;
    const PUBLISH_NO = 0;
    public static function getArticleStatus($key = null)
    {
        $items = [
            self::PUBLISH_YES => '发布',
            self::PUBLISH_NO => '草稿'
        ];
        return self::getItems($items, $key);
    }

    const INPUT_INPUT = 1;
    const INPUT_TEXTAREA = 2;
    const INPUT_UEDITOR = 3;
    public static function getInputTypeItems($key = null)
    {
        $items = [
            self::INPUT_INPUT => 'input',
            self::INPUT_TEXTAREA => 'textarea',
            self::INPUT_UEDITOR => 'ueditor',
        ];
        return self::getItems($items, $key);
    }

    const ARTICLE_VISIBILITY_PUBLIC = 0;
    const ARTICLE_VISIBILITY_SECRET = 1;
    const ARTICLE_VISIBILITY_COMMENT = 2;
    public static function getArticleVisibility($key = null)
    {
        $items = [
            self::ARTICLE_VISIBILITY_PUBLIC => '公开',
            self::ARTICLE_VISIBILITY_SECRET => '加密',
            self::ARTICLE_VISIBILITY_COMMENT => '回复',
        ];
        return self::getItems($items, $key);
    }

    const Status_Enable = 1;
    const Status_Desable = 0;
    public static function getStatusItems($key = null)
    {
        $items = [
            self::Status_Enable => '可用', 
            self::Status_Desable => '禁用'
        ];
        return self::getItems($items, $key);
    }

    public static function getDisplayItems($key = null)
    {
        $items = [
            self::Status_Enable => '<span class="label label-primary">显示</span>',
            self::Status_Desable => '<span class="label label-default">禁用</span>'
        ];
        return self::getItems($items, $key);
    }

    const Target_Self = '_self';
    const Target_blank = '_blank';
    public static function getTargetItems($key = null)
    {
        $items = [
            self::Target_Self => '当前窗口', 
            self::Target_blank => '新窗口'
        ];
        return self::getItems($items, $key);
    }

    const Visibility_Public = '1';
    const Visibility_Hidden = '2';
    const Visibility_Password = '3';
    const Visibility_Private = '4';
    public static function getVisibilityItems($key = null)
    {
        $items = [
            self::Visibility_Public => '公开', 
            self::Visibility_Hidden => '回复可见', 
            self::Visibility_Password => '密码保护', 
            self::Visibility_Private => '私有'
        ];
        return self::getItems($items, $key);
    }

    const Status_Publish = '1';
    const Status_Draft = '2';
    const Status_Pending = '3';
    public static function getStatusItemsForContent($key = null)
    {
        $items = [
            self::Status_Publish => '发布', 
            self::Status_Draft => '草稿', 
            self::Status_Pending => '等待审核'
        ];
        return self::getItems($items, $key);
    }

    public static function getRecommendItems($key = null)
    {
        $items = [
            // 0 => '无',
            1 => '一级推荐', 
            2 => '二级推荐', 
            3 => '三级推荐', 
            4 => '四级推荐', 
            5 => '五级推荐', 
            6 => '六级推荐', 
            7 => '七级推荐', 
            8 => '八级推荐', 
            9 => '九级推荐'
        ];
        return self::getItems($items, $key);
    }

    public static function getHeadlineItems($key = null)
    {
        $items = [
            // 0 => '无',
            1 => '一级头条', 
            2 => '二级头条', 
            3 => '三级头条', 
            4 => '四级头条', 
            5 => '五级头条', 
            6 => '六级头条', 
            7 => '七级头条', 
            8 => '八级头条', 
            9 => '九级头条'
        ];
        return self::getItems($items, $key);
    }

    public static function getStickyItems($key = null)
    {
        $items = [
            // 0 => '无',
            1 => '一级置顶', 
            2 => '二级置顶', 
            3 => '三级置顶', 
            4 => '四级置顶', 
            5 => '五级置顶', 
            6 => '六级置顶', 
            7 => '七级置顶', 
            8 => '八级置顶', 
            9 => '九级置顶'
        ];
        
        return self::getItems($items, $key);
    }

    public static function getTimezoneItems($key = null)
    {
        $items = [
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+11' => '(GMT - 11:00 小时) 中途岛，萨摩亚',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
            'Etc/GMT+12' => '(GMT - 12:00 小时) 安尼威托克岛，卡瓦加兰',
     ];
        return self::getItems($items, $key);
    }

    private static function getItems($items, $key = null,$throw=false)
    {
        if ($key !== null)
        {
            if (key_exists($key, $items))
            {
                return $items[$key];
            }
            if($throw)
            {
                throw new InvalidParamException();
            }
            return 'unknown key:' . $key;
        }
        return $items;
    }
}
