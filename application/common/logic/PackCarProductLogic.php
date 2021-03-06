<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

use ruitu\PageVo;
use think\Page;

class PackCarProductLogic extends BaseLogic{

    protected $table = 'ruit_pack_car_product';

    const TYPE_AIRPLANE_RECEIVE = 1;  //接送机
    const TYPE_AIRPLANE_SEND    = 3;  // 包车
    const TYPE_PACKCAR          = 2;  // 包车


    public function getPageByType($where,$order_times){
        $total = $this->where($where)->count();

        $page = new Page($total);
        if(empty($total)){
            //return resultArray(4004);
        }
        $fields = [
            'id',
            'publish_time' => 'publishTime',
            'price',
            'title',
            'img' => 'imgs',
            'car_level',
            'car_seat_num',
        ];
        if(empty($order_times)){
            $list = $this->where($where)
                ->field($fields)
                ->order("sort asc")
                ->limit($page->firstRow, $page->listRows)
                ->select();
        }else{
            $list = $this->where($where)
                ->field($fields)
                ->order("order_times $order_times")
                ->limit($page->firstRow, $page->listRows)
                ->select();
        }

        foreach($list as &$item){
            $item['publishTimeFmt'] = date('Y-m-d', $item['publishTime']);
            $item['priceFmt'] = moneyFormat($item['price']);
            $item['imgs'] = explode('|', $item['imgs']);
            $item['car_level_name'] = PackCarInfoLogic::LEVEL_ARR[$item['car_level']];
        }
        $pageVo = new PageVo($page, $list);
        return resultArray(2000, '', $pageVo);
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe:
     * @param       $id
     * @param array $user
     */
    public function getDetailById($id, $user = []){
        $userPraiceLogic = new UserPraiseLogic();
        $userCollLogic = new UserCollectLogic();
        $ordCommLogic = new OrderCommentLogic();

        $fields = [
            'id',
            'publish_time' => 'publishTime',
            'price',
            'type',
            'title',
            'img' => 'imgs',
            'service_country_id' => 'serviceCountryId',              //服务范围国家id',
            //'service_country_name' => 'serviceCountryName',                //服务范围国家名称',
            'service_city_id' => 'serviceCityId',               //服务范围城市id',
            //'service_city_name' => 'serviceCityName',             //服务范围城市名称',
            'service_max_distance' => 'serviceMaxDistance',             //服务范围服务公里数',
            'service_max_person' => 'serviceMaxPerson',               //服务范围最多接待人数',
            'service_max_time' => 'serviceMaxTime',              //服务范围最长服务时间单位小时',
            'has_insurance' => 'hasInsurance',               //是否有乘车险',
            //'car_type_id' => 'carTypeId',                //车辆类型id',
            //'car_type_name' => 'carTypeName',                //车辆类型名称',
            //'car_seat_total' => 'carSeatTotal',              //座位总数',
            'car_seat_num' => 'carSeatNum',              //行李空间空闲座位数',
            'car_luggage_num' => 'carLuggageNum',                //行李空间行李数',
            'is_allow_return' => 'isAllowReturn',                //是否允许退订',
            'return_policy' => 'returnPolicy',               //退订政策',
            'has_child_seat' => 'hasChildSeat',              //是否有儿童座椅',
            'child_seat_price' => 'childSeatPrice',              //儿童座椅单价',
            'has_wheel_chair' => 'hasWheelChair',                //是否有轮椅',
            'wheel_chair_price' => 'wheelChairPrice',                //轮椅单价',
            'overtime_price' => 'overtimePrice',             //超时加收价格',
            'overdistance_price' => 'overdistancePrice',             //超出公里加收价格',
            'remind' => 'remind',             //当地人提醒',
            'fly_name' => 'flyName',             //机场名',
            'cost_statement' => 'costStatement',             //    费用说明.
            'cost_compensation' => 'costCompensation',             // 补偿改退.
            'car_level',             // 舒适度.
            'car_seat_num',             // 座位数.
            'full_cityname',             // 城市.
        ];
        $pcar = $this->field($fields)->find($id);
        if(empty($pcar)){
            return resultArray(4004);
        }
        $map = [
            'cover_img_k' => '宽松',
            'cover_img_z' => '中等',
            'cover_img_y' => '严格',
            'cover_img_n' => '不退订',
        ];
        $pcar = $pcar->toArray();
        $pcar['costStatement'] = $pcar['costStatement'];
        $pcar['costCompensationLevel'] = $map[explode('###',$pcar['costCompensation'])[0]];
        $pcar['costCompensation'] = htmlspecialchars_decode(explode('###',$pcar['costCompensation'])[1]);
        $pcar['publishTimeFmt'] = date('Y-m-d', $pcar['publishTime']);
        $pcar['priceFmt'] = moneyFormat($pcar['price']);
        $pcar['childSeatPriceFmt'] = moneyFormat($pcar['childSeatPrice']);
        $pcar['wheelChairPriceFmt'] = moneyFormat($pcar['wheelChairPrice']);
        $pcar['overtimePriceFmt'] = moneyFormat($pcar['overtimePrice']);
        $pcar['overdistancePriceFmt'] = moneyFormat($pcar['overdistancePrice']);
        $pcar['imgs'] = explode('|', $pcar['imgs']);

        $pcar['hasWheelChairFmt'] = empty($pcar['hasWheelChair']) ? '无' : '有';
        $pcar['hasInsuranceFmt'] = empty($pcar['hasInsurance']) ? '无' : '有';
        $pcar['isAllowReturnFmt'] = empty($pcar['isAllowReturn']) ? '不支持' : '支持';
        $pcar['isCollect'] = empty($user) ? 0 : $userCollLogic->isCollectPackCar($id, $user['user_id']);
        $pcar['isPraise'] = empty($user) ? 0 : $userPraiceLogic->isPraisePackCar($id, $user['user_id']);
        $pcar['orderCnt'] = $this->countOrder($id);
        $pcar['score'] = intval($ordCommLogic->where('car_product_id', $pcar['id'])->avg('pack_order_score'));
        $pcar['car_level_name'] = PackCarInfoLogic::LEVEL_ARR[$pcar['car_level']];
        $pcar['serviceCountryName'] = explode('·',$pcar['full_cityname'])[1];//国家名称
        $pcar['serviceCityName'] = explode('·',$pcar['full_cityname'])[2];//城市名称
        ksort($pcar);
        return resultArray(2000, '', $pcar);
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe: 统计下单数量
     * @param $id
     */
    public function countOrder($id){
        return PackOrderLogic::where('car_product_id', $id)->count();
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 我收藏的包车产品列表
     * @param $user_id
     */
    public function getCollectPage($user_id){

        $userColl = new  UserCollectLogic();

        $count = $userColl->where('user_id', $user_id)->where('model_type', UserCollectLogic::TYPE_PACKCAR)->count();
        if(empty($count)){
            return resultArray(4004);
        }

        $page = new Page($count);

        $ids = $userColl->where('user_id', $user_id)
            ->where('model_type', UserCollectLogic::TYPE_PACKCAR)
            ->limit($page->firstRow, $page->listRows)
            ->order('add_time DESC')
            ->column('goods_id');

        $fields = [
            'id',
            'type',
            'publish_time' => 'publishTime',
            'price',
            'title',
            'img' => 'imgs',
        ];

        $list = $this->where('id', ['IN', $ids])->order('sort , create_at DESC')->field($fields)->select();

        foreach($list as &$item){
            $item['imgs'] = explode('|', $item['imgs']);
            $item['priceFmt'] = moneyFormat($item['price']);
            $item['publishTimeFmt'] = date('Y-m-d', $item['publishTime']);
        }

        $ret = [
            'p' => $page->nowPage,
            'totalPages' => $page->totalPages,
            'list' => $list,
        ];
        return resultArray(2000, '', $ret);
    }

}