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

/**
 *  包车订单评论
 * Class CatsLogic
 * @package common\Logic
 */
class OrderCommentLogic extends BaseLogic{

    protected $table = 'ruit_order_comment';

    /*
     * 1是普通用户 2是司导
     */

    const TYPE_USER   = 1;
    const TYPE_SYSTEM = 2;
    const TYPE_SELLER = 3;


    public function getListByWere($where, $viewUserId = 0){
        $list = $this->where($where)->select();
        if(empty($list)){
            return [];
        }
        foreach($list as $item){
            $item['imgs'] = explode('|', $item['img']);
            $item['commemt_time_fmt'] = date('Y-m-d', $item['commemt_time']);
            $item['owner'] = $this->getOwner($item, $viewUserId, $item['is_anonymous']);
        }
        return $list;
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe:
     * @param $item
     */
    public function getOwner($item, $viewUserId = 0, $isAnonymous = 0){
        if($item['type'] == self::TYPE_USER){
            return UsersLogic::getBaseInfoById($item['user_id'], $viewUserId, $isAnonymous)['result'];
        }elseif($item['type'] == self::TYPE_SELLER){
            return SellerLogic::getBaseInfoById($item['user_id'], $viewUserId, $isAnonymous);
        }elseif($item['type'] == self::TYPE_SYSTEM){
            return [
                'nickname' => '平台',
                'avatar' => C('APP_DEFAULT_USER_AVATAR'),
            ];
        }
    }


}