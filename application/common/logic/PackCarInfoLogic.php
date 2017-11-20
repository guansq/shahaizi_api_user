<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

class PackCarInfoLogic extends BaseLogic{
    protected $table = 'ruit_pack_car_bar';
    protected $resultSetType = 'collection';
    const STATUS_UNCHECK = 0;//0:待审核1:审核通过2:驳回
    const STATUS_PASS    = 1;//0:待审核1:审核通过2:驳回
    const STATUS_REFUSE  = 2;//0:待审核1:审核通过2:驳回

    const LEVEL_ONE = 1;
    const LEVEL_TWO = 2;
    const LEVEL_THREE = 3;
    const LEVEL_ARR = [
        self::LEVEL_ONE => '经济型',//1=>jinji,2=>shushi         2=>'shushixing'
        self::LEVEL_TWO => '舒适型',
        self::LEVEL_THREE => '豪华型',
    ];
    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:
     */
    public function getList(){
        $fields = [
            'pci.car_id' => 'car_id',
            'pci.brand_id' => 'brand_id',
            'cb.car_info' => 'brand_name',
            'pci.car_type_id' => 'car_type_id',
            'ct.car_info' => 'car_type_name',
            'pci.seat_num' => 'seat_num',
        ];
        $list = $this->alias('pci')
            ->join('ruit_pack_car_bar cb', 'pci.brand_id = cb.id', 'LEFT')
            ->join('ruit_pack_car_bar ct', 'pci.car_type_id = ct.id', 'LEFT')
            ->where('pci.is_state', self::STATUS_PASS)
            ->field($fields)
            ->select();

        return $list;
    }

    public function getMyCar($id){
        $seller_car =  M('pack_car_info')->alias('pci')
            ->field('pci.*,cb.car_info as brand_name,ct.car_info as type_name')
            ->join('ruit_pack_car_bar cb', 'pci.brand_id = cb.id', 'LEFT')
            ->join('ruit_pack_car_bar ct', 'pci.car_type_id = ct.id', 'LEFT')
            ->where("pci.seller_id",$id)
            ->where('pci.is_state',self::STATUS_PASS)
            ->select();
        return $seller_car;
    }

    /*
     * 得到车位数
     */
    public function getAllWhereInfo(){
        $list = $this->field('seat_num,car_level')->select();
        $list = $list->toArray();
        //print_r($list);die;
        if(empty($list)){
            return resultArray(-1,'暂无数据',[]);
        }
        $seat_list = array_unique(get_arr_column($list,'seat_num'));
        $seat = [];
        foreach($seat_list as $val){
            $seat[] = $val;
        }

        $level_list = array_unique(get_arr_column($list,'car_level'));
        $level = [];
        foreach($level_list as $val){
            $temp = [];
            $temp['id'] = $val;
            $temp['name'] = self::LEVEL_ARR[$val];
            $level[] = $temp;
        }
        //print_r($seat_list);die;

        return resultArray(1,'成功',['seat_list'=>$seat,'level_list'=>$level]);
    }


}