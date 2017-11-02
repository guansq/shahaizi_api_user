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

use app\common\logic\SellerLogic;
use think\Page;
use ruitu\PageVo;
/**
 *  消息
 * Class CatsLogic
 * @package common\Logic
 */
class MessageLogic extends BaseLogic{

    protected $table = 'ruit_article';

    //  状态 1:显示 0:隐藏
    const STATUS_HIDE = 0;
    const STATUS_SHOW = 1;

    const PUSH_USER = 1;
    const PUSH_BUSINESS = 2;

    public function getSystemMsgPage(){
        $count = $this->where('is_open', self::STATUS_SHOW)->count();
        // todo
        $count = 0;
        if(empty($count)){
            return resultArray(4004, '');
        }

        $ret = [// todo
        ];
        return resultArray(2000, '', $ret);
    }


    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:
     * @param $user_id
     * @param $type string  driver,house,shop
     */
    public function getHxUserPage($userId, $type){
        $poLogic = new PackOrderLogic();
        $sellerLogic = new SellerLogic();
        if($type == 'driver'){
            $sellerIds = $poLogic->where('user_id', $userId)
                ->where('status', ['<', PackOrderLogic::STATUS_FINISH])
                ->where('seller_id', ['<>', 0])
                ->group('seller_id')
                ->column('seller_id');
        }elseif($type == 'house'){
            //todo
            return resultArray(4004);
        }elseif($type == 'shop'){
            // todo
            return resultArray(4004);
        }else{
            return resultArray(4003);
        }
        if(empty($sellerIds)){
            return resultArray(4004);
        }

        $sellers = [];
        foreach($sellerIds as $sellerId){
            $sellers[] = $sellerLogic->getBaseInfoById($sellerId);
        }
        return resultArray(2000,'',$sellers);


    }

    //得到系统消息列表
    public function getSystemList($user_id){
        $push_user = self::PUSH_USER;
        if(empty($user_id)){
            $where = "push_users like '%{$push_user}%' AND receive_id is null";
        }else{
            $where = "push_users like '%{$push_user}%' AND (receive_id is null OR receive_id = $user_id)";
        }
        $list =  M('system_message')->where($where)->select();//echo die;->fetchSql(ture)
        $count = M('system_message')->where($where)->count();
        if(empty($list)){
            return resultArray(-1,'没有数据',[]);
        }
        $page = new Page($count);
        foreach($list as &$val){
            $val['content'] = htmlspecialchars_decode($val['content']);
            $val['create_at'] = shzDate($val['create_at']);
        }
        $ret = new PageVo($page,$list);
        return resultArray(1,'成功',$ret);
    }

    //得到系统消息详情
    public function getSystemInfo($id){
        $push_user = self::PUSH_USER;
        $where['push_users'] = ['like',"%$push_user%"];
        $where['id'] = $id;
        $info = M('system_message')->where($where)->find();
        if(empty($info)){
            return resultArray(-1,'没有数据',[]);
        }
        $info['content'] = htmlspecialchars_decode($info['content']);
        $info['create_at'] = shzDate($info['create_at']);
        return resultArray(1,'成功',$info);
    }
}