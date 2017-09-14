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
use think\Db;

class DriverLogic extends Model{

    /*
     * 得到司导列表
     */
    public function get_driver_list($where){

        $count = M('seller')->where($where)->count();
        $Page = new Page($count, 10);
        $list = M('seller')->field('seller_id,drv_id,drv_code,head_pic,seller_name')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if(empty($list)){
            return ['status'=>-1,'msg'=>'没有记录'];
        }
        //去计算评分
        foreach($list as &$val){
            $star = M('pack_comment')->where('seller_id',$val['seller_id'])->avg('star');
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
        $info = M('seller')->field('seller_id,drv_id,drv_code,head_pic,seller_name,briefing,country,putonghua,language')->where($where)->find();
        if(empty($info)){
            return ['status'=>-1,'msg'=>'没有记录'];
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
            $info['type_info'] = substr($str,0,-1);
        }
        return $info;
    }

    /*
     * 收到的评价
     */
    public function get_comment_info($seller_id){
        $where = ['c.seller_id' => $seller_id];
        $info = M('pack_comment')->field('u.head_pic,u.nickname,c.start_time,c.star,c.type,c.content')->alias('c')->join('__USERS__ u','c.user_id = u.user_id','LEFT')->order('c.create_at desc')->where($where)->find();
        return $info;
    }

    /*
     * 我的相册
     */
    public function get_my_photo($seller_id){
        $where = ['seller_id'=>$seller_id];
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
    public function get_my_line(){

    }

    /*
     * 我的车辆
     */
    public function get_my_car(){
        //$where = [];
    }

    /**
     * 公共的存入数据库pack_base包车资源表
     */
    public function save_pack_base($data,$user){
        $saveData = [
            'type' => $data['type'],
            'user_id' => $user['user_id'],
            'user_name' => $user['nickname'],
            'car_type_id' => $data['car_type_id'],
            'connect' => $data['connect'],
            'drv_code' => $data['drv_code'],
            'is_have_pack' => $data['is_have_pack'],
            'total_num' => $data['total_num'],
            'adult_num' => $data['adult_num'],
            'child_num' => $data['child_num'],
            'create_at' => time(),
            'update_at' => time(),
            'remark' => $data['remark'],
        ];
        $return = M('pack_base')->save($saveData);
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
}