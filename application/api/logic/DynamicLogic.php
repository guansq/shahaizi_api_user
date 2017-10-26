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

namespace app\api\logic;

use app\common\logic\ArticleCommentLogic;
use app\common\logic\UserCollectLogic;
use app\common\logic\UserPraiseLogic;
use app\common\logic\UsersLogic;
use think\Page;


/**
 * 个人动态定义
 * Class CatsLogic
 * @package common\Logic
 */
class DynamicLogic extends BaseLogic{

    protected $table = 'ruit_article_new_action';

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 发布动态
     * @param $reqParams
     * @Param  img    封面图片 多张用“|” 分割 ，第一张为默认封面.
     * @Param  title  标题.
     * @Param  content 内容.
     * @param $user
     */
    public function createDynamic($reqParams, $user){
        $data = [
            'cover_img' => $reqParams['img'],     // 封面图片 多张用“|” 分割 ，第一张为默认封面.
            'title' => $reqParams['title'],     // 标题.
            'content' => $reqParams['content'],    // 内容.
            'user_id' => $user['user_id'],
            'user_name' => $user['nickname'],
        ];
        if(!$this->create($data)){
            return resultArray(5020);
        };
        return resultArray(2000);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Time: ${DAY}
     * Describe:
     * @param $user_id
     * @success {number} list.id     id.
     * @success {string} list.img    封面图片.
     * @success {string} list.title  标题.
     * @success {string} list.subTitle 副标题.
     * @success {number} list.timeStamp  发布时间戳.
     * @success {string} list.timeFmt    格式化发布时间.
     * @success {number} list.readNum  阅读量.
     */
    public function getDynamicPageByUserId($user_id){
        $where = ['user_id' => $user_id];
        return $this->getDynamicPageByWhere($where);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Time: ${DAY}
     * Describe:
     * @param $id
     * @param $user_id
     *
     * @success id            id.
     * @success img           封面图片.
     * @success title         标题.
     * @success subTitle      副标题.
     * @success content       内容.
     * @success timeStamp         发布时间戳.
     * @success timeFmt           格式化发布时间
     * @success praiseNum         点赞数量.
     * @success readNum           阅读量.
     * @success isCollect         是否收藏.
     * @success isPraise          是否点赞.
     * @success ownerId    发布人id..
     * @success ownerName  发布人昵称..
     * @success ownerAvatar 发布人头像..
     *
     */
    public function getDynamicDetailWithUserId($id, $user_id){

        $fields = [
            'act_id' => 'id',
            'user_id' => 'ownerId',
            'cover_img' => 'img',
            'title',
            'content',
            'summary' => 'subTitle',
            'read_num' => 'readNum',
            'create_at' => 'timeStamp',
        ];
        $dynamic = $this->where('act_id', $id)->field($fields)->find();
        if(empty($dynamic)){
            return resultArray(4004, '该动态已经不存在');
        }
        $user = UserLogic::where('user_id', $dynamic['ownerId'])->find();

        $dynamic['timeFmt'] = date('Y.m.d', $dynamic['timeStamp']);
        $dynamic['isCollect'] = UserCollectLogic::where('goods_id', $id)
            ->where('model_type', UserCollectLogic::TYPE_DYNAMIC)
            ->where('user_id', $user_id)
            ->count();
        $dynamic['collectNum'] = UserCollectLogic::where('goods_id', $id)
            ->where('model_type', UserCollectLogic::TYPE_DYNAMIC)
            ->count();
        $dynamic['isPraise'] = UserPraiseLogic::where('obj_id', $id)
            ->where('obj_type', UserPraiseLogic::TYPE_DYNAMIC)
            ->where('user_id', $user_id)
            ->count();
        $dynamic['praiseNum'] = UserPraiseLogic::where('obj_id', $id)
            ->where('obj_type', UserPraiseLogic::TYPE_DYNAMIC)
            ->count();
        $dynamic['owner'] = UsersLogic::getBaseInfo($user, $user_id)['result'];
        $dynamic['comments'] = ArticleCommentLogic::getListByTypeAndObjid(ArticleCommentLogic::TYPE_DYNAMIC, $id, $user_id)['result'];

        return resultArray(2000, '', $dynamic);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe:
     * @param $id
     * @param $user_id
     */
    public function deleteDynamic($id, $user_id){
        $fields = [
            'act_id' => 'id',
            'user_id',
        ];
        $dynamic = $this->where('act_id', $id)->field($fields)->find();
        if(empty($dynamic)){
            return resultArray(4004, '要删除的动态已经不存在');
        }
        if($dynamic->user_id != $user_id){
            return resultArray(4010, '无权删除');
        }
        $dynamic->delete();
        return resultArray(2000);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 我收藏的动态列表
     * @param $user_id
     */
    public function getCollectDynamicPage($user_id){

        $userColl = new  UserCollectLogic();

        $count = $userColl->where('user_id', $user_id)->where('model_type', UserCollectLogic::TYPE_DYNAMIC)->count();
        $page = new Page($count);

        $ids = $userColl->where('user_id', $user_id)
            ->where('model_type', UserCollectLogic::TYPE_DYNAMIC)
            ->limit($page->firstRow, $page->listRows)
            ->order('add_time DESC')
            ->column('goods_id');
        $fields = [
            'act_id' => 'id',
            'cover_img' => 'img',
            'title',
            'summary' => 'subTitle',
            'read_num' => 'readNum',
            'create_at' => 'timeStamp',
            'dyn.user_id' => 'ownerId',
            'nickname' => 'ownerName',
            'head_pic' => 'ownerAvatar',
        ];

        $list = $this->alias('dyn')->join('ruit_users user', 'user.user_id=dyn.user_id', 'LEFT')->where('act_id', [
            'IN',
            $ids
        ])->order('create_at DESC')->field($fields)->select();

        foreach($list as &$item){
            $item['img'] = explode('|', $item['img'])[0];
            $item['timeFmt'] = date('Y.m.d', $item['timeStamp']);
            $item['praiseNum'] = UserPraiseLogic::where('obj_id', $item['id'])
                ->where('obj_type', UserPraiseLogic::TYPE_DYNAMIC)
                ->count();
        }

        $ret = [
            'p' => $page->nowPage,
            'totalPages' => $page->totalPages,
            'list' => $list,
        ];
        return resultArray(2000, '', $ret);
    }

    public function getDynamicPage($sortField = 'create_at', $sortType = 'DESC'){
        return $this->getDynamicPageByWhere([], $sortField, $sortType);
    }

    private function getDynamicPageByWhere($where = [], $sortField = 'create_at', $sortType = 'DESC'){
        $fields = [
            'act_id' => 'id',
            'cover_img' => 'img',
            'title',
            'summary' => 'subTitle',
            'read_num' => 'readNum',
            'user_id' => 'owner',
            'create_at' => 'timeStamp',
        ];
        $count = $this->where($where)->count();
        $page = new Page($count);
        $list = $this->where($where)
            ->limit($page->firstRow, $page->listRows)
            ->order("$sortField $sortType , sort")
            ->field($fields)
            ->select();

        foreach($list as &$item){
            $item['img'] = explode('|', $item['img'])[0];
            $item['timeFmt'] = date('Y.m.d', $item['timeStamp']);
            $item['praiseNum'] = UserPraiseLogic::where('obj_id', $item['id'])
                ->where('obj_type', UserPraiseLogic::TYPE_DYNAMIC)
                ->count();
            $item['owner'] = UsersLogic::getBaseInfoById($item['owner'])['result']; // todo
        }

        $ret = [
            'p' => $page->nowPage,
            'totalPages' => $page->totalPages,
            'list' => $list,
        ];
        return resultArray(2000, '', $ret);
    }
}