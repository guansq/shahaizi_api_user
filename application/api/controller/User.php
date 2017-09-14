<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */

namespace app\api\controller;

use app\api\logic\UserLogic;
use app\common\logic\CartLogic;
use app\common\logic\CommentLogic;
use app\common\logic\CouponLogic;
use app\common\logic\OrderLogic;
use app\common\logic\StoreLogic;
use app\common\logic\UsersLogic;
use service\MsgService;
use think\Page;

class User extends Base{
    public $userLogic;

    /**
     * 析构流函数
     */
    public function __construct(){
        parent::__construct();
        $this->userLogic = new UsersLogic();
    }


    /**
     * @api      {POST} /index.php?m=Api&c=User&a=login     用户登录done  管少秋
     * @apiName  login
     * @apiGroup User
     * @apiParam {String} username          用户名.
     * @apiParam {String} password          密码.
     * @apiParam {String} unique_id         手机端唯一标识 类似web pc端sessionid.
     * @apiParam {String} pushToken         消息推送token.
     * @apiParam {String} capache         图形验证码.
     * @apiParam {String} push_id         推送id，相当于第三方的reg_id.
     * @apiSuccessExample {json}    Success-Response:
     *           Http/1.1   200 OK
     * {
     * "status": 1,
     * "msg": "登陆成功",
     * "result": {
     * "user_id": "1",
     * "email": "398145059@qq.com",
     * "password": "e10adc3949ba59abbe56e057f20f883e",
     * "sex": "1",
     * "birthday": "2015-12-30",
     * "user_money": "9999.39",
     * "frozen_money": "0.00",
     * "pay_points": "5281",
     * "address_id": "3",
     * "reg_time": "1245048540",
     * "last_login": "1444134213",
     * "last_ip": "127.0.0.1",
     * "qq": "3981450598",
     * "mobile": "13800138000",
     * "mobile_validated": "0",
     * "oauth": "",
     * "openid": null,
     * "head_pic": "/Public/upload/head_pic/2015/12-28/56812d56854d0.jpg",
     * "province": "19",
     * "city": "236",
     * "district": "2339",
     * "email_validated": "1",
     * "nickname": "的广泛地"
     * "token": "9f3de86be794f81cdfa5ff3f30b99257"        // 用于 app 登录
     * "expireTime":"1245048540"         //token过期时间
     * }
     * }
     * @apiErrorExample {json}  Error-Response:
     *           Http/1.1   404 NOT FOUND
     * {
     * "status": -1,
     * "msg": "请填写账号或密码",
     * "result": ""
     * }
     */
    public function login(){
        $username = I('username', '');
        $password = I('password', '');
        $capache = I('capache', '');
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        $push_id = I('push_id', '');
        $data = $this->userLogic->app_login($username, $password, $capache, $push_id);

        if($data['status'] != 1){
            $this->ajaxReturn($data);
        }

        $cartLogic = new CartLogic();
        $cartLogic->setUserId($data['result']['user_id']);
        $cartLogic->setUniqueId($unique_id);
        $cartLogic->doUserLoginHandle();  // 用户登录后 需要对购物车 一些操作
        $this->ajaxReturn($data);
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=User&a=flashToken    刷新token（返回信息同login一样） done 管少秋
     * @apiName     flashToken
     * @apiGroup    User
     * @apiParam    {String}    token   token.
     */
    public function flashToken(){
        $result = $this->userLogic->flash_token($this->user_id);
        $this->ajaxReturn($result);
    }

    /**
     * 登出
     */
    public function logout(){
        $token = I("post.token", '');
        $data = $this->userLogic->app_logout($token);
        $this->ajaxReturn($data);
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=User&a=thirdLogin     第三方登录done（未调试） 管少秋
     * @apiName     thirdLogin
     * @apiGroup    User
     * @apiParam    {String}      unique_id     第三方唯一标识
     * @apiParam    {String}      from          来源 wx weibo alipay
     * @apiParam    {String}      [nickname]    第三方返回昵称
     * @apiParam    {String}      [head_pic]    头像路径
     * @apiSuccessExample   {json}  Success-response
     *      Http/1.1    200 Ok
     * {
     * "status": 1,
     * "msg": "登陆成功",
     * "result": {
     * "user_id": "12",
     * "email": "",
     * "password": "",
     * "sex": "0",
     * "birthday": "0000-00-00",
     * "user_money": "0.00",
     * "frozen_money": "0.00",
     * "pay_points": "0",
     * "address_id": "0",
     * "reg_time": "1452331498",
     * "last_login": "0",
     * "last_ip": "",
     * "qq": "",
     * "mobile": "",
     * "mobile_validated": "0",
     * "oauth": "wx",
     * "openid": "2",
     * "head_pic": null,
     * "province": "0",
     * "city": "0",
     * "district": "0",
     * "email_validated": "0",
     * "nickname": ""
     * }
     * }
     * @apiErrorExample     {json}  Error-response
     *              Http/1.1    200 OK
     *   {
     * "status": -1,
     * "msg": "参数有误",
     * "result": ""
     * }
     */
    public function thirdLogin(){
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        $map['openid'] = I('openid', '');
        $map['oauth'] = I('from', '');
        $map['nickname'] = I('nickname', '');
        $map['head_pic'] = I('head_pic', '');
        $map['unionid'] = I('unionid', '');
        $map['push_id'] = I('push_id', '');
        $map['sex'] = I('sex', 0);

        if($map['oauth'] == 'miniapp'){
            $code = I('post.code', '');
            if(!$code){
                $this->ajaxReturn(['status' => -1, 'msg' => 'code值非空']);
            }

            $miniapp = new \app\common\logic\MiniAppLogic;
            $session = $miniapp->getSessionInfo($code);
            if($session === false){
                $this->ajaxReturn(['status' => -1, 'msg' => $miniapp->getError()]);
            }
            $map['openid'] = $session['openid'];
            $map['unionid'] = $session['unionid'];
        }

        $data = $this->userLogic->thirdLogin($map);
        if($data['status'] == 1){
            $cartLogic = new CartLogic();
            $cartLogic->setUserId($data['result']['user_id']);
            $cartLogic->setUniqueId($unique_id);
            $cartLogic->doUserLoginHandle();// 用户登录后 需要对购物车 一些操作
            //重新获取用户信息，补全数据
            $data = $this->userLogic->getApiUserInfo($data['result']['user_id']);
        }
        $this->ajaxReturn($data);
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=User&a=bindPhone     绑定手机done  管少秋
     * @apiName     bindPhone
     * @apiGroup    User
     * @apiParam    {String}    token       token
     * @apiParam    {String}    mobile      绑定手机号
     * @apiParam    {String}    countroy_code   绑定国家的区号
     * @apiParam    {String}    code        绑定手机code
     */
    public function bindPhone(){
        //看该手机号是否被别人绑定
        $mobile = I('mobile');
        if(!check_mobile($mobile)){
            $this->ajaxReturn(['status' => -1, 'msg' => '请填写正确的手机号']);
        }
        $user = M('users')->field('mobile, nickname')->where(['mobile' => $mobile])->find();
        if(!empty($user)){
            $this->ajaxReturn(['status' => -1, 'msg' => '该手机号已被绑定']);
        }
        $countroy_code = I('countroy_code');
        $sendphone = $countroy_code.$mobile;
        $code = I('code');
        //校验code
        $msgService = new MsgService();
        $result = $msgService->verifyInterCaptcha($sendphone, 'bind', $code);
        if($result['status'] != 1){
            $this->ajaxReturn(['status' => -1, 'msg' => $result['msg']]);
        }
        $where = [
            'user_id' => $this->user_id
        ];
        $updateData = [
            'mobile' => $mobile,
            'mobile_validated' => 1
        ];
        $result = M('users')->where($where)->update($updateData);
        if($result === false){
            $this->ajaxReturn(['status' => -1, 'msg' => '绑定失败']);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '绑定成功']);
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=User&a=changeBindPhone     更改绑定手机   done 管少秋
     * @apiName     changeBindPhone
     * @apiGroup    User
     * @apiParam    {String}    token       token
     * @apiParam    {String}    mobile      新手机号
     * @apiParam    {Number}    code        需要验证的验证码
     */
    public function changeBindPhone(){
        $code = I('code');
        $mobile = I('mobile');
        $old_mobile = $this->user['mobile'];
        if($old_mobile == $mobile){
            $this->ajaxReturn(['status' => -1, 'msg' => '当前改绑的手机号与绑定的手机号相同']);
        }
        $user = M('users')->field('mobile, nickname')->where(['mobile' => $mobile])->find();
        if(empty($user)){
            $countroy_code = $this->user['countroy_code'];
            $sendphone = $countroy_code.$mobile;
            //校验code
            $msgService = new MsgService();
            $result = $msgService->verifyInterCaptcha($sendphone, 'bind', $code);
            if($result['status'] != 1){
                $this->ajaxReturn(['status' => -1, 'msg' => $result['msg']]);
            }
            $where = [
                'user_id' => $this->user_id
            ];
            $updateData = [
                'mobile' => $mobile,
                'mobile_validated' => 1
            ];
            $result = M('users')->where($where)->update($updateData);
            if($result === false){
                $this->ajaxReturn(['status' => -1, 'msg' => '绑定失败']);
            }
            $this->ajaxReturn(['status' => 1, 'msg' => '绑定成功']);
        }else{
            $this->ajaxReturn(['status' => -1, 'msg' => '该手机号已被绑定']);
        }
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=User&a=BindMail      绑定用户邮箱done  管少秋
     * @apiName     BindMail
     * @apiGroup    User
     * @apiParam    {String}    token       token
     * @apiParam    {String}    mail        绑定邮箱
     * @apiParam    {String}    code        绑定邮箱code
     */
    public function bindMail(){
        //看该邮箱号是否被别人绑定
        $mail = I('mail');
        if(!check_email($mail)){
            $this->ajaxReturn(['status' => -1, 'msg' => '请填写正确的邮箱']);
        }
        $user = M('users')->field('mobile, nickname')->where(['email' => $mail])->find();
        if(!empty($user)){
            $this->ajaxReturn(['status' => -1, 'msg' => '该邮箱已被绑定']);
        }
        $code = I('code');
        //校验code
        $msgService = new MsgService();
        $result = $msgService->verifyMailCaptcha($mail, 'bind', $code);
        if($result['status'] != 1){
            $this->ajaxReturn(['status' => -1, 'msg' => $result['msg']]);
        }
        $where = [
            'user_id' => $this->user_id
        ];
        $updateData = [
            'mail' => $mail,
            'mail_validated' => 1
        ];
        $result = M('users')->where($where)->update($updateData);
        if($result === false){
            $this->ajaxReturn(['status' => -1, 'msg' => '绑定失败']);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '绑定成功']);
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=User&a=changeBindMail      更改绑定用户邮箱  done 管少秋
     * @apiName     changeBindMail
     * @apiGroup    User
     * @apiParam    {String}    token       token
     * @apiParam    {String}    mail        更改绑定的邮箱
     * @apiParam    {Number}    code        绑定邮箱code
     */
    public function changeBindMail(){
        //看该邮箱号是否被别人绑定
        $mail = I('mail');
        if(!check_email($mail)){
            $this->ajaxReturn(['status' => -1, 'msg' => '请填写正确的邮箱']);
        }
        $user = M('users')->field('mobile, nickname')->where(['email' => $mail])->find();
        if(!empty($user)){
            $this->ajaxReturn(['status' => -1, 'msg' => '该邮箱已被绑定']);
        }
        $code = I('code');
        //校验code
        $msgService = new MsgService();
        $result = $msgService->verifyMailCaptcha($mail, 'bind', $code);
        if($result['status'] != 1){
            $this->ajaxReturn(['status' => -1, 'msg' => $result['msg']]);
        }
        $where = [
            'user_id' => $this->user_id
        ];
        $updateData = [
            'mail' => $mail,
            'mail_validated' => 1
        ];
        $result = M('users')->where($where)->update($updateData);
        if($result === false){
            $this->ajaxReturn(['status' => -1, 'msg' => '绑定失败']);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '绑定成功']);
    }

    /**
     * @api       {POST} /index.php?m=Api&c=User&a=reg            用户注册done  管少秋
     * @apiName   reg
     * @apiGroup  User
     * @apiParam {String} username         手机号（未加国家区号的手机号）/邮件名.
     * @apiParam {String} password         密码加密方式：md5(TPSHOP密码)
     * @apiParam {String} type             phone 为手机/mail为邮件
     * @apiParam {String} [countroy_code]    国家代码编号
     * @apiParam {String} [code]           手机短信验证码或邮箱验证码
     * @apiParam {String} [push_id]        推送id，相当于第三方的reg_id
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     * {
     * "status": 1,
     * "msg": "注册成功",
     * "result": {
     * "user_id": 146,
     * "email": "",
     * "password": "90600d68b0f56d90c4c34284d8dfd138",
     * "sex": 0,
     * "birthday": 0,
     * "user_money": "0.00",
     * "frozen_money": "0.00",
     * "distribut_money": "0.00",
     * "pay_points": "0.0000",
     * "address_id": 0,
     * "reg_time": 1504596640,
     * "last_login": 1504596640,
     * "last_ip": "",
     * "qq": "",
     * "mobile": "18451847701",
     * "mobile_validated": 1,
     * "oauth": "",
     * "openid": null,
     * "unionid": null,
     * "head_pic": null,
     * "province": 0,
     * "city": 0,
     * "district": 0,
     * "email_validated": 0,
     * "nickname": "18451847701",
     * "level": 1,
     * "discount": "1.00",
     * "total_amount": "0.00",
     * "is_lock": 0,
     * "is_distribut": 1,
     * "first_leader": 0,
     * "second_leader": 0,
     * "third_leader": 0,
     * "fourth_leader": null,
     * "fifth_leader": null,
     * "sixth_leader": null,
     * "seventh_leader": null,
     * "token": "c34ba58aec24003f0abec19ae2688c86",
     * "address": null,
     * "pay_passwd": null,
     * "pre_pay_points": "0.0000",
     * "optional": "0.0000",
     * "vipid": 0,
     * "paypoint": "0.00"
     * }
     * }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     * "status": -1,
     * "msg": "账号已存在",
     * "result": ""
     * }
     */
    public function reg(){
        $username = I('post.username', '');
        $countroy_code = I('post.countroy_code', '');//国家代码
        $send_phone = $countroy_code.$username;
        $password = I('post.password', '');
        $code = I('post.code');
        $type = I('type', 'phone');
        $session_id = I('unique_id', session_id());// 唯一id  类似于 pc 端的session id
        $scene = I('scene', 1);
        $push_id = I('post.push_id', '');

        //是否开启注册验证码机制 check_mobile($username)
        $msgService = new MsgService();
        if($type == 'phone'){
            //校验验证码
            $result = $msgService->verifyInterCaptcha($send_phone, 'reg', $code);
            if($result['status'] != 1){
                returnJson(-1, $result['msg']);
            }
        }else{
            //验证邮箱Code
            $result = $msgService->verifyMailCaptcha($username, 'reg', $code);
            if($result['status'] != 1){
                returnJson(-1, $result['msg']);
            }
        }

        $data = $this->userLogic->reg($username, $password, $password, $push_id, $countroy_code);
        if($data['status'] == 1){
            $cartLogic = new CartLogic();
            $cartLogic->setUserId($data['result']['user_id']);
            $cartLogic->setUniqueId($session_id);
            $cartLogic->doUserLoginHandle(); // 用户登录后 需要对购物车 一些操作
        }
        exit(json_encode($data));
    }

    /**
     * @api      {POST} /index.php?m=Api&c=User&a=userInfo   获取用户信息done  管少秋
     * @apiName  info
     * @apiGroup User
     * @apiParam {String}   token           token.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     * {
     * "status": 1,
     * "msg": "获取成功",
     * "result": {
     * "user_id": 146,
     * "email": "",
     * "password": "90600d68b0f56d90c4c34284d8dfd138",
     * "sex": 0,
     * "birthday": 0,
     * "user_money": "0.00",
     * "frozen_money": "0.00",
     * "distribut_money": "0.00",
     * "pay_points": "0.0000",
     * "address_id": 0,
     * "reg_time": 1504596640,
     * "last_login": 1504602255,
     * "last_ip": "",
     * "qq": "",
     * "mobile": "18451847701",
     * "mobile_validated": 1,
     * "oauth": "",
     * "openid": null,
     * "unionid": null,
     * "head_pic": null,
     * "province": 0,
     * "city": 0,
     * "district": 0,
     * "email_validated": 0,
     * "nickname": "18451847701",
     * "level": 1,
     * "discount": "1.00",
     * "total_amount": "0.00",
     * "is_lock": 0,
     * "is_distribut": 1,
     * "first_leader": 0,
     * "second_leader": 0,
     * "third_leader": 0,
     * "fourth_leader": null,
     * "fifth_leader": null,
     * "sixth_leader": null,
     * "seventh_leader": null,
     * "token": "a279c833cebe5fb963ccba311e27c394",
     * "address": null,
     * "pay_passwd": null,
     * "pre_pay_points": "0.0000",
     * "optional": "0.0000",
     * "vipid": 0,
     * "paypoint": "0.00",
     * "coupon_count": 0,
     * "collect_count": 0,
     * "focus_count": 0,
     * "visit_count": 0,
     * "return_count": 0,
     * "waitPay": 0,
     * "waitSend": 0,
     * "waitReceive": 0,
     * "order_count": 0,
     * "message_count": 0,
     * "comment_count": 0,
     * "uncomment_count": 0,
     * "serve_comment_count": 0,
     * "cart_goods_num": 0
     * }
     * }
     *
     */
    public function userInfo(){
        //$user_id = I('user_id/d');
        $data = $this->userLogic->getApiUserInfo($this->user_id);
        exit(json_encode($data));
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=User&a=updateUserInfo     更改用户信息done  管少秋
     * @apiName     updateUserInfo
     * @apiGroup    User
     * @apiParam    {String}    [nickname]      昵称
     * @apiParam    {String}    [qq]            QQ号码
     * @apiParam    {String}    [head_pic]      头像URL
     * @apiParam    {String}    [sex]           性别（0 保密 1 男 2 女）
     * @apiParam    {String}    [birthday]      生日 （2015-01-05）
     * @apiParam    {String}    [province]      省份ID
     * @apiParam    {String}    [city]          城市ID
     * @apiParam    {String}    [district]      地区ID
     * @apiParam    {String}    token      token
     */
    public function updateUserInfo(){
        if(IS_POST){
            //$user_id = I('user_id/d');
            if(!$this->user_id){
                exit(json_encode(array('status' => -1, 'msg' => '缺少参数', 'result' => '')));
            }

            I('post.nickname') ? $post['nickname'] = I('post.nickname') : false; //昵称
            I('post.qq') ? $post['qq'] = I('post.qq') : false;  //QQ号码
            I('post.head_pic') ? $post['head_pic'] = I('post.head_pic') : false; //头像地址
            I('post.sex') ? $post['sex'] = I('post.sex') : false;  // 性别
            I('post.birthday') ? $post['birthday'] = strtotime(I('post.birthday')) : false;  // 生日
            I('post.province') ? $post['province'] = I('post.province') : false;  //省份
            I('post.city') ? $post['city'] = I('post.city') : false;  // 城市
            I('post.district') ? $post['district'] = I('post.district') : false;  //地区
            I('post.email') ? $post['email'] = I('post.email') : false;
            I('post.mobile') ? $post['mobile'] = I('post.mobile') : false;

            if(!$this->userLogic->update_info($this->user_id, $post)){
                exit(json_encode(array('status' => -1, 'msg' => '更新失败', 'result' => '')));
            }
            exit(json_encode(array('status' => 1, 'msg' => '更新成功', 'result' => '')));

        }
    }


    /**
     * @api      {POST} /index.php?m=Api&c=User&a=password   修改用户密码done 管少秋
     * @apiName  password
     * @apiGroup User
     * @apiParam {String}   token           token.
     * @apiSuccessExample {json} Success-Response:
     *           Http/1.1   200 OK
     * {
     * "status": 1,
     * "msg": "密码修改成功",
     * "result": ""
     * }
     */
    public function password(){
        if(IS_POST){
            if(!$this->user_id){
                exit(json_encode(array('status' => -1, 'msg' => '缺少参数', 'result' => '')));
            }
            $data = $this->userLogic->passwordForApp($this->user_id, I('post.old_password'), I('post.new_password')); // 修改密码
            exit(json_encode($data));
        }
    }

    public function forgetPasswordInfo(){
        $account = I('post.account', '');
        $capache = I('post.capache', '');
        if(!capache([], SESSION_ID, $capache)){
            $this->ajaxReturn(['status' => -1, 'msg' => '验证码错误！']);
        }
        if(($user = M('users')->field('mobile, nickname')->where(['mobile' => $account])->find()) || ($user = M('users')
                ->field('mobile, nickname')
                ->where(['email' => $account])
                ->find()) || ($user = M('users')->field('mobile, nickname')->where(['nickname' => $account])->find())){
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $user]);
        }
        if(!$user){
            $this->ajaxReturn(['status' => -1, 'msg' => '该账户不存在']);
        }
    }

    /**
     * 短信验证
     */
    public function check_sms(){
        $mobile = I('post.mobile');
        $unique_id = I('unique_id');
        $code = I('post.check_code');   //验证码
        $scene = I('post.scene/d', 2);   //验证码
        if(!check_mobile($mobile)){
            $this->ajaxReturn(['status' => -1, 'msg' => '手机号码格式不正确', 'result' => '']);
        }

        $res = $this->userLogic->check_validate_code($code, $mobile, 'phone', $unique_id, $scene);
        if($res['status'] != 1){
            $this->ajaxReturn($res);
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '验证成功']);
    }

    /**
     * 修改手机验证
     */
    public function change_mobile(){
        $mobile = I('post.mobile');
        $unique_id = I('unique_id');
        $code = I('post.check_code');   //验证码
        $scene = I('post.scene/d', 0);   //验证码
        $capache = I('post.capache', '');
        if(!check_mobile($mobile)){
            $this->ajaxReturn(['status' => -1, 'msg' => '手机号码格式不正确', 'result' => '']);
        }

        $res = $this->userLogic->check_validate_code($code, $mobile, 'phone', $unique_id, $scene);
        if($res['status'] != 1){
            $this->ajaxReturn($res);
        }

        /* if (!capache([], SESSION_ID, $capache)) {
            $this->ajaxReturn(['status'=>-1,'msg'=>'图形验证码错误！']);
        } */

        if($scene != 6){
            $this->ajaxReturn(['status' => -1, 'msg' => '场景码错误！']);
        }

        $data['mobile'] = $mobile;
        if(!$this->userLogic->update_info($this->user_id, $data)){
            $this->ajaxReturn(['status' => -1, 'msg' => '手机号码更新失败']);
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '更改成功']);
    }


