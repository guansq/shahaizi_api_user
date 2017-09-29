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
     * @api             {GET}   /api/packCarProduct   02.包车产品列表 fixme wxx
     * @apiDescription  包车-按天包车-包车产品列表
     * @apiName         getList
     * @apiGroup        PackCarProduct
     * @apiParam  {Number} type         包车类型 1=接送机  2=包车.
     * @apiParam  {Number} [p=1]        页码.
     * @apiParam  {Number} [pageSize=20]   每页数据量.
     *
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
     *
     */
    private function getList(Request $request){
        $pcpLogic = new PackCarProductLogic();
        $type = input('type');
        if(!in_array($type,[PackCarProductLogic::TYPE_AIRPLANE,PackCarProductLogic::TYPE_PACKCAR])){
            return $this->returnJson(4002);
        }
        return $this->returnJson($pcpLogic->getPageByType($type));
    }


    /**
     *
     * @api             {GET}   /api/packCarProduct   03.包车产品详情 fixme wxx
     * @apiDescription  包车-按天包车-包车产品详情
     * @apiName         getDetail
     * @apiGroup        PackCarProduct
     * @apiParam  {Number} id        id.
     *
     * @apiSuccess {Number} id             id.
     * @apiSuccess {Array}  imgs           图片.
     * @apiSuccess {String} title          标题.
     * @apiSuccess {Number} publishTime    发布时间戳.
     * @apiSuccess {String} publishTimeFmt 发布时格式化.
     * @apiSuccess {Number} price          单价.
     * @apiSuccess {String} priceFmt       单价格式化.
     * @apiSuccess {Number} type            单价格式化.
     * @apiSuccess {String} title           单价格式化.
     * @apiSuccess {String} img             单价格式化.
     * @apiSuccess {String} price           单价格式化.
     * @apiSuccess {String} service_country_id       单价格式化.
     * @apiSuccess {String} service_country_name       单价格式化.
     * @apiSuccess {String} service_city_id       单价格式化.
     * @apiSuccess {String} service_city_name       单价格式化.
     * @apiSuccess {String} service_max_distance       单价格式化.
     * @apiSuccess {String} service_max_person       单价格式化.
     * @apiSuccess {String} service_max_time       单价格式化.
     * @apiSuccess {String} has_insurance       单价格式化.
     * @apiSuccess {String} car_type_id       单价格式化.
     * @apiSuccess {String} car_type_name       单价格式化.
     * @apiSuccess {String} car_seat_total       单价格式化.
     * @apiSuccess {String} car_seat_num       单价格式化.
     * @apiSuccess {String} car_luggage_num       单价格式化.
     * @apiSuccess {String} is_allow_return       单价格式化.
     * @apiSuccess {String} return_policy       单价格式化.
     * @apiSuccess {String} has_child_seat       单价格式化.
     * @apiSuccess {String} child_seat_price       单价格式化.
     * @apiSuccess {String} has_wheel_chair       单价格式化.
     * @apiSuccess {String} wheel_chair_price       单价格式化.
     * @apiSuccess {String} overtime_price       单价格式化.
     * @apiSuccess {String} overdistance_price       单价格式化.
     * @apiSuccess {String} remind       单价格式化.
     */
    private function getDetail($id){
    }

}