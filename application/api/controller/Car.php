<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 18:16
 */
namespace app\api\controller;

use app\common\logic\PackCarBarLogic;
use app\common\logic\PackCarInfoLogic;
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
     * @api {GET}  /index.php?m=Api&c=Car&a=getCarBrand  得到车辆品牌列表 fixme wxx
     * @apiName     getCarBrand
     * @apiGroup    Car
     * @apiSuccess {Number} id id.
     * @apiSuccess {Number} pid id.
     * @apiSuccess {Number} name 品牌名称.
     * @apiSuccess {Number} initialLetter  拼音首字母.
     * @apiSuccess {Number} status 状态.
     * @apiSuccess {Number} logo logo.
     */
    public function getCarBrand(){
        $carLogic = new PackCarBarLogic();
        $result = $carLogic->getValidList();
        return $this->returnJson(['status'=>2000,'msg'=>'成功','result'=>['list'=>$result]]);
    }


    /**
     * @api {GET}  /index.php?m=Api&c=Car&a=getCarList  得到车辆列表 ok wxx
     * @apiName     getCarList
     * @apiGroup    Car
     * @apiSuccess {Number} car_id      车辆id.
     * @apiSuccess {Number} brand_id     品牌id.
     * @apiSuccess {String} brand_name    品牌名称.
     * @apiSuccess {Number} car_type_id     车辆类型id.
     * @apiSuccess {String} car_type_name    车辆类型名称.
     * @apiSuccess {Number} seat_num        车辆座位数.
     */
    public function getCarList(){
        $carLogic = new PackCarInfoLogic();
        $result = $carLogic->getList();
        return $this->returnJson(['status'=>2000,'msg'=>'成功','result'=>['list'=>$result]]);
    }
}