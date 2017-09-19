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

use think\Page;


/**
 * 攻略
 * @package common\Logic
 */
class StrategyLogic extends BaseLogic{

    protected $table = 'ruit_article_hot_guide';

    // 1:热门攻略2:地区攻略
    const TYPE_HOT= 1;
    const TYPE_REGION = 2;

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 发布攻略
     * @param $reqParams
     * @Param  img    封面图片 多张用“|” 分割 ，第一张为默认封面.
     * @Param  title  标题.
     * @Param  content 内容.
     * @param $user
     */
    public function createStrategy($reqParams, $user){
        $data = [
            'title' => $reqParams['title'],     // 标题.
            'guide_img' => $reqParams['img'],     // 封面图片 多张用“|” 分割
            'cover_img' => explode('|', $reqParams['img'])[0],     // 第一张为默认封面.
            'content' => $reqParams['content'],    // 内容.
            'summary' => $reqParams['summary'],    // 内容.
            'city_id' => $reqParams['regionId'],
            'city' => RegionInterLogic::where('id',$reqParams['regionId'])->value('name') ,
            'user_id' => $user['user_id'],
            'user_name' => $user['nickname'],
            'type' => self::TYPE_REGION,
            'status' => 1,
        ];
        if(!$this->create($data)){
            return resultArray(5020);
        };
        return resultArray(2000);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe:
     * @param $user_id
     * @success {number} id     id.
     * @success {string} img    封面图片.
     * @success {string} title  标题.
     * @success {string} subTitle 副标题.
     * @success {number} timeStamp  发布时间戳.
     * @success {string} timeFmt    格式化发布时间.
     * @success {number} praiseNum  点赞数量.
     */
    public function getStrategyPageByUserId($user_id){
        $fields = [
            'guide_id' => 'id',
            'cover_img' => 'img',
            'title',
            'summary' => 'subTitle',
            'read_num' => 'readNum',
            'create_at' => 'timeStamp',
        ];
        $count = $this->where('user_id', $user_id)->count();
        $page = new Page($count);
        $list = $this->where('user_id', $user_id)
            ->limit($page->firstRow, $page->listRows)
            ->order('create_at DESC')
            ->field($fields)
            ->select();

        foreach($list as &$item){
            $item['img'] = explode('|', $item['img'])[0];
            $item['timeFmt'] = date('Y.m.d', $item['timeStamp']);
            $item['praiseNum'] = UserPraiseLogic::where('obj_id', $item['id'])->where('obj_type', UserPraiseLogic::TYPE_STRATEGY)->count();
        }

        $ret = [
            'p' => $page->nowPage,
            'totalPages' => $page->totalPages,
            'list' => $list,
        ];
        return resultArray(2000, '', $ret);
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
    public function getStrategyDetailWithUserId($id, $user_id){

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
        $Strategy = $this->where('act_id', $id)->field($fields)->find();
        if(empty($Strategy)){
            return resultArray(4004, '该攻略已经不存在');
        }
        $user = UserLogic::where('user_id', $Strategy['ownerId'])->find();

        $Strategy['timeFmt'] = date('Y.m.d', $Strategy['timeStamp']);
        $Strategy['isCollect'] = UserCollectLogic::where('goods_id',$id)->where('model_type',UserCollectLogic::TYPE_Strategy)->where('user_id', $user_id)->count();
        $Strategy['collectNum'] = UserCollectLogic::where('goods_id',$id)->where('model_type',UserCollectLogic::TYPE_Strategy)->count();
        $Strategy['isPraise'] = UserPraiseLogic::where('obj_id', $id)->where('obj_type', UserPraiseLogic::TYPE_Strategy)->where('user_id', $user_id)->count();
        $Strategy['praiseNum'] = UserPraiseLogic::where('obj_id', $id)->where('obj_type', UserPraiseLogic::TYPE_Strategy)->count();
        $Strategy['ownerName'] =$user['nickname'];
        $Strategy['ownerAvatar'] = $user['head_pic'] ;


        return resultArray(2000, '', $Strategy);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe:
     * @param $id
     * @param $user_id
     */
    public function deleteStrategy($id, $user_id){
        $fields = [
            'guide_id' => 'id',
            'user_id',
        ];
        $Strategy = $this->where('guide_id', $id)->field($fields)->find();
        if(empty($Strategy)){
            return resultArray(4004,'要删除的攻略已经不存在');
        }
        if($Strategy->user_id != $user_id){
            return resultArray(4010,'无权删除');
        }
        $Strategy->delete();
        return resultArray(2000);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 我收藏的攻略列表
     * @param $user_id
     */
    public function getCollectStrategyPage($user_id){

        $userColl = new  UserCollectLogic();

        $count = $userColl->where('user_id', $user_id)->where('model_type',UserCollectLogic::TYPE_STRATEGY)->count();
        $page = new Page($count);

        $ids = $userColl->where('user_id', $user_id)
            ->where('model_type',UserCollectLogic::TYPE_Strategy)
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

        $list = $this->alias('dyn')->join('ruit_users user','user.user_id=dyn.user_id','LEFT')->where('act_id', ['IN', $ids])
            ->order('create_at DESC')
            ->field($fields)
            ->select();

        foreach($list as &$item){
            $item['img'] = explode('|', $item['img'])[0];
            $item['timeFmt'] = date('Y.m.d', $item['timeStamp']);
            $item['praiseNum'] = UserPraiseLogic::where('obj_id', $item['id'])->where('obj_type', UserPraiseLogic::TYPE_Strategy)->count();
        }

        $ret = [
            'p' => $page->nowPage,
            'totalPages' => $page->totalPages,
            'list' => $list,
        ];
        return resultArray(2000, '', $ret);
    }
}