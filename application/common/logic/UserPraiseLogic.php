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
class UserPraiseLogic extends BaseLogic{


    protected $table = 'ruit_user_praise';

    const TYPE_DYNAMIC = 1;    // 动态
    const TYPE_GUIDE   = 2;    // 攻略
    const TYPE_LINE    = 3;     // 线路
    const TYPE_PACKCAR = 4;     // 包车产品
    const TYPE_TALENT  = 5;     //达人


    const TYPE_TABLE_ARR = [
        self::TYPE_DYNAMIC => 'article_new_action',
        self::TYPE_GUIDE => 'article_hot_guide',
        self::TYPE_LINE => 'pack_line',
        self::TYPE_PACKCAR => 'pack_car_product',
        self::TYPE_TALENT => 'article_local_talent',
    ];

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 攻略点赞数
     * @param $id
     * @return int|string
     */
    public function countPraiseOfGuide($id){
        return $this->where('obj_type', self::TYPE_GUIDE)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 动态点赞数
     * @param $id
     * @return int|string
     */
    public function countPraiseOfDynamic($id){
        return $this->where('obj_type', self::TYPE_DYNAMIC)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 线路点赞数
     * @param $id
     * @return int|string
     */
    public function countPraiseOfLine($id){
        return $this->where('obj_type', self::TYPE_LINE)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 包车产品点赞数
     * @param $id
     * @return int|string
     */
    public function countPraiseOfPackCar($id){
        return $this->where('obj_type', self::TYPE_PACKCAR)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 包车产品点赞数
     * @param $id
     * @return int|string
     */
    public function isPraisePackCar($id, $userId){
        return $this->where('obj_type', self::TYPE_PACKCAR)->where('obj_id', $id)->where('user_id', $userId)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 是否点过赞
     * @param $id
     * @return int|string
     */
    public function isPraise($id, $userId, $type){
        return $this->where('obj_type', $type)->where('obj_id', $id)->where('user_id', $userId)->count();
    }

    public function addPraise($userId, $type, $id){

        $where = [
            'obj_id' => $id,
            'obj_type' => $type,
            'user_id' => $userId,
        ];
        if($this->where($where)->count() >= 1){
            return resultArray(4005, '您已经点过赞了。');
        }

        if($type == self::TYPE_PACKCAR){ // 包车产品不是用户发布的 没有user_id 字段
            $owner = null;
        }else{
            $owner = M(self::TYPE_TABLE_ARR[$type])->field(['user_id'])->find($id);
        }
        $ownerId = empty($owner) ? null : $owner['user_id'];

        $data = array_merge($where, ['obj_owner_id' => $ownerId]);
        if(!$this->create($data)){
            return resultArray(5020);
        }
        return resultArray(2000);

    }


}