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
use service\HttpService;

class BaseMessage extends Base{

    /**
     * @api      {POST} /index.php?m=Api&c=BaseMessage&a=sendCaptcha  发送验证码done
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

        returnJson(MsgService::sendCaptcha($data['mobile'],$data['opt']));
    }

    /**
     * @api      {POST} /index.php?m=Api&c=BaseMessage&a=sendInterCaptcha  发送国际验证码
     * @apiName  sendInterCaptcha
     * @apiGroup Common
     * @apiParam {String} mobile   手机号.
     * @apiParam {String} opt      验证码类型 reg=注册 resetpwd=找回密码 login=登陆 bind=绑定手机号.
     */
    public function sendInterCaptcha(){

        $data = input('param.');
        $result = $this->validate($data,'BaseMessage');
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->ajaxReturn(['status'=>1,'msg'=>$result,'result'=>'']);
        }
        //6位数字验证码
        $min = pow(10 , 5);
        $max = pow(10, 6);
        $code = rand($min, $max);
        switch($data['opt']){
            case 'reg' :
                $content = '【傻孩子APP】您正在进行[注册]操作，验证码为：'.$code;
                break;
            case 'resetpwd' :
                $content = '【傻孩子APP】您正在进行[重置密码]操作，验证码为：'.$code;
                break;
            case 'bind' :
                $content = '【傻孩子APP】您正在进行[绑定手机]操作，验证码为：'.$code;
                break;
            case 'login' :
                $content = '【傻孩子APP】您正在进行[登录]操作，验证码为：'.$code;
                break;
            default :
                $content = '【傻孩子APP】您正在进行[注册]操作，验证码为：{d6}'.$code;
                break;
        }
        $msgService = new MsgService();
        $result = $msgService->sendInternational($data['mobile'],$content,$code,$data['opt']);
        $this->ajaxReturn($result);
    }

    /**
     * 测试发送短信
     */
    public function testsendSms(){
        $msgService = new MsgService();
        $result = $msgService->sendSms('008615250215762','【傻孩子APP】大美收到短信了吗？');
        $this->ajaxReturn($result);
    }
}