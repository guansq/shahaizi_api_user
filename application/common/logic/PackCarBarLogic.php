<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */
namespace app\common\logic;

use think\Model;
class PackCarBarLogic extends BaseLogic{
    protected $table ='ruit_pack_car_bar';

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:
     */
    public function getValidList(){
        $fields = [
            'id'=>'id',
            'pid'=>'pid',
            'car_info'=>'name',
            'initial_letter'=>'initialLetter',
            'status'=>'status',
            'car_brand_img'=>'logo',
        ];
        return $this->where('status',1)->field($fields)->select();
    }

}