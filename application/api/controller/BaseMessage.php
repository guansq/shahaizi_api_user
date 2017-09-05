<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 13:33
 */

namespace app\api\controller;

use think\Validate;
use service\MsgService;

class BaseMessage extends Base{

    /**
     * @api      {POST} /index/sendCaptcha  发送验证码done
     * @apiName  sendCaptcha
     * @apiGroup Common
     * @apiParam {String} mobile   手机号.
     * @apiParam {String} opt      验证码类型 reg=注册 resetpwd=找回密码 login=登陆 bind=绑定手机号.
     */
    public function sendCaptcha(){
        $data = input('param.');
        $result = $this->validate($data,'BaseMessage');
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->ajaxReturn(['status'=>1,'msg'=>$result,'result'=>'']);
        }

        /*if($data['opt'] == 'reg'){
            //用已有账号注册时，依然能获得验证码，  此处应该不能再获得验证码且应提示“该用户已存在”。
            $info =  model('User','logic')->findByAccount($data['mobile']);
            if(!empty($info)){
                returnJson('4000','该用户已存在');
            }
        }
        if(in_array($data['opt'],['resetpwd'])){
            $isReg = isReg($data['mobile']);
            if(!$isReg){
                returnJson(4000,'抱歉您还未注册');
            }
        }*/
        returnJson(MsgService::sendCaptcha($data['mobile'],$data['opt']));
    }
}