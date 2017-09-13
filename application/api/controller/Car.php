<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 18:16
 */
namespace app\api\controller;

use think\controller;
use app\common\logic\CarLogic;

class Car extends Base{

    /**
     * @api {GET}  /index.php?m=Api&c=Car&a=getCarInfo  得到system_car信息done 管少秋
     * @apiName     getCarInfo
     * @apiGroup    Car
     *
    */
    public function getCarInfo(){
        $carLogic = new CarLogic();
        $result = $carLogic->get_car_info();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }
}