    /**
     * @api      {POST} /index.php?m=Api&c=User&a=forgetPassword   忘记密码通过短信done  管少秋
     * @apiName  forgetPassword
     * @apiGroup User
     * @apiParam {String}   mobile      手机号
     * @apiParam {String}   password    加密后的密码
     * @apiParam {String}   code    验证码
     * @apiSuccessExample {json} Success-Response:
     *           Http/1.1   200 OK
     * {
     * "status": 1,
     * "msg": "密码已重置,请重新登录",
     * }
     */
    public function forgetPassword(){
        $password = I('password');
        $mobile = I('mobile', 'invalid');
        //$consignee = I('consignee', '');
        $code = I('code');

        $user = M('users')->where("mobile", $mobile)->find();
        if(!$user){
            $this->ajaxReturn(['status' => -1, 'msg' => '该手机号码没有关联账户']);
        }else{
            $sendphone = $user['countroy_code'].$mobile;
            //echo $sendphone;die;
            //校验验证码
            $msgClass = new MsgService();
            $result = $msgClass->verifyInterCaptcha($sendphone, 'resetpwd', $code);

            if($result['status'] != 1){
                returnJson(-1, '验证码输入有误');
            }
            //修改密码
            M('users')->where("user_id", $user['user_id'])->save(array('password' => $password));
            $this->ajaxReturn(['status' => 1, 'msg' => '密码已重置,请重新登录']);
        }
    }

