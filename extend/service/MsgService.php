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
use think\Model;
/**
 * 消息服务
 * Class ToolsService
 * @package service
 * @author  Anyon <zoujingli@qq.com>
 * @date    2016/10/25 14:49
 */
class MsgService extends Model{

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

    const ISENDURL='http://222.73.117.140:8044/mt';//单发短信接口
    const IQUERYURL='http://222.73.117.140:8044/bi';
    const BAT_SENDURL='http://222.73.117.140:8044/batchmt'; //群发短信接口

    const RUITUSERVICE = 'http://pushmsg.ruitukeji.com/';//睿途科技的发送邮件网关

    private $_sendUrl='';               // 发送短信接口url
    private $_queryBalanceUrl='';   // 查询余额接口url

    private $_un;           // 账号
    private $_pw;           // 密码

    /**
     * 构造方法
     * @param string $account  接口账号
     * @param string $password 接口密码
     */
    public function __construct(){
        $this->_un='I4816721';
        $this->_pw='7spztcAyQ';
    }


    /**
     * 国际验证码短信发送
     * @param string $phone     手机号码
     * @param string $content   短信内容
     * @param integer $isreport 是否需要状态报告
     * @return void
     */
    public  function sendInternational($phone,$content,$code,$type,$isreport=0){
        $requestData=array(
            'un'=>$this->_un,
            'pw'=>$this->_pw,
            'sm'=>$content,
            'da'=>$phone,
            'rd'=>$isreport,
            'rf'=>2,
            'tf'=>3,
        );
        $param='un='.$this->_un.'&pw='.$this->_pw.'&sm='.urlencode($content).'&da='.$phone.'&rd='.$isreport.'&rf=2&tf=3';
        $url=MsgService::ISENDURL.'?'.$param;//单发接口
        //$url=ChuanglanSMS::BAT_SENDURL.'?'.$param;//群发接口
        //进行存入数据库
        $saveData = [
            'mobile' => $phone,
            'code' => $code,
            'type' => $type,
            'update_at' => time(),
            'create_at' => time(),
        ];
        $where = [
            'mobile' => $phone,
            'type' => $type,
        ];
        $info = M('sms_info')->where($where)->find();

        if(empty($info)){
            M('sms_info')->save($saveData);
        }else{
            $needCheckArray = [''];
            //判断是否在60s之内更新的
            $time = time();
            if(($time - $info['update_at']) < 60){//当前更新的时间间隔小于60
                return ['status' => -1, 'msg' => '请在60s之后重新请求该接口'];
            }else{
                $updateData = [
                    'is_check' => 0,//更改状态为未验证过的验证码
                    'code' => $code,
                    'update_at' => time()
                ];
                M('sms_info')->where($where)->update($updateData);//更新该表
            }
        }
        //echo $time - $info['update_at'];die;
        $result = $this->_request($url);
        $result = json_decode($result,true);
        //dump($result);die;
        if($result['success']){//发送成功
            return ['status' => 1, 'msg' => '发送成功'];
        }else{
            return ['status' => -1, 'msg' => '发送失败'];
        }
    }

    /*
     * 普通短信发送（是否群发）第三个参数是否群发
     */
    public function sendSms($phone,$content,$isbatch=0,$isreport=0){
        $requestData=array(
            'un'=>$this->_un,
            'pw'=>$this->_pw,
            'sm'=>$content,
            'da'=>$phone,
            'rd'=>$isreport,
            'rf'=>2,
            'tf'=>3,
        );
        $param='un='.$this->_un.'&pw='.$this->_pw.'&sm='.urlencode($content).'&da='.$phone.'&rd='.$isreport.'&rf=2&tf=3';
        if($isbatch){
            $url=MsgService::BAT_SENDURL.'?'.$param;//群发接口
        }else{
            $url=MsgService::ISENDURL.'?'.$param;//单发接口
        }
        $result = $this->_request($url);
        $result = json_decode($result,true);
        //dump($result);die;
        if($result['success']){//发送成功
            return ['status' => 1, 'msg' => '发送成功'];
        }else{
            return ['status' => -1, 'msg' => '发送失败'];
        }
    }

