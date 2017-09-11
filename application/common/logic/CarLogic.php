<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */
namespace app\common\logic;

use think\Model;
class CarLogic extends Model{

    public function get_car_info(){
        return M('system_car_info')->order('site_num desc')->select();
    }
}