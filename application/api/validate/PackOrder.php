<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 14:26
 */

namespace app\api\validate;

use think\Validate;

class PackOrder extends Validate{
    protected $rule = [
        'line_id' => 'require',
        'title' => 'require',
        'customer_name' => 'require',
        'customer_phone' => 'require',
        'user_passport' => 'require',
        'user_identity' => 'require',
        'work_at' => 'require',
        'work_address' => 'require',
        'dest_address' => 'require',
        //'discount_id' => 'require',
        'total_price' => 'require',
    ];

    protected $message = [
        'line_id.require'  =>  '请填写路线ID',
        'title.require' =>  '请填写路线标题',
        'customer_name.require' =>  '请填写名称',
        'customer_phone.require' =>  '请填写手机号',
        'user_passport.require' =>  '请填写护照',
        'user_identity.require' =>  '请填写身份证',
        'work_at.require' =>  '请选择服务时间',
        'work_address.require' =>  '请填写出发地',
        'dest_address.require' =>  '请填写目的地',
        'child_num.require' =>  '请填写儿童乘客数',
        'remark.require' =>  '请填写备注信息',
        'dest_address.require' =>  '请填写目的地地址',
        'total_price.require' =>  '订单价格必填',
    ];
}