    /*
     * 短信验证码验证
     */
    public function verifyInterCaptcha($mobile, $opt,$captcha){
        $where = [
            'mobile' => $mobile,
            'type' => $opt,
            'code' => $captcha,
        ];
        //验证码有效时间300s 5分钟
        $info = M('sms_info')->where($where)->find();
        //dump($info);die;
        if(empty($info)){
            return ['status'=>-1,'msg'=>'验证码验证出错'];
        }else{
            $time = time();
            if(($time - $info['update_at']) > 300){
                return ['status'=>-1,'msg'=>'该验证码已失效'];
            }
        }
        //判断是否已验证
        if($info['is_check']){
            return ['status'=>-1,'msg'=>'请不要重复验证'];
        }
        M('sms_info')->where($where)->update(['is_check'=>1]);
        return ['status'=>1,'msg'=>'验证成功'];
    }


    /**
     * 查询余额
     * @return String 余额返回
     */
    public function queryBalanceInternational(){
        $requestData=array(
            'un'=>$this->_un,
            'pw'=>$this->_pw,
            'rf'=>2
        );

        $url=MsgService::IQUERYURL.'?'.http_build_query($requestData);
        return $this->_request($url);
    }

    /* ========== 业务模块 ========== */

    /* ========== 功能模块 ========== */
    /**
     * 请求发送
     * @return string 返回状态报告
     */
    private function _request($url){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    /* ========== 功能模块 ========== */

    /*
     * 发送邮件 发送多个邮件通过 ;隔开
     */
    public function sendMailCaptcha($mail, $type, $content, $code){

        //进行存入数据库
        $saveData = [
            'mail' => $mail,
            'channel' => 1,//渠道
            'code' => $code,
            'type' => $type,
            'update_at' => time(),
            'create_at' => time(),
        ];
        $where = [
            'mail' => $mail,
            'type' => $type,
        ];
        $info = M('sms_info')->where($where)->find();

        if(empty($info)){
            M('sms_info')->save($saveData);
        }else{
            //判断是否在60s之内更新的
            $time = time();
            if(($time - $info['update_at']) < 60){//当前更新的时间间隔小于60
                return ['status' => -1, 'msg' => '请在60s之后重新请求该接口'];
            }else{
                $updateData = [
                    'is_check' => 0,//更改状态为未验证过的验证码
                    'code' => $code,
                    'update_at' => time()
                ];
                M('sms_info')->where($where)->update($updateData);//更新该表
            }
        }
        //进行发送邮件操作
        $result = sendMail($mail,'傻孩子APP用户操作信息',$content);
        //dump($result);die;
        if($result['code'] == 2000){//发送成功
            return ['status' => 1, 'msg' => '发送成功'];
        }else{
            return ['status' => -1, 'msg' => '发送失败'];
        }
    }

    /*
     * 发送邮件验证码校验
     */

    public function verifyMailCaptcha($mail, $opt,$captcha){
        $where = [
            'mail' => $mail,
            'channel' => 1,//1为发送邮件
            'type' => $opt,
            'code' => $captcha,
        ];
        //验证码有效时间300s 5分钟
        $info = M('sms_info')->where($where)->find();
        //dump($info);die;
        if(empty($info)){
            return ['status'=>-1,'msg'=>'验证码验证出错'];
        }else{
            $time = time();
            if(($time - $info['update_at']) > 300){
                return ['status'=>-1,'msg'=>'该验证码已失效'];
            }
        }
        //判断是否已验证
        if($info['is_check']){
            return ['status'=>-1,'msg'=>'请不要重复验证'];
        }
        M('sms_info')->where($where)->update(['is_check'=>1]);
        return ['status'=>1,'msg'=>'验证成功'];
    }
}
