<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 18:16
 */
namespace app\api\controller;

use app\common\logic\PackCarBarLogic;
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

    /**
     * @api {GET}  /index.php?m=Api&c=Car&a=getCarBrand  得到车辆品牌列表 ok wxx
     * @apiName     getCarBrand
     * @apiGroup    Car
     * @apiSuccess {Number} id id.
     * @apiSuccess {Number} pid id.
     * @apiSuccess {Number} name 品牌名称.
     * @apiSuccess {Number} initialLetter  拼音首字母.
     * @apiSuccess {Number} status 状态.
     * @apiSuccess {Number} seatNum 座位数.
     * @apiSuccess {Number} logo logo.
     */
    public function getCarBrand(){
        $carLogic = new PackCarBarLogic();
        $result = $carLogic->getValidList();
        return $this->returnJson(['status'=>2000,'msg'=>'成功','result'=>['list'=>$result]]);
    }
}