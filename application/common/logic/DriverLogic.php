<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/8
 * Time: 12:40
 */

namespace app\common\logic;

use think\Model;
use think\Page;

class DriverLogic extends BaseLogic{


    /*
     * 得到司导列表
     */
    public function get_driver_list($where){

        $count = M('seller')->where($where)->count();
        $Page = new Page($count, 10);
        $list = M('seller')
            ->field('seller_id,drv_id,drv_code,head_pic,seller_name,plat_start')
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        if(empty($list)){
            return ['status' => -1, 'msg' => '没有记录'];
        }
        //去计算评分
        foreach($list as &$val){
            $star = M('pack_comment')->where('seller_id', $val['seller_id'])->avg('star');
            $val['star'] = $star;
        }
        $result = [
            'totalPages' => $Page->totalPages,
            'list' => $list
        ];
        return [
            'status' => 1,
            'msg' => '成功',
            'result' => $result
        ];
    }

    /*
     * 得到司导个人信息
     */
    public function get_person_info($seller_id){
        $where = ['seller_id' => $seller_id];
        $info = M('seller')
            ->field('seller_id,cover_img,drv_id,drv_code,head_pic,seller_name,briefing as content,country,putonghua,language')
            ->where($where)
            ->find();
        if(empty($info)){
            return ['status' => -1, 'msg' => '没有记录'];
        }
        $str = '';
        $type = getIDType($info['seller_id']);
        if(!empty($type['store_id'])){
            $str .= '店主-';
        }
        if(!empty($type['drv_id'])){
            $str .= '司导-';
        }
        if(!empty($type['home_id'])){
            $str .= '房东-';
        }
        if(!empty($str)){
            $info['type_info'] = substr($str, 0, -1);
        }
        return $info;
    }

    /*
     * 收到的评价
     */
    public function get_comment_info($seller_id){
        $where = ['c.seller_id' => $seller_id];
        $info = M('pack_comment')
            ->field('u.head_pic,u.nickname,c.start_time,c.star,c.type,c.content')
            ->alias('c')
            ->join('__USERS__ u', 'c.user_id = u.user_id', 'LEFT')
            ->order('c.create_at desc')
            ->where($where)
            ->find();
        return $info;
    }

    /*
     * 我的相册
     */
    public function get_my_photo($seller_id){
        $where = ['seller_id' => $seller_id];
        $list = M('article_photo_type')->field('drv_type_id,cover_img')->where($where)->select();
        return $list;
    }

    /*
     * 我的故事
     */
    public function get_my_story(){
        return [];
    }

    /*
     * 我的路线
     */
    public function get_my_line($seller_id){
        $where = [
            'seller_id' => $seller_id,
            'is_del' => 0
        ];
        $list = M('pack_line')->field('line_id,cover_img')->where($where)->select();
        return $list;
    }

    /*
     * 我的车辆
     */
    public function get_my_car($seller_id){
        $list = M('pack_car_info')
            ->alias('c')
            ->field('c.*,b.name as brand_name,t.car_info')
            ->join('ruit_brand_car b', 'c.brand_id = b.id')
            ->join('ruit_pack_car_bar t', 'c.car_type_id = t.id')
            ->where(['is_state' => 1, 'seller_id' => $seller_id])
            ->select();
        return $list;
    }

    /**
     * 公共的存入数据库pack_base包车资源表 记录订单
     */
    public function save_pack_base($data, $user){

        $saveData = [
            'type' => $data['type'],
            'order_sn' => OrderLogic::get_order_sn(),
            'user_id' => $user['user_id'],
            'car_product_id' => intval($data['pcpid']),
            'customer_name' => $data['user_name'],
            'customer_phone' => $data['connect'],
            //'req_car_type' => $data['car_type_id'],
            'req_car_seat_num' => $data['car_seat_num'], // 座位数
            'req_car_level' => $data['car_level'], // 舒适度
            'drv_code' => $data['drv_code'],
            'use_car_adult' => empty($data['adult_num']) ? 0 : intval($data['adult_num']),
            'use_car_children' => empty($data['child_num']) ? 0 : intval($data['child_num']),
            'user_identity' => $data['user_identity'],
            'twenty_four' => empty($data['twenty_four']) ? 0 : intval($data['twenty_four']),
            'twenty_six' => empty($data['twenty_six']) ? 0 : intval($data['twenty_six']),
            'twenty_eight' => empty($data['twenty_eight']) ? 0 : intval($data['twenty_eight']),
            'thirty' => empty($data['thirty']) ? 0 : intval($data['thirty']),
            'remark' => $data['remark'],
            'flt_no' => $data['flt_no'], //航班号
            'start_time' => $data['start_time'], //
            'work_address' => $data['start_address'], //
            'dest_address' => $data['end_address'], //
            'produce_id'=> $data['pcpid'],
            'tour_favorite' => $data['tour_favorite'], //
            'order_day' => $data['order_day'], //
            'eating_ave' => $data['eating_ave'], //
            'stay_ave' => $data['stay_ave'], //
            'total_price' => $data['total_price'], //
            'real_price' => $data['real_price'], //
            'status' => $data['status'], //
            'city' => $data['city'],
            'country' => $data['country'],
            'create_at' => time(),
            'update_at' => time(),
        ];
        //!empty($data['allot_seller_id']) && $saveData['allot_seller_id'] = $data['allot_seller_id'];//指定司导
        !empty($data['country']) && $saveData['country'] = $data['country'];//出行国家
        !empty($data['city']) && $saveData['city'] = $data['city'];//出行城市
        !empty($data['car_level']) && $saveData['car_level'] = $data['car_level'];//舒适度
        if(!empty($data['seller_id'])){
            $saveData['allot_seller_id'] = ','.$data['seller_id'].',';//分配的司导
        }
        if(!empty($data['seller_id'])){
            //$push_info = M('seller')->where()->find();
            //$mobile = $push_info['countroy_code'].$push_info['mobile'];
            //$content = '';
            //sendSMSbyApi($mobile,$content);
        }
        $id = M('pack_order')->insertGetId($saveData);
        if(in_array($data['type'],[1,2,6])){
            M('pack_car_product')->where(['id'=>$data['pcpid']])->setInc('order_times');
        }
        //$id = $this->getLastInsID();
        //$id = M('pack_order')->getLastInsID();
        return $id;
    }

