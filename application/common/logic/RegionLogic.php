<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 15:34
 */
namespace app\common\logic;

use think\Model;
use think\Db;

class RegionLogic extends Model{
    /*
     * 得到城市信息
     */
    public function get_city_info($where){
        $result = M('region_new')->where($where)->select();
        return $result;
    }
}