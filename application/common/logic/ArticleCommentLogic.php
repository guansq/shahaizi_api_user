<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\common\logic;

use think\Page;
/**
 * 文章评论表
 * @package common\Logic
 */
class ArticleCommentLogic extends BaseLogic{

    protected $table = 'ruit_article_comment';

    const TYPE_DYNAMIC = 1;    // 动态
    const TYPE_GUIDE   = 2;    // 攻略
    const TYPE_TALENT  = 3;     //达人

    const TYPE_TABLE_ARR = [
        self::TYPE_DYNAMIC => 'article_new_action',
        self::TYPE_GUIDE => 'article_hot_guide',
        self::TYPE_TALENT => 'article_local_talent',
    ];

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:
     * @param $type      int 文章类型
     * @param $articleId int 文章id
     */
    public static function getListByTypeAndObjid($type, $articleId, $viewUserId = 0){
        $field = [
            'comment_id' => 'id',
            'user_id' => 'owner',
            'content' => 'content',
            'add_time' => 'createTime',
            'img' => 'img',
            'is_anonymous' => 'isAnonymous',
            'parent_id' => 'parentId',
        ];
        $list = self::where('type', $type)
            ->where('article_id', $articleId)
            ->where('parent_id', 0)
            ->field($field)
            ->select();
        if(empty($list)){
            return resultArray(4004);
        }
        foreach($list as $comm){
            $comm['owner'] = UsersLogic::getBaseInfoById($comm['owner'], $viewUserId, $comm['isAnonymous'])['result'];
            $comm['createTimeFmt'] = date('Y-m-d', $comm['createTime']);
            $comm['isPraise'] = UserPraiseLogic::isPraised($comm->id, $viewUserId, UserPraiseLogic::TYPE_ARTICLE_COMMENT);
            $comm['replies'] = self::getListByPid($comm->id, $viewUserId)['result'];
        }
        return resultArray(2000, '', $list);
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:
     * @param $type      int 文章类型
     * @param $articleId int 文章id
     */
    public static function getListByTypeAndObjidByPage($type, $articleId, $viewUserId = 0){
        $field = [
            'comment_id' => 'id',
            'user_id' => 'owner',
            'content' => 'content',
            'add_time' => 'createTime',
            'img' => 'img',
            'is_anonymous' => 'isAnonymous',
            'parent_id' => 'parentId',
        ];
        $count = self::where('type', $type)
            ->where('article_id', $articleId)
            ->where('parent_id', 0)->count();
        //print_r($count);die;
        $page = new Page($count);
        $list = self::where('type', $type)
            ->where('article_id', $articleId)
            ->where('parent_id', 0)
            ->limit($page->firstRow, $page->listRows)
            ->field($field)
            ->select();
        if(empty($list)){
            return resultArray(4004);
        }
        foreach($list as $comm){
            $comm['owner'] = UsersLogic::getBaseInfoById($comm['owner'], $viewUserId, $comm['isAnonymous'])['result'];
            $comm['createTimeFmt'] = date('Y-m-d', $comm['createTime']);
            $comm['isPraise'] = UserPraiseLogic::isPraised($comm->id, $viewUserId, UserPraiseLogic::TYPE_ARTICLE_COMMENT);
            $comm['replies'] = self::getListByPid($comm->id, $viewUserId)['result'];
        }
        $ret = [
            'p' => $page->nowPage,
            'totalPages' => $page->totalPages,
            'list' => $list,
        ];
        return resultArray(2000, '', $ret);
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 获取回复列表
     * @param $id
     */
    public static function getListByPid($id, $viewUserId = 0){
        $field = [
            'comment_id' => 'id',
            'user_id' => 'owner',
            'content' => 'content',
            'add_time' => 'createTime',
            'img' => 'img',
            'is_anonymous' => 'isAnonymous',
            'parent_id' => 'parentId',
        ];
        $list = self::where('parent_id', $id)->field($field)->select();
        if(empty($list)){
            return resultArray(4004);
        }
        foreach($list as $comm){
            $comm['owner'] = UsersLogic::getBaseInfoById($comm['owner'], $viewUserId, $comm['isAnonymous'])['result'];
            $comm['createTimeFmt'] = date('Y-m-d', $comm['createTime']);
            $comm['isPraise'] = UserPraiseLogic::isPraised($comm->id, $viewUserId, UserPraiseLogic::TYPE_ARTICLE_COMMENT);
            $comm['replies'] = self::getListByPid($comm->id);
        }
        return resultArray(2000,'',$list);
    }

}