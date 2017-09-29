<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 9:30
 */
namespace app\common\logic;

use think\Model;

class ConfigLogic extends BaseLogic{
    /*
     * 得到城市区号
     */
    public function get_country_number(){
        return M('country_mobile_prefix')->select();
    }
}