<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 19:40
 */
namespace app\api\controller;
use app\common\logic\DriverLogic;

class DriverPack extends Base{

    public $driverLogic;

    public function __construct(){
        $this->driverLogic = new DriverLogic();
    }

    /**
     * @api {GET}   /index.php?m=Api&c=DriverPack&a=getAllDriver     得到全部司导
     * @apiName     getAllDriver
     * @apiGroup    DriverPack
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     *{
     * "seller_id"   : "11",//商家端总ID
     * "drv_id"   : "11",//司导ID
     * "drv_code"   : "11",//司导code
     * "head_pic" : "http://xxx.jpg",//司导图片
     * "seller_name" : "司导姓名",
     * "score" : "1",//星级
     *}
     */
    public function getAllDriver(){
        $result = $this->driverLogic->get_driver_list();
        $this->ajaxReturn($result);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=DriverPack&a=getDriverDetail 司导详情
     * @apiName     getDriverDetail
     * @apiGroup    DriverPack
     * @apiParam    drv_id  {String}    司导ID
     * @apiSuccessExample   {json}  Success-Response
     * Http/1.1 200 OK
     *{
     *  "head_pic" : "http://xxx.jpg",//司导头像
     *  "putonghua" : "",//普通话
     *  "language" : "",//精通外语
     *  "putonghua" : "",//东京
     *  "putonghua" : "",//职业
     * }
     */
    public function getDriverDetail(){

    }
}