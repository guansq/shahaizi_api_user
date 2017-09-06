<?php

// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------

namespace service;

/**
 * 消息服务
 * Class ToolsService
 * @package service
 * @author  Anyon <zoujingli@qq.com>
 * @date    2016/10/25 14:49
 */
class MsgService{

    const RT_APP_KEY  = 'rtkj';
    const RT_MSG_HOME = 'http://mps.ruitukeji.com';

    /**
     * Author: WILL<314112362@qq.com>
     * Time: ${DAY}
     * @param string $mobile 手机号
     * @param string $msg    短信内容
     */
    public static function sendText($mobile = "", $msg = ""){

    }

    /**
     * Author: WILL<314112362@qq.com>
     * Time: ${DAY}
     * @param string $mobile 手机号
     * @param string $opt    验证码用途
     */
    public static function sendCaptcha($mobile, $opt){
        $data = [
            'rt_appkey' => self::RT_APP_KEY,
            "req_time" => time(),
            "req_action" => 'sendCaptcha',
            'mobile' => $mobile,
            'opt' => $opt,
        ];


        $data['sign'] = createSign($data);
        $httpRet = HttpService::post(self::RT_MSG_HOME.'/SendSms/sendCaptcha', $data);
        if(empty($httpRet)){
            return resultArray(-1,'请求验证码出错',[]);
        }
        $ret = json_decode($httpRet, true);
        if(empty($ret)){
            return resultArray(-1,'',$ret);
        }
        if($ret['msg'] == '手机号码个数错'){
            $ret['msg'] = '手机号未注册';
        }
        if($ret['code'] == 2000){
            return resultArray(1,'成功');
        }
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Time: ${DAY}
     * @param string $mobile 手机号
     * @param string $opt       验证码用途
     * @param string $captcha   验证码
     */
    public static function verifyCaptcha($mobile, $opt,$captcha){
        $data = [
            'rt_appkey' => self::RT_APP_KEY,
            "req_time" => time(),
            "req_action" => 'verifyCaptcha',
            'mobile' => $mobile,
            'opt' => $opt,
            'captcha' => $captcha,
        ];
        $data['sign'] = createSign($data);
        $httpRet = HttpService::post(self::RT_MSG_HOME.'/SendSms/verifyCaptcha', $data);
        if(empty($httpRet)){
            return resultArray(-1,'验证码验证出错',[]);
        }
        $httpRet = json_decode($httpRet, true);
        if($httpRet['code'] == 2000){
            return resultArray(1,'验证成功',[]);
        }else{
            return resultArray(-1,'验证码验证出错',[]);
        }
    }
}
