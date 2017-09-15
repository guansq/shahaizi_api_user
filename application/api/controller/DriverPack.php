<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 19:40
 */
namespace app\api\controller;
use app\common\logic\DriverLogic;
use app\api\validate\PackBase;

class DriverPack extends Base{

    public $driverLogic;

    public function __construct(){
        $this->driverLogic = new DriverLogic();
        parent::__construct();
    }

    /**
     * @api {GET}   /index.php?m=Api&c=DriverPack&a=getAllDriver     得到全部司导done  管少秋
     * @apiName     getAllDriver
     * @apiGroup    DriverPack
     * @apiParam    {Number}    [partner_num]     伴侣人数
     * @apiParam    {Number}    [dest_address]     目的地
     * @apiParam    {Number}    [date]     日期
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
        $partner = I('partner_num');
        $dest_address = I('dest_address');
        $date = I('date');
        $city = I('dest_address');
        $where = [];
        $where['is_driver'] = 1;
        $where['drv_id'] = ['<>',0];
        if(!empty($partner)){
            $map = [
                1 => 4,
                2 => 4,
                3 => 4,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9,
                10 => 10,
            ];
            $num = $map[$partner];//人数座位对应表->取满足的drv_id
        }

        if(!empty($dest_address)){
            $where['city'] = ['like',"{$city}"];
        }

        if(!empty($date)){

        }
        $result = $this->driverLogic->get_driver_list();
        $this->ajaxReturn($result);

    }

    /**
     * @api {GET}   /index.php?m=Api&c=DriverPack&a=getDriverDetail 司导详情 (待完成) 管少秋
     * @apiName     getDriverDetail
     * @apiGroup    DriverPack
     * @apiParam    seller_id  {String}    商家ID
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
    {
    "status": 1,
    "msg": "成功",
        "result": {
            "preson_info": {    //个人信息
            "seller_id": 17,//商家ID
            "drv_id": 2,//司导ID
            "drv_code": "20170908-1",//司导code
            "head_pic": null,//头像
            "seller_name": "少秋",//名称
            "briefing": null,//简介
            "country": null,//家乡
            "putonghua": null,//普通话
            "language": null,//外语
            "type_info": "店主-司导-房东"//职业
            }
        }
    }
     */
    public function getDriverDetail(){
        $seller_id = I('seller_id/d',0);
        //个人信息
        $person_info = $this->driverLogic->get_person_info($seller_id);
        //收到的评价
        $comment_info = $this->driverLogic->get_comment_info($seller_id);

        //我的相册
        $photo_type = $this->driverLogic->get_my_photo($seller_id);

        //我的故事
        $my_story = $this->driverLogic->get_my_story();

        //我的路线
        $this->driverLogic->get_my_line();

        //我的车辆
        $this->driverLogic->get_my_car();

        $result = [
            'preson_info' => $person_info,
            'comment_info' => $comment_info,
            'photo_type' => $photo_type,
            'my_story' => $my_story,
        ];
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {POST}  /index.php?m=Api&c=DriverPack&a=rentCarByDay    按天包车游done  管少秋
     * @apiName     rentCarByDay
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    （rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）
     * @apiParam    {String}    user_name       用户
     * @apiParam    {String}    car_type_id     车型ID
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {String}    [drv_code]        指定司导
     * @apiParam    {Number}    is_have_pack    是否有行李0没有行李1有行李
     * @apiParam    {Number}    total_num       出行总人数
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {String}    child_num       儿童乘客数
     * @apiParam    {String}    remark       备注
     * @apiParam    {String}    dest_address    目的地地址
     * @apiParam    {String}    pack_time       包车日期
     * @apiParam    {Number}    [twenty-four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty-six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty-eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     */
    public function rentCarByDay(){
        $data = I('post.');
        $result = $this->validate($data,'PackBase.rentCarByDay');
        if($result === true){//验证通过
            $base_id = $this->driverLogic->save_pack_base($data,$this->user);
            $saveData = [
                'base_id' => $base_id,
                'dest_address' => $data['dest_address'],
                'pack_time' => $data['pack_time'],
            ];
            $result = $this->driverLogic->rent_car_by_day($saveData);
            if($result){
                $this->ajaxReturn(['status'=>1,'msg'=>'添加成功']);
            }else{
                $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
            }
        }else{
            $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
        }

    }

    /**
     * @api {POST}  /index.php?m=Api&c=DriverPack&a=receiveAirport    接机done 管少秋
     * @apiName     receiveAirport
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    （rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）
     * @apiParam    {String}    user_name       用户
     * @apiParam    {String}    car_type_id     车型ID
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {String}    drv_code        指定司导
     * @apiParam    {Number}    is_have_pack    是否有行李0没有行李1有行李
     * @apiParam    {Number}    total_num       出行总人数
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {String}    child_num       儿童乘客数
     * @apiParam    {String}    remark       备注
     * @apiParam    {String}    flt_no    航班号
     * @apiParam    {String}    airport_name       机场名
     * @apiParam    {String}    dest_address       送达地点
     * @apiParam    {String}    start_time       出发时间
     * @apiParam    {Number}    [twenty-four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty-six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty-eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     */
    public function receiveAirport(){
        $data = I('post.');
        $result = $this->validate($data,'PackBase.receiveAirport');
        if($result === true){//验证通过
            $base_id = $this->driverLogic->save_pack_base($data,$this->user);
            $saveData = [
                'base_id' => $base_id,
                'flt_no' => $data['flt_no'],
                'airport_name' => $data['airport_name'],
                'dest_address' => $data['dest_address'],
                'start_time' => $data['start_time'],
            ];
            $result = $this->driverLogic->receive_airport($saveData);
            if($result){
                $this->ajaxReturn(['status'=>1,'msg'=>'添加成功']);
            }else{
                $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
            }
        }else{
            $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
        }
    }

    /**
     * @api {POST}  /index.php?m=Api&c=DriverPack&a=sendAirport    送机done 管少秋
     * @apiName     sendAirport
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    （rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）
     * @apiParam    {String}    user_name       用户
     * @apiParam    {String}    car_type_id     车型ID
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {String}    drv_code        指定司导
     * @apiParam    {Number}    is_have_pack    是否有行李0没有行李1有行李
     * @apiParam    {Number}    total_num       出行总人数
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {String}    child_num       儿童乘客数
     * @apiParam    {String}    remark       备注
     * @apiParam    {String}    flt_no    航班号
     * @apiParam    {String}    airport_name       机场名
     * @apiParam    {String}    start_address       出发地点
     * @apiParam    {String}    start_time       出发时间
     * @apiParam    {Number}    [twenty-four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty-six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty-eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     */
    public function sendAirport(){
        $data = I('post.');
        $result = $this->validate($data,'PackBase.sendAirport');
        if($result === true){//验证通过
            $base_id = $this->driverLogic->save_pack_base($data,$this->user);
            $saveData = [
                'base_id' => $base_id,
                'flt_no' => $data['flt_no'],
                'airport_name' => $data['airport_name'],
                'start_address' => $data['start_address'],
                'start_time' => $data['start_time'],
            ];
            $result = $this->driverLogic->send_airport($saveData);
            if($result){
                $this->ajaxReturn(['status'=>1,'msg'=>'添加成功']);
            }else{
                $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
            }
        }else{
            $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
        }
    }

    /**
     * @api {POST}  /index.php?m=Api&c=DriverPack&a=oncePickup    单次接送done 管少秋
     * @apiName     oncePickup
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    （rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）
     * @apiParam    {String}    user_name       用户
     * @apiParam    {String}    car_type_id     车型ID
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {String}    drv_code        指定司导
     * @apiParam    {Number}    is_have_pack    是否有行李0没有行李1有行李
     * @apiParam    {Number}    total_num       出行总人数
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {String}    child_num       儿童乘客数
     * @apiParam    {String}    remark       备注
     * @apiParam    {String}    start_address    起始地地址
     * @apiParam    {String}    dest_address       目的地地址
     * @apiParam    {String}    user_car_time     用车时间
     * @apiParam    {Number}    [twenty-four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty-six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty-eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     */
    public function oncePickup(){
        $data = I('post.');
        $result = $this->validate($data,'PackBase.oncePickup');
        if($result === true){//验证通过
            $base_id = $this->driverLogic->save_pack_base($data,$this->user);
            $saveData = [
                'base_id' => $base_id,
                'start_address' => $data['start_address'],
                'dest_address' => $data['dest_address'],
                'user_car_time' => $data['user_car_time'],
            ];
            $result = $this->driverLogic->once_pickup($saveData);
            if($result){
                $this->ajaxReturn(['status'=>1,'msg'=>'添加成功']);
            }else{
                $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
            }
        }else{
            $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
        }
    }

    /**
     * @api {POST}  /index.php?m=Api&c=DriverPack&a=privateMake    私人定制done 管少秋
     * @apiName     privateMake
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    （rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）
     * @apiParam    {String}    user_name       用户
     * @apiParam    {String}    car_type_id     车型ID
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {String}    drv_code        指定司导
     * @apiParam    {Number}    is_have_pack    是否有行李0没有行李1有行李
     * @apiParam    {Number}    total_num       出行总人数
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {String}    child_num       儿童乘客数
     * @apiParam    {String}    tour_time       出行时间
     * @apiParam    {String}    end_address     目的地
     * @apiParam    {String}    tour_days       游玩天数
     * @apiParam    {String}    tour_person_num       游玩人数
     * @apiParam    {String}    tour_favorite       出行偏好
     * @apiParam    {String}    recommend_diner       推荐餐馆
     * @apiParam    {String}    recommend_sleep       推荐住宿
     * @apiParam    {Number}    [twenty-four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty-six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty-eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     */
    public function privateMake(){
        $data = I('post.');
        $result = $this->validate($data,'PackBase.privateMake');
        if($result === true){//验证通过
            $base_id = $this->driverLogic->save_pack_base($data,$this->user);
            $saveData = [
                'base_id' => $base_id,
                'end_address' => $data['end_address'],
                'tour_days' => $data['tour_days'],
                'tour_person_num' => $data['tour_person_num'],
                'tour_favorite' => $data['tour_favorite'],
                'recommend_diner' => $data['recommend_diner'],
                'recommend_sleep' => $data['recommend_sleep'],
            ];
            $result = $this->driverLogic->private_person($saveData);
            if($result){
                $this->ajaxReturn(['status'=>1,'msg'=>'添加成功']);
            }else{
                $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
            }
        }else{
            $this->ajaxReturn(['status'=>-1,'msg'=>$result]);
        }
    }
}