    /**
     * @api      {POST} /index.php?m=Api&c=User&a=forgetPasswordByMail   忘记密码通过邮箱done  管少秋
     * @apiName  forgetPasswordByMail
     * @apiGroup User
     * @apiParam {String}   mail      邮箱号
     * @apiParam {String}   password    加密后的密码
     * @apiParam {String}   code    验证码
     * @apiSuccessExample {json} Success-Response:
     *           Http/1.1   200 OK
     * {
     * "status": 1,
     * "msg": "密码已重置,请重新登录",
     * }
     */
    public function forgetPasswordByMail(){
        $password = I('password');
        $mail = I('mail');
        if(!check_email($mail)){
            $this->ajaxReturn(['status' => -1, 'msg' => '请输入合法的邮箱']);
        }
        $code = I('code');

        $user = M('users')->where("email", $mail)->find();
        if(!$user){
            $this->ajaxReturn(['status' => -1, 'msg' => '该邮箱没有关联账户']);
        }else{
            //校验验证码
            $msgClass = new MsgService();
            $result = $msgClass->verifyMailCaptcha($mail, 'resetpwd', $code);

            if($result['status'] != 1){
                returnJson(-1, '验证码输入有误');
            }
            //修改密码
            M('users')->where("user_id", $user['user_id'])->save(array('password' => $password));
            $this->ajaxReturn(['status' => 1, 'msg' => '密码已重置,请重新登录']);
        }
    }

