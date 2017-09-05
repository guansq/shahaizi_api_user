<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 13:58
 */
namespace app\api\validate;
use think\Validate;

class BaseMessage extends Validate{
    protected $rule = [
        'mobile' => 'require|min:7',
        'opt' => 'require'
    ];

    protected $message = [
        'mobile.require'  =>  '请填写手机号',
        'opt.require' =>  '请填写验证码类型',
    ];

    protected $scene = [
        //'add'   =>  ['name','email'],
    ];
}