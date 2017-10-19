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


/**
 * Class CatsLogic
 * @package common\Logic
 */
class UserAttentionLogic extends BaseLogic{
    protected $table = 'ruit_user_attention';

    // 被关注对象类型 1=用户
    const TYPE_USER = 1;

    const TYPE_TABLE_ARR =[
        self::TYPE_USER       => 'users',
    ];

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: $viewUserId 是否关注了 $userId
     * @param $userId
     * @param $viewUserId
     */
    public static function isAttention($userId, $viewUserId){
        return self::where('obj_id',$userId)->where('obj_type',self::TYPE_USER)->where('user_id',$viewUserId)->count();
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe:添加关注
     * @param $type
     * @param $id
     * @param $userId
     */
    public function addAttention($userId,$type, $id){

        $owner = M(self::TYPE_TABLE_ARR[$type])->field(['user_id'])->find($id);
        $ownerId = empty($owner)?null:$owner['user_id'];
        $where = [
            'user_id' => $userId,
            'obj_type' => $type,
            'obj_id' => $id,
        ];
        if($this->where($where)->count() >= 1){
            return resultArray(4005, '您已经关注过了。');
        }
        $data = array_merge($where, ['add_time' => time(), 'obj_owner_id' => $ownerId]);
        if(!$this->create($data)){
            return resultArray(5020);
        }
        return resultArray(2000);

    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 取消关注
     * @param $type
     * @param $id
     * @param $userId
     */
    public function removeAttention($userId,$type, $id){
        $where = [
            'user_id' => $userId,
            'obj_type' => $type,
            'obj_id' => $id,
        ];
        $this->where($where)->delete();
        return resultArray(2000);
    }
}