    /*
     * @api {GET}   index.php?m=Api&c=User&a=getAddressList    收货地址列表（待调试）
     * @apiName     getAddressList
     * @apiGroup    User
     *
     */
    public function getAddressList(){
        if(!$this->user_id){
            $this->ajaxReturn(array('status' => -1, 'msg' => '缺少参数'));
        }

        $address = M('user_address')->where(array('user_id' => $this->user_id))->select();
        if(!$address){
            $this->ajaxReturn(array('status' => 1, 'msg' => '没有数据', 'result' => []));
        }

        $regions = M('region')->cache(true)->getField('id,name');
        foreach($address as &$addr){
            $addr['province_name'] = $regions[$addr['province']] ?: '';
            $addr['city_name'] = $regions[$addr['city']] ?: '';
            $addr['district_name'] = $regions[$addr['district']] ?: '';
            $addr['twon_name'] = $regions[$addr['twon']] ?: '';
            $addr['address'] = $addr['address'] ?: '';
        }

        $this->ajaxReturn(array('status' => 1, 'msg' => '获取成功', 'result' => $address));
    }

    /*
     * @api {GET}   index.php?m=Api&c=User&a=addAddress    收货地址添加（待调试）wxx
     * @apiName     addAddress
     * @apiGroup    User
     *
     */
    public function addAddress(){
        //$user_id = I('user_id/d',0);
        if(!$this->user_id){
            exit(json_encode(array('status' => -1, 'msg' => '缺少参数', 'result' => '')));
        }
        $address_id = I('address_id/d', 0);
        $data = $this->userLogic->add_address($this->user_id, $address_id, I('post.')); // 获取用户信息
        exit(json_encode($data));
    }


