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
 * Class CatsLogic
 * @package common\Logic
 */
class UserCollectLogic extends BaseLogic{
    protected $table = 'ruit_goods_collect';

    //  0为包车模块1为商城模块2为民宿模块 3=个人动态 4=个人攻略  5=线路  6=包车产品
    const TYPE_CAR      = 0;
    const TYPE_SHOP     = 1;
    const TYPE_HOUSE    = 2;
    const TYPE_DYNAMIC  = 3;
    const TYPE_STRATEGY = 4;
    const TYPE_LINE     = 5;
    const TYPE_PACKCAR  = 6;

    const TYPE_TABLE_ARR = [
        self::TYPE_CAR => 'pack_car_info',
        self::TYPE_SHOP => 'goods', // store + goods
        self::TYPE_HOUSE => 'home_house_info',
        self::TYPE_DYNAMIC => 'article_new_action',
        self::TYPE_STRATEGY => 'article_hot_guide',
        self::TYPE_PACKCAR => 'pack_car_product',
    ];

    /**
     * Author: WILL<314112362@qq.com>
     * Describe:添加收藏
     * @param $type
     * @param $id
     * @param $user_id
     */
    public function addCollect($user_id, $type, $id){
        $owner = M(self::TYPE_TABLE_ARR[$type])->field(['user_id'])->find($id);
        $ownerId = empty($owner) ? null : $owner['user_id'];
        $where = [
            'user_id' => $user_id,
            'model_type' => $type,
            'goods_id' => $id,
        ];
        if($this->where($where)->count() >= 1){
            return resultArray(4005, '您已经收藏过了。');
        }
        $data = array_merge($where, ['add_time' => time(), 'obj_owner_id' => $ownerId]);
        if(!$this->create($data)){
            return resultArray(5020);
        }
        return resultArray(2000);

    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 取消收藏
     * @param $TYPE_DYNAMIC
     * @param $id
     * @param $user_id
     */
    public function removeCollect($type, $id, $user_id){
        $where = [
            'model_type' => $type,
            'goods_id' => $id,
            'user_id' => $user_id,
        ];
        $this->where($where)->delete();
        return resultArray(2000);
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 是否收藏包车产品
     * @param $id
     * @return int|string
     */
    public function isCollectPackCar($id, $userId){
        return $this->where('model_type', self::TYPE_PACKCAR)->where('goods_id', $id)->where('user_id', $userId)->count();
    }
}