<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/15
 * Time: 13:59
 */
namespace app\api\logic;

class DataDicLogic extends BaseLogic{

    protected $table = 'ruit_data_dic';
    const TYPE_TRIP = '出行偏好';
    const TYPE_RESTAURANT = '推荐餐馆';
    const TYPE_ROOM = '推荐住宿';
    /*
     * 得到数据字典
     */
    public static function getDataDic($name){
        $where = [
            'is_show' => 1,
            'name' => ['like',"%{$name}%"],
            'parent_id' => ['neq',0],
        ];
        $list = M('data_dic')->field('id,description,img')->order('order_by asc')->where($where)->select();
        return $list;
    }
}