    /*
     * @api {GET}   index.php?m=Api&c=User&a=del_address    收货地址删除（待调试）
     * @apiName     del_address
     * @apiGroup    User
     *
     */
    public function del_address(){
        $id = I('id/d');
        if(!$this->user_id){
            exit(json_encode(array('status' => -1, 'msg' => '缺少参数', 'result' => '')));
        }
        $address = M('user_address')->where("address_id", $id)->find();
        $row = M('user_address')->where(array('user_id' => $this->user_id, 'address_id' => $id))->delete();

        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if($address['is_default'] == 1){
            $address = M('user_address')->where("user_id", $this->user_id)->find();

            //@mobify by wangqh {
            if($address){
                M('user_address')->where("address_id", $address['address_id'])->save(array('is_default' => 1));
            }//@}

        }

        //@mobify by wangqh 
        if($row){
            exit(json_encode(array('status' => 1, 'msg' => '删除成功', 'result' => '')));
        }else{
            exit(json_encode(array('status' => 1, 'msg' => '删除失败', 'result' => '')));
        }
    }

    /*
     * @api {GET}   index.php?m=Api&c=User&a=setDefaultAddress    设置默认收货地址（待调试）
     * @apiName     setDefaultAddress
     * @apiGroup    User
     *
     */
    public function setDefaultAddress(){
        //        $user_id = I('user_id/d',0);
        if(!$this->user_id){
            exit(json_encode(array('status' => -1, 'msg' => '缺少参数', 'result' => '')));
        }
        $address_id = I('address_id/d', 0);
        $data = $this->userLogic->set_default($this->user_id, $address_id); // 获取用户信息
        if(!$data){
            exit(json_encode(array('status' => -1, 'msg' => '操作失败', 'result' => '')));
        }
        exit(json_encode(array('status' => 1, 'msg' => '操作成功', 'result' => '')));
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=User&a=getPackCouponList    得到优惠券列表 done 管少秋
     * @apiName     getPackCouponList
     * @apiGroup    User
     * @apiParam    {String}    token           token
     * @apiParam    {String}    model_type      模块类型 0为包车模块1为商城模块2为民宿模块
     * @apiParam    {String}    type            发放类型 0下单赠送 1 按用户发放 2 免费领取 3 线下发放
     * @apiParam    {Number}    [store_id]      传入包车模块所对应发放优惠券人的drv_id store_id home_id
     * @apiSuccessExample   {json}      Success-Response
     *  Http/1.1    200     OK
     * {
     * "status": 1,
     * "msg": "获取成功",
     * "result": [
     * {
     * "id": 63,
     * "cid": 25,
     * "type": 0,
     * "uid": 60,
     * "order_id": 0,
     * "get_order_id": null,
     * "use_time": 0,
     * "code": "",
     * "send_time": 1477566074,
     * "store_id": 1,
     * "status": 0,
     * "deleted": 0,
     * "drv_id": null,
     * "model_type": 0,
     * "home_id": null,
     * "name": "TPshop100元券",//满899减掉100
     * "use_type": 0,
     * "money": "100.00",
     * "use_start_time": 1477497600,
     * "use_end_time": 1536835755,
     * "condition": "899.00"
     * },
     * ]
     * }
     */
    public function getPackCouponList(){
        if(!$this->user_id){
            $this->ajaxReturn(['status' => -1, 'msg' => '还没登录', 'result' => '']);
        }

        $store_id = I('get.store_id', 0);
        $type = I('get.type', 0);
        $order_money = I('get.order_money', 0);
        $model_type = I('get.model_type', 1);
        $data = $this->userLogic->get_coupon($this->user_id, $type, null, 0, $store_id, $order_money, $model_type);
        unset($data['show']);
        if($model_type == 1){
            $coupon['limit_store'] = '商城专属';
        }elseif($model_type == 0){
            $coupon['limit_store'] = '包车专属';
        }elseif($model_type == 2){
            $coupon['limit_store'] = '房源专属';
        }
        $this->ajaxReturn($data);
    }

    public function getCouponList(){
        if(!$this->user_id){
            $this->ajaxReturn(['status' => -1, 'msg' => '还没登录', 'result' => '']);
        }

        $store_id = I('get.store_id', 0);
        $type = I('get.type', 0);
        $order_money = I('get.order_money', 0);

        $data = $this->userLogic->get_coupon($this->user_id, $type, null, 0, $store_id, $order_money);
        unset($data['show']);

        /* 获取各个优惠券的平台 */
        $coupon_list = &$data['result'];
        $store_id_arr = get_arr_column($coupon_list, 'store_id');
        $store_arr = M('store')->where('store_id', 'in', $store_id_arr)->getField('store_id,store_name,store_logo');
        foreach($coupon_list as &$coupon){
            if($coupon['store_id'] > 0){
                $coupon['limit_store'] = $store_arr[$coupon['store_id']]['store_name'];
            }else{
                $coupon['limit_store'] = '全平台';
            }
        }

        $this->ajaxReturn($data);
    }

    /**
     * 获取购物车指定店铺的优惠券
     */
    public function cart_coupons(){
        $store_id = I('store_id/d', 0);    //限制店铺
        $money = I('money/f', 0);        //限制金额

        $cartLogic = new CartLogic();
        $couponLogic = new CouponLogic();
        $cartLogic->setUserId($this->user_id);
        if($cartLogic->getUserCartOrderCount() == 0){
            $this->ajaxReturn(['status' => -1, 'msg' => '你的购物车没有选中商品']);
        }
        $cartList = $cartLogic->getCartList(1); // 获取用户选中的购物车商品

        $cartGoodsList = get_arr_column($cartList, 'goods');
        $cartGoodsId = get_arr_column($cartGoodsList, 'goods_id');
        $cartGoodsCatId = get_arr_column($cartGoodsList, 'cat_id3');
        //$storeCartList = $cartLogic->getStoreCartList($cartList);//转换成带店铺数据的购物车商品

        $userCouponList = $couponLogic->getUserAbleCouponList($this->user_id, $cartGoodsId, $cartGoodsCatId);//用户可用的优惠券列表

        $store_id_arr = get_arr_column($userCouponList, 'store_id');
        $store_arr = M('store')->where('store_id', 'in', $store_id_arr)->getField('store_id,store_name,store_logo');

        $returnCouponList = array();
        foreach($userCouponList as $k => $v){
            if($v['store_id'] == 0 || $v['store_id'] == $store_id){
                $coupon = $v['coupon'];

                if($coupon){
                    if($money == 0 || ($money > 0 && $coupon['condition'] < $money)){      //金额限制
                        $coupon['limit_store'] = $store_arr[$coupon['store_id']]['store_name'];
                        switch($coupon['use_type']){//0全店通用1指定商品可用2指定分类商品可用
                            case 0 :
                                $returnCoupon['limit_store'] = $coupon['limit_store'].'全店通用';
                                break;
                            case 1 :
                                $returnCoupon['limit_store'] = $coupon['limit_store'].'指定商品可用';
                                break;
                            case 2 :
                                $returnCoupon['limit_store'] = $coupon['limit_store'].'指定分类商品可用';
                                break;
                            case 3 :
                                $returnCoupon['limit_store'] = '全平台可用';
                                break;
                        }
                        $returnCoupon['id'] = $v['id'];
                        $returnCoupon['name'] = $coupon['name'];
                        $returnCoupon['money'] = $coupon['money'];
                        $returnCoupon['condition'] = $coupon['condition'];
                        $returnCoupon['use_start_time'] = $coupon['use_start_time'];
                        $returnCoupon['use_end_time'] = $coupon['use_end_time'];
                        $returnCoupon['store_id'] = $v['store_id'];
                        $returnCouponList[] = $returnCoupon;
                    }
                }
            }
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $returnCouponList]);
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=User&a=getGoodsCollect    我的收藏路线（待完成） 管少秋
     * @apiName     getGoodsCollect
     * @apiGroup    User
     *
     */
    public function getGoodsCollect(){
        $data = $this->userLogic->get_goods_collect($this->user_id);
        unset($data['show']);
        unset($data['page']);
        $this->ajaxReturn($data);
    }

    /*
     * 用户订单列表
     */
    public function getOrderList(){
        $type = I('type', '');
        $p = I('p', 1);
        if(!$this->user_id){
            $this->ajaxReturn(['status' => -1, 'msg' => '缺少参数', 'result' => '']);
        }

        $map = " deleted = 0 AND user_id = :user_id";
        $map = $type ? $map.C($type) : $map;

        $order_list = [];
        $order_obj = new \app\common\model\Order();
        $order_list_obj = $order_obj->order("order_id DESC")
            ->where($map)
            ->bind(['user_id' => $this->user_id])
            ->page($p, 10)
            ->select();
        if($order_list_obj){
            //转为数字，并获取订单状态，订单状态显示按钮，订单商品
            $order_list = collection($order_list_obj)->append([
                'order_status_detail',
                'order_button',
                'order_goods',
                'store'
            ])->toArray();
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $order_list]);
    }

