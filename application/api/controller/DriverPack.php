<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 19:40
 */

namespace app\api\controller;

use app\api\logic\DataDicLogic;
use app\common\logic\DriverLogic;
use app\common\logic\PackCarProductLogic;
use app\common\logic\PackOrderLogic;
use app\common\logic\SellerLogic;
use app\common\logic\ConfigSetLogic;
use app\common\logic\PackLineLogic;

class DriverPack extends Base{

    public $driverLogic;

    public function __construct(){
        $this->driverLogic = new DriverLogic();
        parent::__construct();
    }

    /**
     * @api         {POST}   /index.php?m=Api&c=DriverPack&a=getAllDriver     得到全部司导done  管少秋
     * @apiName     getAllDriver
     * @apiGroup    DriverPack
     * @apiParam    {String}    [city]     通过city名得到司导
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     *{
     * "seller_id"   : "11",//商家端总ID
     * "drv_id"   : "11",//司导ID
     * "drv_code"   : "11",//司导code
     * "head_pic" : "http://xxx.jpg",//司导图片
     * "seller_name" : "司导姓名",
     * "star" : "1",//星级
     * "plat_start" : "1",//平台星级
     *}
     */
    public function getAllDriver(){
        $where = [];
        $where['is_driver'] = 1;
        $where['drv_id'] = ['<>', 0];
        $city = I('city');

        if(!empty($city)){
            $where['gps_name'] = ['like', "%{$city}%"];
        }
        $packLogic = new PackLineLogic();
        $result = $packLogic->get_all_drv($city);
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' => $result]);
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=DriverPack&a=getDriverDetail 司导详情 done 管少秋
     * @apiName     getDriverDetail
     * @apiGroup    DriverPack
     * @apiParam    seller_id  {String}    商家ID
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     * {
     * "status": 1,
     * "msg": "成功",
     * "result": {
     * "preson_info": {
     * "seller_id": 17,
     * "drv_id": 2,
     * "drv_code": "20170908-1",
     * "head_pic": null,
     * "seller_name": "少秋",
     * "briefing": null,
     * "country": null,
     * "putonghua": null,
     * "language": null,
     * "type_info": "店主-司导-房东"
     * },
     * "comment_info": {
     * "head_pic": null,
     * "nickname": "15151877660",
     * "start_time": 1504839306,
     * "star": 4,
     * "type": 1,
     * "content": "提前联系了很热情定了豪华车，够宽敞"
     * },
     * "photo_type": [],
     * "my_story": [],
     * "my_line": [
     * {
     * "line_id": 1,
     * "cover_img": null
     * },
     * {
     * "line_id": 5,
     * "cover_img": "http://ovwiqces1.bkt.clouddn.com/cee31c276bb2c1ee71391ac799ed78cc.png"
     * }
     * ],
     * "my_car": []
     * }
     * }
     */
    public function getDriverDetail(){
        $seller_id = I('seller_id/d', 0);
        //个人信息
        $person_info = $this->driverLogic->get_person_info($seller_id);
        //收到的评价
        $comment_info = $this->driverLogic->get_comment_info($seller_id);

        //我的相册
        $photo_type = $this->driverLogic->get_my_photo($seller_id);

        //我的故事
        $my_story = $this->driverLogic->get_my_story($seller_id);

        //我的路线
        $my_line = $this->driverLogic->get_my_line($seller_id);

        //我的车辆
        $my_car = $this->driverLogic->get_my_car($seller_id);

        $result = [
            'preson_info' => $person_info,
            'comment_info' => $comment_info,
            'photo_type' => $photo_type,
            'my_story' => $my_story,
            'my_line' => $my_line,
            'my_car' => $my_car,
        ];
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' => $result]);
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=DriverPack&a=rentCarByDay    按天包车游done  管少秋
     * @apiName     rentCarByDay
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {Number}    pcpid          包车产品id
     * @apiParam    {String}    start_address   出发地
     * @apiParam    {String}    end_address     目的地
     * @apiParam    {String}    pack_time       包车日期
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {String}    child_num       儿童乘客数
     * @apiParam    {String}    [drv_code]        指定司导
     * @apiParam    {Number}    [twenty_four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty_six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty_eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     * @apiParam    {String}    [remark]       备注
     */
    public function rentCarByDay(){
        $data = I('post.');
        $result = $this->validate($data, 'PackBase.rentCarByDay');
        if(empty($result)){
            return $this->returnJson(4003);
        }
        // 校验指定司导
        if(!empty($data['drv_code'])){

            $seller = SellerLogic::findByDrvCode($data['drv_code']);
            if(empty($seller) || empty($seller->is_driver)){
                return $this->returnJson(4004, '指定司导不存在。');
            }
            $data['seller_id'] = $seller['seller_id'];
        }

        $pcpLogic = new PackCarProductLogic();
        $pcp = $pcpLogic->find($data['pcpid']);
        if(empty($pcp)){
            return $this->returnJson(4004, '缺少参数pcpId');
        }
        if(!empty($pcp['full_cityname'])){
            $full_arr = explode('·',$pcp['full_cityname']);
            $data['country'] = $full_arr[1];
            $data['city'] = $full_arr[2];
        }
        //$data['car_type_id'] = $pcp['car_type_id'] ;
        $data['car_seat_num'] = $pcp['car_seat_num']; //车的总座位数
        $data['car_level'] = $pcp['car_level']; //车的舒适度
        $data['type'] = 6;
        $pack_arr = explode('|',$data['pack_time']);//包车数组
        if(!empty($pack_arr)){
            $data['real_price'] = $data['total_price'] = $pcp['price'] * count($pack_arr);
        }else{
            $data['real_price'] = $data['total_price'] = $pcp['price'];
        }

        //验证通过
        $data['start_time'] = $pack_arr[0];
        $data['status'] = 0;
        $base_id = $this->driverLogic->save_pack_base($data, $this->user);
        $saveData = [
            'base_id' => $base_id,
            'dest_address' => $data['end_address'],
            'pack_time' => $data['pack_time'],
        ];
        $result = $this->driverLogic->rent_car_by_day($saveData);
        if($result){
            $ret = ['id' => $base_id];
            return $this->returnJson(2000, '添加成功', $ret);
        }else{
            return $this->returnJson(5020, '添加失败');
        }

    }

    /**
     * @api         {POST}  /index.php?m=Api&c=DriverPack&a=receiveAirport    接机done 管少秋
     * @apiName     receiveAirport
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    （rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）
     * @apiParam    {Number}    pcpid          包车产品id
     * @apiParam    {String}    user_name       用户
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {String}    drv_code        指定司导
     * @apiParam    {Number}    is_have_pack    是否有行李0没有行李1有行李
     * @apiParam    {Number}    total_num       出行总人数
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {String}    child_num       儿童乘客数
     * @apiParam    {String}    remark          备注
     * @apiParam    {String}    flt_no          航班号
     * @apiParam    {String}    airport_name     机场名
     * @apiParam    {String}    end_address     送达地点
     * @apiParam    {String}    start_time       出发时间
     * @apiParam    {Number}    [twenty_four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty_six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty_eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     */
    public function receiveAirport(){
        $pcpLogic = new PackCarProductLogic();
        $data = I('post.');
        $pcpId = I('pcpid');
        // $result = $this->validate($data, 'PackBase.receiveAirport');
        // if($result){
        //     $this->ajaxReturn(['status' => -1, 'msg' => $result]);
        // }
        //验证通过
        // 校验指定司导
        if(!empty($data['drv_code'])){
            $seller = SellerLogic::findByDrvCode($data['drv_code']);
            if(empty($seller) || empty($seller->is_driver)){
                return $this->returnJson(4004, '指定司导不存在。');
            }
            $data['seller_id'] = $seller['seller_id'];
        }
        $pcp = $pcpLogic->find($pcpId);
        if(empty($pcp)){
            return $this->returnJson(4004, '缺少参数pcpId');
        }
        if(!empty($pcp['full_cityname'])){
            $full_arr = explode('·',$pcp['full_cityname']);
            $data['country'] = $full_arr[1];
            $data['city'] = $full_arr[2];
        }
        $data['real_price'] = $data['total_price'] = $pcp['price'];
        //$data['car_type_id'] = $pcp['car_type_id'] ;
        $data['car_seat_num'] = $pcp['car_seat_num']; // 座位数
        $data['car_level'] = $pcp['car_level']; //车的舒适度
        $data['start_address'] = $data['airport_name'];
        $data['status'] = PackOrderLogic::STATUS_UNPAY;
        $data['type'] = 1;
        $base_id = $this->driverLogic->save_pack_base($data, $this->user);
        $saveData = [
            'base_id' => $base_id,
            'flt_no' => $data['flt_no'],
            'airport_name' => $data['airport_name'],
            'dest_address' => $data['dest_address'],
            'start_time' => $data['start_time'],
        ];
        $result = $this->driverLogic->receive_airport($saveData);
        if($result){
            $ret = ['id' => $base_id];
            return $this->returnJson(2000, '添加成功', $ret);
        }else{
            return $this->returnJson(5020, '添加失败');
        }
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=DriverPack&a=sendAirport    送机done 管少秋
     * @apiName     sendAirport
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    （rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）
     * @apiParam    {Number}    pcpid          包车产品id
     * @apiParam    {String}    user_name       用户
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
     * @apiParam    {String}    start_time          出发时间
     * @apiParam    {String}    end_time            结束时间
     * @apiParam    {Number}    [twenty_four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty_six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty_eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     */
    public function sendAirport(){
        $data = I('post.');
        $result = $this->validate($data, 'PackBase.sendAirport');
        if(empty($result)){
            return $this->returnJson(4003);
        }
        $pcpLogic = new PackCarProductLogic();
        $pcp = $pcpLogic->find($data['pcpid']);
        if(empty($pcp)){
            return $this->returnJson(4004, '缺少参数pcpId');
        }
        if(!empty($pcp['full_cityname'])){
            $full_arr = explode('·',$pcp['full_cityname']);
            $data['country'] = $full_arr[1];
            $data['city'] = $full_arr[2];
        }
        // 校验指定司导
        if(!empty($data['drv_code'])){
            $seller = SellerLogic::findByDrvCode($data['drv_code']);
            if(empty($seller) || empty($seller->is_driver)){
                return $this->returnJson(4004, '指定司导不存在。');
            }
            $data['seller_id'] = $seller['seller_id'];
        }

        $data['real_price'] = $data['total_price'] = $pcp['price'];
        //$data['car_type_id'] = $pcp['car_type_id'] ;
        $data['car_seat_num'] = $pcp['car_seat_num']; // 座位数
        $data['car_level'] = $pcp['car_level']; //车的舒适度
        $data['end_address'] = $data['airport_name'];
        $data['status'] = PackOrderLogic::STATUS_UNPAY;
        $data['type'] = 2;
        $base_id = $this->driverLogic->save_pack_base($data, $this->user);
        $saveData = [
            'base_id' => $base_id,
            'flt_no' => $data['flt_no'],
            'airport_name' => $data['airport_name'],
            'start_address' => $data['start_address'],
            'start_time' => $data['start_time'],
        ];
        $result = $this->driverLogic->send_airport($saveData);
        if($result){
            $ret = ['id' => $base_id];
            return $this->returnJson(2000, '添加成功', $ret);
        }else{
            return $this->returnJson(5020, '添加失败');
        }
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=DriverPack&a=oncePickup    单次接送done 管少秋
     * @apiName     oncePickup
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    （rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）
     * @apiParam    {String}    user_name       用户
     * @apiParam    {String}    car_seat_num    需求座位数
     * @apiParam    {String}    car_level    舒适度
     * @apiParam    {String}    connect         联系方式
     * @apiParam    {String}    [drv_code]        指定司导
     * @apiParam    {Number}    is_have_pack    是否有行李0没有行李1有行李
     * @apiParam    {Number}    total_num       出行总人数
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {String}    child_num       儿童乘客数
     * @apiParam    {String}    remark       备注
     * @apiParam    {String}    start_address    起始地地址
     * @apiParam    {String}    end_address       目的地地址
     * @apiParam    {String}    user_car_time     用车时间
     * @apiParam    {String}    [country]     出行国家 后台筛选订单用
     * @apiParam    {String}    [city]        出行城市 后台筛选订单用
     * @apiParam    {Number}    [twenty_four]     24行李箱尺寸
     * @apiParam    {Number}    [twenty_six]      26行李箱尺寸
     * @apiParam    {Number}    [twenty_eight]     28行李箱尺寸
     * @apiParam    {Number}    [thirty]     30行李箱尺寸
     */
    public function oncePickup(){
        $data = I('post.');
        $result = $this->validate($data, 'PackBase.oncePickup');
        if(empty($result)){
            return $this->returnJson(4003);
        }
        // 校验指定司导
        if(!empty($data['drv_code'])){
            $seller = SellerLogic::findByDrvCode($data['drv_code']);
            if(empty($seller) || empty($seller->is_driver)){
                return $this->returnJson(4004, '指定司导不存在。');
            }
            //$data['allot_seller_id'] = $seller['seller_id'];
            $data['seller_id'] = $seller['seller_id'];

        }
        //验证通过
        $data['start_time'] = $data['user_car_time'];
        $data['status'] = PackOrderLogic::STATUS_UNCONFIRM;
        $data['type'] = 4;
        $base_id = $this->driverLogic->save_pack_base($data, $this->user);
        $saveData = [
            'base_id' => $base_id,
            'start_address' => $data['start_address'],
            'dest_address' => $data['end_address'],
            'user_car_time' => $data['user_car_time'],
        ];
        $result = $this->driverLogic->once_pickup($saveData);
        if($result){
            $ret = ['id' => $base_id];
            return $this->returnJson(2000, '添加成功', $ret);
        }else{
            return $this->returnJson(5020, '添加失败');
        }
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=DriverPack&a=privateMake    私人定制用户提交 done 管少秋
     * @apiName     privateMake
     * @apiGroup    DriverPack
     * @apiParam    {String}    token   token.
     * @apiParam    {Number}    adult_num       成人乘客数
     * @apiParam    {Number}    child_num       儿童乘客数
     * @apiParam    {String}    tour_time       出行时间
     * @apiParam    {String}    start_address   出发地
     * @apiParam    {String}    end_address     目的地
     * @apiParam    {String}    tour_days       游玩天数
     * @apiParam    {String}    tour_person_num       游玩人数
     * @apiParam    {String}    [country]     出行国家 后台筛选订单用
     * @apiParam    {String}    [city]        出行城市 后台筛选订单用
     * @apiParam    {String}    [tour_favorite]       出行偏好
     * @apiParam    {String}    [recommend_diner]       推荐餐馆
     * @apiParam    {String}    [recommend_sleep]       推荐住宿
     * @apiSuccess  {Number}    id  订单id
     */
    public function privateMake(){
        $data = I('post.');
        $result = $this->validate($data, 'PackBase.privateMake');
        if($result !== true){
            return $this->returnJson(4003,$result);
        }
        // 校验指定司导
        if(!empty($data['drv_code'])){
            $seller = SellerLogic::findByDrvCode($data['drv_code']);
            if(empty($seller) || empty($seller->is_driver)){
                return $this->returnJson(4004, '指定司导不存在。');
            }
            $data['seller_id'] = $seller['seller_id'];
            //$data['allot_seller_id'] = $seller['seller_id'];
            //pushMessage('客人指定司导', '您有一条新订单，请及时处理', $seller['device_no'], $seller['seller_id'], 1);
            //pushMessage('客人指定司导', '您有一条新订单，请及时处理', $seller['device_no'], $seller['seller_id'], 1);
        }
        //验证通过
        $data['start_time'] = $data['tour_time'];
        $data['order_day'] = intval($data['tour_days']);
        $data['eating_ave'] = $data['recommend_diner'];
        $data['stay_ave'] = $data['recommend_sleep'];
        $data['use_car_adult'] = intval($data['adult_num']);
        $data['use_car_children'] = intval($data['child_num']);
        $data['type'] = 5;
        $data['status'] = -3;//-3=私人定制用户提交
        $base_id = $this->driverLogic->save_pack_base($data, $this->user);
        $saveData = [
            'base_id' => $base_id,
            'end_address' => $data['end_address'],
            'tour_days' => intval($data['tour_days']),
            'tour_time' => $data['tour_time'],
            'tour_person_num' => intval($data['tour_person_num']),
            'tour_favorite' => $data['tour_favorite'],
            'recommend_diner' => $data['recommend_diner'],
            'recommend_sleep' => $data['recommend_sleep'],
        ];
        $result = $this->driverLogic->private_person($saveData);
        if($result){
            $ret = ['id' => $base_id];
            return $this->returnJson(2000, '添加成功', $ret);
        }else{
            return $this->returnJson(5020, '添加失败');
        }
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=DriverPack&a=getConfig    得到私人定制的配置 FIXME 管少秋
     * @apiName     getConfig
     * @apiGroup    DriverPack
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     * {
     * "status": 1,
     * "msg": "成功",
     * "result": {
     * "trip_choose": {    //出行偏好
     * {"id": 1,
     * "description": '吃的地方多一点'},//出行偏好名称
     * {"id": 2,
     * "description": '玩的地方多一点'},//出行偏好名称
     * },
     * "restaurant_choose": {    //餐馆
     * {"id": 1,
     * "description": '北海道大饭店'},//出行偏好名称
     * {"id": 2,
     * "description": '东京热大饭店'},//出行偏好名称
     * },
     * "sleep_choose": {    //个人信息
     * {"id": 1,
     * "description": '札幌大酒店'},//出行偏好名称
     * {"id": 2,
     * "description": '土耳其大宾馆'},//出行偏好名称
     * },
     * }
     * }
     */
    public function getConfig(){
        //得到出行设置
        $config = new ConfigSetLogic();
        //1出行偏好2推荐餐饮3推荐住宿
        //print_r($trip_choose);die;
        $result = [
            'trip_choose' => DataDicLogic::getDataDic(DataDicLogic::TYPE_TRIP),
            'restaurant_choose' => DataDicLogic::getDataDic(DataDicLogic::TYPE_RESTAURANT),
            'sleep_choose' => DataDicLogic::getDataDic(DataDicLogic::TYPE_ROOM),
        ];
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' => $result]);
    }

    /**
     * @api             {POST}  /index.php?m=Api&c=DriverPack&a=searchDriver    搜索司导done 管少秋
     * @apiDescription  按照 seller_name|drv_code|nickname 模糊搜索
     * @apiName         searchDriver
     * @apiGroup        DriverPack
     * @apiParam    {String}    search  搜索字符
     * @apiSuccessExample {json}    Success-Response
     * {
     * "status": 1,
     * "msg": "成功",
     * "result": [
     * {
     * "seller_id": 17,
     * "head_pic": null,
     * "seller_name": "少秋",
     * "drv_code": "20170908-1",
     * "province": "朝阳门街道",
     * "city": "建国门街道",
     * "plat_start": null,
     * "star": 4,
     * "line": "我的标题很长很长可以用100年"
     * }
     * ]
     * }
     */
    public function searchDriver(){

        $search = I('search');
        if(empty($search)){
            $this->ajaxReturn(['status' => -1, 'msg' => '请输入搜索词']);
        }
        $where = [
            'seller_name|drv_code|nickname' => ['like', "%{$search}%"],
            'is_driver' => 1
        ];
        $result = $this->driverLogic->search_driver($where);
        $this->ajaxReturn($result);
    }

    /**
     * @api             {GET}  /index.php?m=Api&c=DriverPack&a=findDriver    精确查找司导 ok wxx
     * @apiDescription  按照 手机号|工号 精确查找
     * @apiName         findDriver
     * @apiGroup        DriverPack
     * @apiParam    {String}    search  搜索字符
     * @apiSuccessExample {json}    Success-Response
     * {
     *  status: -1,
     *  msg: "成功",
     *  result:  {
     *      seller_id: 48,
     *      head_pic: "http://img.shahaizi.cn/4804b6d4afbaf7229635c2542cf8c07b.jpeg",
     *      seller_name: "",
     *      drv_code: "20170925-48",
     *      province: null,
     *      city: null,
     *      plat_start: "4",
     *      star: null,
     *      line: "地狱城一人一游"
     *  }
     * }
     */
    public function findDriver(){
        $search = I('search');
        if(empty($search)){
            $this->ajaxReturn(['status' => -1, 'msg' => '请输入搜索词']);
        }
        $where = [
            'mobile|drv_code' => $search,
            'is_driver' => 1
        ];
        $driverInfo = $this->driverLogic->find_driver($where);
        return $this->returnJson($driverInfo);
    }


    /**
     * @api     {POST}  /index.php?m=Api&c=DriverPack&a=saveUserPrivate   保存用户私人定制信息
     * @apiName     saveUserPrivate
     * @apiGroup     DriverPack
     * @apiParam    {Number}    air_id      订单ID
     * @apiParam    {String}    customer_name      顾客名称
     * @apiParam    {String}    customer_phone     顾客手机
     * @apiParam    {String}    [user_passport]      护照号
     * @apiParam    {String}    [user_identity]      用户的身份证
     * @apiParam    {String}    [twenty_four]      24寸行李箱
     * @apiParam    {String}    [twenty_six]       26寸行李箱
     * @apiParam    {String}    [twenty_eight]     28寸行李箱
     * @apiParam    {String}    [thirty]      30寸行李箱
     */
    public function saveUserPrivate(){
        $reqParams = $this->getReqParams(['air_id','customer_name', 'customer_phone', 'user_passport', 'user_identity', 'twenty_four', 'twenty_six', 'twenty_eight', 'thirty']);
        $rule = [
            'air_id' => 'require',
            'customer_name' => 'require',
            'customer_phone' => 'require',
        ];
        $where = [
            'air_id' => $reqParams['air_id']
        ];
        $this->validateParams($reqParams, $rule);
        /*if(!check_mobile($reqParams['description'])){
            $this->returnJson(['status'=>-1,'msg'=>'不是合法的手机号']);
        }*/
        $reqParams['status'] = 0;//改为未支付
        $result = $this->driverLogic->update_private($where,$reqParams);
        return $this->ajaxReturn($result);
    }

    /**
     * @api     {GET}   /index.php?m=Api&c=DriverPack&a=getPrivateDetail     得到私人定制的行程详情
     * @apiName     getPrivateDetail
     * @apiGroup    DriverPack
     * @apiParam    {Number}    air_id      订单ID
     * @apiParam    {String}    token       token
     */
    public function getPrivateDetail(){
        $air_id = I('air_id');
        $result = $this->driverLogic->get_private_detail($air_id);
        return $this->ajaxReturn($result);
    }
}