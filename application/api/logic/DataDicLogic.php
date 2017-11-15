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
    const TYPE_TRIP = 1;
    const TYPE_RESTAURANT = 2;
    const TYPE_ROOM = 3;
    /*
     * 得到数据字典
     */
    public static function getDataDic($type){
        $where = [
            'is_show' => 1,
            'type' => $type,
        ];
        $list = M('data_dic')->field('id,name,img')->order('order_by asc')->where($where)->select();
        return $list;
    }
}