    /**
     * 取消订单
     */
    public function cancelOrder(){
        $id = I('order_id/d');
        //        $user_id = I('user_id/d',0);
        $logic = new OrderLogic();
        if(!$this->user_id > 0 || !$id > 0){
            exit(json_encode(array('status' => -1, 'msg' => '参数有误', 'result' => '')));
        }
        $data = $logic->cancel_order($this->user_id, $id);
        exit(json_encode($data));
    }

    /**
     *  收货确认
     */
    public function orderConfirm(){
        $id = I('order_id/d', 0);
        //$user_id = I('user_id/d',0);
        if(!$this->user_id || !$id){
            exit(json_encode(array('status' => -1, 'msg' => '参数有误', 'result' => '')));
        }
        $data = confirm_order($id, $this->user_id);
        exit(json_encode($data));
    }


    /*
     *添加评论
     */
    public function add_comment(){
        $data['order_id'] = input('post.order_id/d', 0);
        $data['rec_id'] = input('post.rec_id/d', 0);
        $data['goods_id'] = input('post.goods_id/d', 0);
        $data['seller_score'] = input('post.service_rank', 0);   //卖家服务分数（0~5）(order_comment表)
        $data['logistics_score'] = input('post.deliver_rank', 0); //物流服务分数（0~5）(order_comment表)
        $data['describe_score'] = input('post.goods_rank', 0);  //描述服务分数（0~5）(order_comment表)
        $data['goods_rank'] = input('post.goods_score/d', 0);   //商品评价等级
        $data['is_anonymous'] = input('post.is_anonymous/d', 0);
        $data['content'] = input('post.content', '');
        $data['img'] = input('post.img/a', ''); //小程序需要
        $data['user_id'] = $this->user_id;

        $commentLogic = new CommentLogic;
        $return = $commentLogic->addGoodsAndServiceComment($data);

        $this->ajaxReturn($return);
    }

    /**
     * 提交服务评论
     */
    public function add_service_comment(){
        $order_id = I('post.order_id/d', 0);
        $service_rank = I('post.service_rank', 0);
        $deliver_rank = I('post.deliver_rank', 0);
        $goods_rank = I('post.goods_rank', 0);

        $store_id = M('order')->where(array('order_id' => $order_id))->getField('store_id');

        $commentLogic = new CommentLogic;
        $return = $commentLogic->addServiceComment($this->user_id, $order_id, $store_id, $service_rank, $deliver_rank, $goods_rank);

        $this->ajaxReturn($return);
    }

