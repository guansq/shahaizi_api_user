<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/23
 * Time: 18:39
 */

namespace app\common\logic;

class ConfigSetLogic extends BaseLogic{

    public function get_config_set(){
        return M('config_set')->order('sorting asc')->select();
    }
}