    /**
     * 按天包车游
     */
    public function rent_car_by_day($saveData){
        return M('pack_base_by_day')->save($saveData);
    }

    /**
     * 接机
     */
    public function receive_airport($saveData){
        return M('pack_base_receive')->save($saveData);
    }

    /**
     * 送机
     */
    public function send_airport($saveData){
        return M('pack_base_send')->save($saveData);
    }

    /**
     * 单次接送
     */
    public function once_pickup($saveData){
        return M('pack_base_once')->save($saveData);
    }

    /**
     * 私人定制
     */
    public function private_person($saveData){
        return M('pack_base_private')->save($saveData);
    }

    /*
     * 搜索司导
     */
    public function search_driver($where){
        $drv = M('seller')
            ->field('seller_id,head_pic,seller_name,drv_code,province,city,plat_start')
            ->where($where)
            ->select();
        foreach($drv as &$val){
            $result = getDrvIno($val['seller_id']);
            $val['province'] = getCityName($val['province']);
            $val['city'] = getCityName($val['city']);
            $val['star'] = $result['star'];
            $val['line'] = $result['line'];
        }
        if(empty($drv)){
            return ['status' => 4004, 'msg' => '没有数据'];
        }else{
            return ['status' => 2000, 'msg' => '成功', 'result' => $drv];
        }
    }

    /*
     * 搜索司导
     */
    public function find_driver($where){
        $sellerLogic = new SellerLogic();
        $drv = $sellerLogic->field('seller_id,head_pic,seller_name,drv_code,province,city,plat_start')
            ->where($where)
            ->find();
        if(empty($drv)){
            return ['status' => 4004, 'msg' => '没有数据'];
        }

        $result = getDrvIno($drv['seller_id']);
        $drv['province'] = getCityName($drv['province']);
        $drv['city'] = getCityName($drv['city']);
        $drv['star'] = $result['star'];
        $drv['line'] = $result['line'];
        return ['status' => 2000, 'msg' => '成功', 'result' => $drv];
    }

    /*
     * 更改私人定制
     */
    public function update_private($where,$data){
        $result = M('pack_order')->where($where)->update($data);
        if($result !== false){
            return ['status' => 1, 'msg' => '成功'];
        }
        return ['status' => -1, 'msg' => '更改失败'];
    }

    /*
     * 得到私人定制的行程详情
     */
    public function get_private_detail($air_id){
        $field = [
            'o.air_id',
            'o.total_price',
            'o.customer_name',
            'o.customer_phone',
            'p.tour_time',
            'o.work_address',
            'o.dest_address',
            'p.tour_days',
            'o.use_car_adult',
            'o.use_car_children',
            'o.req_car_level',
            'o.req_car_seat_num',
            //缺少游玩说明
            'o.cost_statement',
            'o.cost_compensation',
        ];
        $info = M('pack_order')->alias('o')
            ->field($field)
            ->join('ruit_pack_base_private p','o.air_id = p.base_id','LEFT')
            ->where("o.air_id = $air_id")
            ->find();
        $info['req_car_level'] = PackCarInfoLogic::LEVEL_ARR[$info['req_car_level']];

        $info['tour_time'] =shzDate($info['tour_time']);
        if(empty($info)){
            return resultArray(-1,'暂无数据',[]);
        }
        return resultArray(1,'成功',$info);
    }
}