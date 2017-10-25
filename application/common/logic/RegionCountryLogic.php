<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 15:34
 */
namespace app\common\logic;

use think\Model;

class RegionCountryLogic extends BaseLogic{
    protected $table = 'ruit_region_country';


    public function getNameByid($id){
        return $this->where('id', $id)->value('name');
    }
}