    /**
     * 上传头像
     */
    public function upload_headpic(){
        $userLogic = new UsersLogic();

        $return = $userLogic->upload_headpic(true);
        if($return['status'] !== 1){
            $this->ajaxReturn($return);
        }
        $post['head_pic'] = $return['result'];

        if(!$userLogic->update_info($this->user_id, $post)){
            $this->ajaxReturn(['status' => -1, 'msg' => '保存失败']);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => $post['head_pic']]);
    }

    /*
     * @api         {GET}   index.php?m=Api&c=User&a=account    我的钱包（待调试） wxx
     * @apiName     account
     * @apiGroup    User
     *
     */
    public function account(){

        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        // $user_id = I('user_id/d'); // 用户id
        //获取账户资金记录

        $data = $this->userLogic->get_account_log($this->user_id, I('get.type'));
        $account_log = $data['result'];
        exit(json_encode(array('status' => 1, 'msg' => '获取成功', 'result' => $account_log)));
    }

    /**
     * 申请退货状态
     */
    public function return_goods_status(){
        $rec_id = I('rec_id', '');

        $return_goods = M('return_goods')->where(['rec_id' => $rec_id])->where('status', 'in', '0,1')->find();

        //判断是否超过退货期
        $order = M('order')->where('order_id', $return_goods['order_id'])->find();
        $confirm_time_config = tpCache('shopping.auto_service_date');//后台设置多少天内可申请售后
        $confirm_time = $confirm_time_config*24*60*60;
        if($order && (time() - $order['confirm_time']) > $confirm_time && !empty($order['confirm_time'])){
            return ['result' => -1, 'msg' => '已经超过'.($confirm_time_config ?: 0)."天内退货时间"];
        }

        $return_id = $return_goods ? $return_goods['id'] : 0; //1代表可以退换货
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $return_id]);
    }

    /**
     * 获取收藏店铺列表集合, 只用于查询用户收藏的店铺, 页面判断用, 区别于getUserCollectStore
     */
    public function getCollectStoreData(){
        $where = array('user_id' => $this->user_id);
        $storeCollects = M('store_collect')->where($where)->select();
        $json_arr = array('status' => 1, 'msg' => '获取成功', 'result' => $storeCollects);
        exit(json_encode($json_arr));
    }

    /**
     * @author dyr
     * 获取用户收藏店铺列表
     */
    public function getUserCollectStore(){
        $page = I('page', 1);
        $storeLogic = new StoreLogic();
        $store_list = $storeLogic->getUserCollectStore($this->user_id, $page, 10);
        $json_arr = array('status' => 1, 'msg' => '获取成功', 'result' => $store_list);
        exit(json_encode($json_arr));
    }

    /*
     * @api {GET}   /index.php/Api/User/_list    提现列表（待调试） wxx
     * @apiName     withdrawals_list
     * @apiGroup    User
     *
     */
    public function withdrawals_list(){
        $is_json = I('is_json', 0); //json数据请求
        $withdrawals_where['user_id'] = $this->user_id;
        $count = M('withdrawals')->where($withdrawals_where)->count();
        $pagesize = C('PAGESIZE') == 0 ? 10 : C('PAGESIZE');
        $page = new Page($count, $pagesize);
        $list = M('withdrawals')
            ->where($withdrawals_where)
            ->order("id desc")
            ->limit("{$page->firstRow},{$page->listRows}")
            ->select();

        if($is_json){
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $list]);
        }

        $this->assign('page', $page->show());// 赋值分页输出
        $this->assign('list', $list); // 下线
        if(I('is_ajax')){
            return $this->fetch('ajax_withdrawals_list');
        }
        return $this->fetch();
    }

    /**
     * @api         {POST}   /index.php?m=Api&c=User&a=withdrawals    我的钱包-申请提现（todo） wxx
     * @apiName     withdrawals
     * @apiGroup    User
     *
     */
    public function withdrawals(){
        $data = I('post.');
        if(!capache([], SESSION_ID, $data['verify_code'])){
            $this->ajaxReturn(['status' => -1, 'msg' => "验证码错误"]);
        }

        $data['user_id'] = $this->user_id;
        $data['create_time'] = time();
        $distribut_min = tpCache('basic.min'); // 最少提现额度
        $distribut_need = tpCache('basic.need'); //满多少才能提
        if($data['money'] < $distribut_min){
            $this->ajaxReturn(['status' => -1, 'msg' => '每次最少提现额度'.$distribut_min]);
        }
        if($data['money'] > $this->user['user_money']){
            $this->ajaxReturn(['status' => -1, 'msg' => "你最多可提现{$this->user['user_money']}账户余额."]);
        }
        if($this->user['user_money'] < $distribut_need){
            $this->ajaxReturn(['status' => -1, 'msg' => '账户余额最少达到'.$distribut_need.'才能提现']);
        }

        $withdrawal = M('withdrawals')->where(array('user_id' => $this->user_id, 'status' => 0))->sum('money');
        if($this->user['user_money'] < ($withdrawal + $data['money'])){
            $this->ajaxReturn(['status' => -1, 'msg' => '您有提现申请待处理，本次提现余额不足']);
        }
        if(M('withdrawals')->add($data)){
            $bank['bank_name'] = $data['bank_name'];
            $bank['bank_card'] = $data['account_bank'];
            $bank['realname'] = $data['account_name'];
            M('users')->where(array('user_id' => $this->user_id))->save($bank);
            $json_arr = array('status' => 1, 'msg' => '提交成功');
        }else{
            $json_arr = array('status' => -1, 'msg' => '提交失败,联系客服!');
        }
        $this->ajaxReturn($json_arr);
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=User&a=points    我的钱包-账号明细（todo） wxx
     * @apiName     points
     * @apiGroup    User
     *
     */
    public function points(){
        $type = I('type', 'all');
        $usersLogic = new UsersLogic;
        $result = $usersLogic->points($this->user_id, $type);

        $json_arr = ['status' => 1, 'msg' => '获取成功', 'result' => $result['account_log']];
        exit(json_encode($json_arr));
    }


    /**
     * @api         {GET}   /index.php/api/user/recharge  我的钱包-充值（doing） wxx
     * @apiDescription  用户充值获取调起支付需要的参数
     * @apiName     recharge
     * @apiGroup    User
     * @apiParam  {string} token    token.
     * @apiParam  {string=wx,zfb} payWay    支付方式.
     * @apiParam  {number{0.01-10000}} amount  充值金额.
     * @apiSuccess {String} xx xxx.
     */
    public function recharge(){
        $reqParams = $this->getReqParams(['payWay','amount']);
        $rule =[
            'payWay'=>'in:wx,zfb',
            'amount'=>'between:0.01,10000'
        ];
        $this->validateParams($reqParams,$rule);
        $userLogic = new  UserLogic();
        return $this->returnJson($userLogic->getRechargeParams($reqParams,$this->user));
    }

    /**
     * @api         {GET}   /index.php/api/user/recharge  我的钱包-充值（todo） wxx
     * @apiDescription  用户充值获取调起支付需要的参数
     * @apiName     recharge
     * @apiGroup    User
     * @apiParam  {string=wx,zfb} payWay    支付方式.
     * @apiParam  {number{0.01-10000}} amount  充值金额.
     * @apiSuccess {String} xx xxx.
     *
     */
    public function withdraw(){
        returnJson(2000);
    }


    /**
     * 验证码获取
     */
    public function verify(){
        $type = I('get.type') ?: SESSION_ID;
        $is_image = I('get.is_image', 0);
        if(!$is_image){
            $result = capache([], $type);
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
        }

        $config = array(
            'fontSize' => 30,
            'length' => 4,
            'imageH' => 60,
            'imageW' => 300,
            'fontttf' => '5.ttf',
            'useCurve' => true,
            'useNoise' => false,
            'length' => 4,
        );
        $Verify = new \think\Verify($config);
        $Verify->entry($type);
        exit;
    }

    /**
     * 评论列表
     */
    public function comment(){
        $status = I('get.status', 0);
        $logic = new CommentLogic;
        $result = $logic->getComment($this->user_id, $status);

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result['result']]);
    }

    /**
     * 服务评论列表
     */
    public function service_comment(){
        $p = input('p', 1);
        $logic = new CommentLogic;
        $result = $logic->getServiceComment($this->user_id, $p);

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
    }

    public function comment_num(){
        $logic = new CommentLogic;
        $result = $logic->getAllTypeCommentNum($this->user_id);

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
    }

    /**
     * 浏览记录
     */
    public function visit_log(){
        $p = I('get.p', 1);

        $user_logic = new UsersLogic;
        $visit_list = $user_logic->visit_log($this->user_id, $p);

        $list = [];
        foreach($visit_list as $k => $v){
            $list[] = ['date' => $k, 'visit' => $v];
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $list]);
    }

    /**
     * 删除浏览记录
     */
    public function del_visit_log(){
        $visit_ids = I('get.visit_ids', 0);
        $row = M('goods_visit')->where('visit_id', 'IN', $visit_ids)->delete();
        if(!$row){
            $this->ajaxReturn(['status' => -1, 'msg' => '删除失败']);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
    }

    /**
     * 清空浏览记录
     */
    public function clear_visit_log(){
        $row = M('goods_visit')->where('user_id', $this->user_id)->delete();
        if(!$row){
            $this->ajaxReturn(['status' => -1, 'msg' => '删除失败']);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
    }

    /**
     *  获取用户消息通知
     */
    public function message_notice(){
        $messageModel = new \app\common\logic\MessageLogic;
        $messages = $messageModel->getUserPerTypeLastMessage();

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $messages]);
    }

    /**
     * 获取消息
     */
    public function message(){
        $p = I('get.p', 1);
        $category = I('get.category', 0);

        $messageModel = new \app\common\logic\MessageLogic;
        $message = $messageModel->getUserMessageList($this->user_id, $category, $p);

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $message]);
    }

    /**
     * 消息开关
     */
    public function message_switch(){
        if(!$this->user){
            $this->ajaxReturn(['status' => -1, 'msg' => '用户不存在']);
        }

        $messageModel = new \app\common\logic\MessageLogic;

        if(request()->isGet()){
            /* 获取消息开关 */
            $notice = $messageModel->getMessageSwitch($this->user['message_mask']);
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $notice]);
        }elseif(request()->isPost()){
            /* 设置消息开关 */
            $type = I('post.type/d', 0); //开关类型
            $val = I('post.val', 0); //开关值
            $return = $messageModel->setMessageSwitch($type, $val, $this->user);
            $this->ajaxReturn($return);
        }

        $this->ajaxReturn(['status' => -1, 'msg' => '请求方式错误']);
    }

    /**
     * 清除消息
     */
    public function clear_message(){
        if(!$this->user_id){
            $this->ajaxReturn(['status' => -1, 'msg' => '用户不存在']);
        }

        $messageModel = new \app\common\logic\MessageLogic;
        $messageModel->setMessageRead($this->user_id);

        $this->ajaxReturn(['status' => 1, 'msg' => '清除成功']);
    }

    /**
     * 账户明细列表网页
     * @return type
     */
    public function account_list(){
        $type = I('type', 'all');
        $is_json = I('is_json', 0); //json数据请求
        $usersLogic = new UsersLogic;
        $result = $usersLogic->account($this->user_id, $type);

        if($is_json){
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result['account_log']]);
        }

        $this->assign('type', $type);
        $showpage = $result['page']->show();
        $this->assign('account_log', $result['account_log']);
        $this->assign('page', $showpage);
        if(I('is_ajax')){
            return $this->fetch('ajax_acount_list');
        }
        return $this->fetch();
    }

    /**
     * 积分类别网络
     * @return type
     */
    public function points_list(){
        $type = I('type', 'all');
        $is_json = I('is_json', 0); //json数据请求
        $usersLogic = new UsersLogic;
        $result = $usersLogic->points($this->user_id, $type);

        if($is_json){
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result['account_log']]);
        }

        $this->assign('type', $type);
        $showpage = $result['page']->show();
        $this->assign('account_log', $result['account_log']);
        $this->assign('page', $showpage);
        if(I('is_ajax')){
            return $this->fetch('ajax_points');
        }
        return $this->fetch();
    }

    /**
     * 物流网页
     * @return type
     */
    public function express(){
        $is_json = I('is_json', 0);
        $order_id = I('get.order_id/d', 0);
        $order_goods = M('order_goods')->where("order_id", $order_id)->select();
        $delivery = M('delivery_doc')->where("order_id", $order_id)->limit(1)->find();
        if($is_json){
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $delivery]);
        }
        $this->assign('order_goods', $order_goods);
        $this->assign('delivery', $delivery);
        return $this->fetch();
    }

    /**
     * 获取全部地址信息, 从BaseController移入到UserController @modify by wangqh.
     */
    public function allAddress(){
        $data = M('region')->where('level < 4')->select();
        $json_arr = array('status' => 1, 'msg' => '成功!', 'result' => $data);
        $json_str = json_encode($json_arr);
        exit($json_str);
    }

    /**
     * 关于我们页面
     */
    public function about_us(){
        return $this->fetch();
    }

    /**
     * 检查token状态
     */
    public function token_status(){
        $token = I('token/s', '');
        $return = $this->getUserByToken($token);
        if($return['status'] == 1){
            $return['result'] = '';
        }
        $this->ajaxReturn($return);
    }

    /**
     * 上传评论图片，小程序图片只能一张一张传
     */
    public function upload_comment_img(){
        $logic = new \app\common\logic\CommentLogic;
        $img = $logic->uploadCommentImgFile('comment_img_file');

        if($img['status'] === 1){
            $img['result'] = implode(',', $img['result']);
        }

        $this->ajaxReturn($img);
    }

    /**
     * 消息列表（小程序临时接口by lhb）
     * @author dyr
     * @time   2016/09/01
     */
    public function message_list(){
        $type = I('type', 0);
        $user_logic = new UsersLogic();
        $message_model = new \app\common\logic\MessageLogic();
        if($type == 1){
            //系统消息
            $user_sys_message = $message_model->getUserMessageNotice();
            //$user_logic->setSysMessageForRead();
        }else if($type == 2){
            //活动消息：后续开发
            $user_sys_message = array();
        }else{
            //全部消息：后续完善
            $user_sys_message = $message_model->getUserMessageNotice();
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $user_sys_message]);
    }


}
