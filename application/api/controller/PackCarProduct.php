<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 18:35
 */

namespace app\api\controller;


use app\common\logic\PackCarProductLogic;
use think\Request;

class PackCarProduct extends Base{

    public function index(Request $request){

        if($request->isGet()){
            $id = input('id');
            if($id){
                return $this->getDetail($id);
            }
            return $this->getList($request);
        }
        if($request->isDelete()){
        }

        return $this->returnJson();
    }

    /**
     *
     * @api             {GET}   /api/packCarProduct   02.包车产品列表 ok wxx
     * @apiDescription  包车-按天包车-包车产品列表
     * @apiName         getList
     * @apiGroup        PackCarProduct
     * @apiParam  {Number} type         包车类型  1=接机  2=包车 3=送机.
     * @apiParam  {Number} [order_times]         预订次数 正序asc 反序desc.
     * @apiParam  {Number} [car_level]   舒适度.
     * @apiParam  {Number} [car_seat_num]   座位数.
     * @apiParam  {Number} [p=1]        页码.
     * @apiParam  {Number} [pageSize=20]   每页数据量.
     * @apiSuccess {Number} p          当前页码
     * @apiSuccess {Number} pageSize   列表每页显示行数
     * @apiSuccess {Number} totalRows  总行数
     * @apiSuccess {Number} totalPages 分页总页面数
     * @apiSuccess {Array}  list         列表.
     * @apiSuccess {Number} list.id             id.
     * @apiSuccess {Array}  list.imgs           图片.
     * @apiSuccess {String} list.title          标题.
     * @apiSuccess {Number} list.publishTime    发布时间戳.
     * @apiSuccess {String} list.publishTimeFmt 发布时格式化.
     * @apiSuccess {Number} list.price          单价.
     * @apiSuccess {String} list.priceFmt       单价格式化.
     * @apiSuccess {String} list.car_level       车的舒适度.
     * @apiSuccess {String} list.car_level_name       舒适度名称.
     * @apiSuccess {String} list.car_seat_num       车的座位数.
     *
     */
    private function getList(Request $request){
        $pcpLogic = new PackCarProductLogic();
        $type = input('type');
        $city = input('city');
        $car_level = input('car_level');
        $car_seat_num = input('car_seat_num');
        $order_times = input('order_times');
        if(!in_array($type, [PackCarProductLogic::TYPE_AIRPLANE_RECEIVE, PackCarProductLogic::TYPE_PACKCAR,PackCarProductLogic::TYPE_AIRPLANE_SEND])){
            return $this->returnJson(4002);
        }

        //print_r(is_null(input('city')));die;
        //print_r(isset());
        $where = [
            'is_show' => 1,
            'type' => $type
        ];
        if(!empty($city)){
            $where['full_cityname'] = ['like',"%{$city}%"];
        }
        if($car_level != ''){
            $where['car_level'] = $car_level;
        }
        if($car_seat_num != ''){
            $where['car_seat_num'] = $car_seat_num;
        }
        return $this->returnJson($pcpLogic->getPageByType($where,$order_times));
    }


    /**
     *
     * @api             {GET}   /api/packCarProduct   03.包车产品详情 ok wxx
     * @apiDescription  包车-按天包车-包车产品详情
     * @apiName         getDetail
     * @apiGroup        PackCarProduct
     * @apiParam  {Number} id        id.
     * @apiParam  {String} [token]  token.
     *
     * @apiSuccess {Number} id             id.
     * @apiSuccess {Array}  imgs           图片.
     * @apiSuccess {String} title          标题.
     * @apiSuccess {Number} publishTime    发布时间戳.
     * @apiSuccess {String} publishTimeFmt 发布时格式化.
     * @apiSuccess {Number} price          单价.
     * @apiSuccess {String} priceFmt       单价格式化.
     * @apiSuccess {Number} type           类型.
     * @apiSuccess {String} serviceCountryId       服务范围国家id.
     * @apiSuccess {String} serviceCountryName     服务范围国家名称.
     * @apiSuccess {String} serviceCityId        服务范围城市id.
     * @apiSuccess {String} serviceCityName      服务范围城市名称.
     * @apiSuccess {String} serviceMaxDistance    服务范围服务公里数.
     * @apiSuccess {String} serviceMaxPerson      服务范围最多接待人数.
     * @apiSuccess {String} serviceMaxTime       服务范围最长服务时间单位小时.
     * @apiSuccess {String} hasInsurance       是否有乘车险.
     * @apiSuccess {String} carSeatTotal       座位总数（含司）.
     * @apiSuccess {String} carSeatNum       行李空间空闲座位数.
     * @apiSuccess {String} carLuggageNum       行李空间行李数.
     * @apiSuccess {String} isAllowReturn       是否允许退订.
     * @apiSuccess {String} returnPolicy       退订政策.
     * @apiSuccess {String} hasChildSeat       是否有儿童座椅.
     * @apiSuccess {String} childSeatPrice       儿童座椅单价.
     * @apiSuccess {String} hasWheelChair       是否有轮椅.
     * @apiSuccess {String} wheelChairPrice       轮椅单价.
     * @apiSuccess {String} overtimePrice       超时加收价格.
     * @apiSuccess {String} overdistancePrice       超出公里加收价格.
     * @apiSuccess {String} remind                  当地人提醒.
     * @apiSuccess {number} isCollect           是否收藏.
     * @apiSuccess {number} isPraise            是否点赞.
     * @apiSuccess {String} flyName            机场名.
     * @apiSuccess {String} costStatement      费用说明.
     * @apiSuccess {String} costCompensation   补偿改退.
     * @apiSuccess {String} car_level   车的舒适度.
     * @apiSuccess {String} car_level_name   舒适度名称.
     * @apiSuccess {String} car_seat_num   车的座位数.
     */
    private function getDetail($id){
        $this->checkToken();
        $pcpLogic = new PackCarProductLogic();
        return $this->returnJson($pcpLogic->getDetailById($id, $this->user));
    }

}