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

class DriverLogic extends Model{

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
            ->field('seller_id,drv_id,drv_code,head_pic,seller_name,briefing,country,putonghua,language')
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
     * 公共的存入数据库pack_base包车资源表
     *
     *
     *
     *
     *
     *
     *
     */
    public function save_pack_base($data, $user){
        /*
          3线路订单
        rent_car_by_day 按天包车游  6按天包车游
        receive_airport 接机   1是接机
        send_airport 送机   2是送机
        once_pickup 单次接送  4单次接送
        private_person 私人定制  5私人订制
         */
        $type = 0;
        switch($data['type']){
            case 'rent_car_by_day':
                $type = 6;
                break;
            case 'receive_airport':
                $type = 1;
                break;
            case 'send_airport':
                $type = 2;
                break;
            case 'once_pickup':
                $type = 4;
                break;
            case 'private_person':
                $type = 5;
                break;
        }
        $OrderLogic = new OrderLogic();
        $saveData = [
            'type' => $type,
            'order_sn' => $OrderLogic->get_order_sn(),
            'user_id' => $user['user_id'],
            'customer_name' => $data['user_name'],
            'req_car_type' => $data['car_type_id'],
            'customer_phone' => $data['connect'],
            'drv_code' => $data['drv_code'],
            'is_have_pack' => $data['is_have_pack'],
            'use_car_adult' => $data['adult_num'],
            'use_car_children' => $data['child_num'],
            'user_identity' => $data['user_identity'],
            'twenty_four' => empty($data['twenty_four']) ? 0 : $data['twenty_four'],
            'twenty_six' =>  empty($data['twenty_six']) ?0: $data['twenty_six'],
            'twenty_eight' => empty($data['twenty_eight']) ? 0:$data['twenty_eight'],
            'thirty' => empty($data['thirty']) ? 0:$data['thirty'],
            'remark' => $data['remark'],
            'flt_no' => $data['flt_no'], //航班号
            'start_time' => $data['start_time'], //
            'work_address' =>  $data['start_address'], //
            'dest_address' => $data['dest_address'], //
            'con_car_seat_num' => $data['con_car_seat_num'], // 座位数
            'tour_favorite' => $data['tour_favorite'], //
            'order_day' => $data['order_day'], //
            'eating_ave' => $data['eating_ave'], //
            'stay_ave' => $data['stay_ave'], //
        ];
        $return = M('pack_order')->save($saveData);
        $id = $this->getLastInsID();
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
            return ['status' => -1, 'msg' => '没有数据'];
        }else{
            return ['status' => 1, 'msg' => '成功', 'result' => $drv];
        }
    }
}