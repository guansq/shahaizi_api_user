<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 13:58
 */
namespace app\api\validate;
use think\Validate;

class PackBase extends Validate{
    protected $rule = [
        'type' => 'require',
        'car_type_id' => 'require',
        'connect' => 'require',
        'user_name' => 'require',
        'is_have_pack' => 'require',
        'total_num' => 'require',
        'adult_num' => 'require',
        'child_num' => 'require',
        //'remark' => 'require',
        'dest_address' => 'require',
        'pack_time' => 'require',
        'flt_no' => 'require',
        'airport_name' => 'require',
        //'dest_address' => 'require',
        'start_time' => 'require',
        'start_address' => 'require',
        'user_car_time' => 'require',
        'tour_time' => 'require',
        'end_address' => 'require',
        'tour_days' => 'require',
        'tour_person_num' => 'require',
        'tour_favorite' => 'require',
        'recommend_diner' => 'require',
        'recommend_sleep' => 'require',
    ];

    protected $message = [
        'type.require'  =>  '请填写包车类型',
        'car_type_id.require' =>  '请填写车型ID',
        'connect.require' =>  '请填写联系方式',
        'user_name.require' =>  '请填写用户',
        'is_have_pack.require' =>  '请选择是否有行李',
        'total_num.require' =>  '请填写出行总人数',
        'adult_num.require' =>  '请填写成人乘客数',
        'child_num.require' =>  '请填写儿童乘客数',
        //'remark.require' =>  '请填写备注信息',
        'dest_address.require' =>  '请填写目的地地址',
        'pack_time.require' =>  '请填写包车日期',
        'flt_no.require' =>  '请填写航班号',
        'airport_name.require' =>  '请填写机场名',
        //'dest_address.require' =>  '请填写送达地点',
        'start_time.require' =>  '请填写出发时间',
        'start_address.require' =>  '请填写出发地点',
        'use_car_time.require'  =>  '用车时间',
        'tour_time.require' => '请填写出行时间',
        'end_address.require' => '请填写目的地',
        'tour_days.require' => '请填写游玩天数',
        'tour_person_num.require' => '请填写游玩人数',
        'tour_favorite.require' => '请填写出行偏好',
        'recommend_diner.require' => '请填写推荐餐馆',
        'recommend_sleep.require' => '请填写推荐住宿',
    ];

    protected $scene = [
        'rentCarByDay' => ['type','car_type_id','user_name','connect','is_have_pack','total_num','adult_num','child_num','dest_address','pack_time'],
        'receiveAirport' => ['type','car_type_id','user_name','connect','is_have_pack','total_num','flt_no','airport_name','dest_address','start_time'],
        'sendAirport' => ['type','car_type_id','user_name','connect','is_have_pack','total_num','flt_no','airport_name','start_address','start_time'],
        'oncePickup' => ['type','car_type_id','user_name','connect','is_have_pack','total_num','adult_num','child_num','start_address','dest_address','user_car_time'],
        'privateMake' => ['type','car_type_id','user_name','connect','is_have_pack','total_num','adult_num','child_num','tour_time','end_address','tour_days','tour_person_num','tour_favorite','recommend_diner','recommend_sleep'],
        //'add'   =>  ['name','email'],
    ];
}