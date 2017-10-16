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

use app\api\logic\DynamicLogic;
use app\api\logic\StrategyLogic;
use app\api\logic\UserLogic;
use app\api\logic\WithdrawalsLogic;
use app\common\logic\CartLogic;
use app\common\logic\CommentLogic;
use app\common\logic\CouponLogic;
use app\common\logic\OrderLogic;
use app\common\logic\PackCarProductLogic;
use app\common\logic\StoreLogic;
use app\common\logic\UserCollectLogic;
use app\common\logic\UsersLogic;
use service\MsgService;
use think\Page;
use think\Request;

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
     * @api         {POST}  /index.php?m=Api&c=User&a=thirdLogin     第三方登录done 管少秋
     * @apiName     thirdLogin
     * @apiGroup    User
     * @apiParam    {String}      openid     第三方唯一标识
     * @apiParam    {String}      from       来源 wx weibo alipay
     * @apiParam    {String}      nickname    第三方返回昵称
     * @apiParam    {String}      head_pic    头像路径
     * @apiParam    {Number}      sex         性别  0=未知 1=男 2=女
     * @apiSuccessExample   {json}  Success-response
     *      Http/1.1    200 Ok
     * {
     *  "status": 1,
     *  "msg": "登陆成功",
     *  "result": {
     *  "user_id": "12",
     *  "email": "",
     *  "password": "",
     *  "sex": "0",
     *  "birthday": "0000-00-00",
     *  "user_money": "0.00",
     *  "frozen_money": "0.00",
     *  "pay_points": "0",
     *  "address_id": "0",
     *  "reg_time": "1452331498",
     *  "last_login": "0",
     *  "last_ip": "",
     *  "qq": "",
     *  "mobile": "",
     *  "mobile_validated": "0",
     *  "oauth": "wx",
     *  "openid": "2",
     *  "head_pic": null,
     *  "province": "0",
     *  "city": "0",
     *  "district": "0",
     *  "email_validated": "0",
     *  "nickname": ""
     *  }
     *  }
     * @apiErrorExample     {json}  Error-response
     *               Http/1.1    200 OK
     *    {
     *  "status": -1,
     *  "msg": "参数有误",
     *  "result": ""
     *  }
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
     * @api         {POST}  /index.php?m=Api&c=User&a=bindPhone     token绑定手机done  管少秋
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
     * @api         {POST}  /Api/User/thirdBindPhone     第三方登录绑定手机 ok  wxx
     * @apiName     thirdBindPhone
     * @apiGroup    User
     * @apiParam    {String}    openid     第三方唯一标识
     * @apiParam    {String}    from       同第三方登录接口
     * @apiParam    {String}    mobile      绑定手机号
     * @apiParam    {String}    countroy_code   绑定国家的区号
     * @apiParam    {String}    code        绑定手机code
     */
    public function thirdBindPhone(){
        //看该手机号是否被别人绑定
        $reqParams = $this->getReqParams(['openid', 'from', 'mobile', 'countroy_code', 'code']);
        $rule = [
            'openid' => 'require|max:100',
            'from' => 'require|max:10',
            'mobile' => 'require|max:20',
            'countroy_code' => 'require|max:5',
            'code' => 'require|max:8',
        ];
        $this->validateParams($reqParams, $rule);

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

        $thirdUser = get_user_info($reqParams['openid'], 3, $reqParams['from']);
        if(empty($thirdUser)){
            return $this->returnJson(4004, '绑定失败,无效的第三方用户');
        }

        $thirdUser->mobile = $mobile;
        $thirdUser->mobile_validated = 1;

        if(!$thirdUser->save()){
            return $this->returnJson(5020, '绑定失败');
        }
        return $this->returnJson(2000, '绑定成功');
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
            'email' => $mail,
            'email_validated' => 1
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
     * "paypoint": "0.00",
     * "shz_code" :         //傻孩子号
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
     * "user_id": 146,      //用户ID
     * "email": "",         //用户邮箱
     * "password": "90600d68b0f56d90c4c34284d8dfd138",
     * "sex": 0,            //用户性别 0 保密 1 男 2 女
     * "birthday": 0,       //用户生日
     * "birthday": 0,       //用户生日
     * "user_money": "0.00",    //用户余额
     * "frozen_money": "0.00",  //冻结金额（忽略）
     * "distribut_money": "0.00",   //累积分佣金额（忽略）
     * "pay_points": "0.0000",      //消费积分（忽略）
     * "address_id": 0,             //（忽略）
     * "reg_time": 1504596640,      //注册时间
     * "last_login": 1504602255,    //最后登录时间
     * "last_ip": "",
     * "qq": "",                    //qq
     * "mobile": "18451847701",     //手机
     * "mobile_validated": 1,       //手机是否验证过了  1验证过了
     * "oauth": "",                 //第三方来源 wx weibo alipay
     * "openid": null,              //第三方唯一标示
     * "unionid": null,
     * "head_pic": null,            //头像
     * "province": 0,               //省
     * "city": 0,                   //市
     * "district": 0,               //区
     * "email_validated": 0,        //邮箱是否验证过了
     * "nickname": "18451847701",   //用户名
     * "level": 1,                  //级别
     * "discount": "1.00",          //会员折扣，默认1不享受
     * "total_amount": "0.00",      //消费累计额度
     * "is_lock": 0,                //是否被锁定冻结（忽略）
     * "is_distribut": 1,           //忽略
     * "first_leader": 0,           //忽略
     * "second_leader": 0,          //忽略
     * "third_leader": 0,           //忽略
     * "fourth_leader": null,       //忽略
     * "fifth_leader": null,
     * "sixth_leader": null,
     * "seventh_leader": null,
     * "token": "a279c833cebe5fb963ccba311e27c394",     //token
     * "address": null,                             //忽略
     * "pay_passwd": null,                          //忽略
     * "pre_pay_points": "0.0000",
     * "optional": "0.0000",
     * "vipid": 0,              //VIPID
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
     * "cart_goods_num": 0,
     * "shz_code" :         ,//傻孩子号
     * "fans_num" :         ,//粉丝数
     * "attention_num" :         ,//关注
     * "good_num" :         ,//被赞数
     * "collection_num" :         ,//被收藏数
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
     * @apiParam    {String}    is_update_address     是否修改省市区   0 不修改   1   修改
     * @apiParam    {String}    [province]      省份
     * @apiParam    {String}    [city]          城市
     * @apiParam    {String}    [district]      地区
     * @apiParam    {String}    [personalized_signature]      个性签名
     * @apiParam    {String}    [shz_code]      傻孩子号
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
            if(in_array(I('post.sex'), [0, 1, 2])){
                $post['sex'] = I('post.sex');
            }
            I('post.birthday') ? $post['birthday'] = strtotime(I('post.birthday')) : false;  // 生日
            I('post.personalized_signature') ? $post['personalized_signature'] = I('post.personalized_signature') : false;  // 个性签名
            if(I('post.is_update_address')){
                $post['province'] = I('post.province');  //省份
                $post['city'] = I('post.city');  // 城市
                $post['district'] = I('post.district');  //地区
            }

            I('post.email') ? $post['email'] = I('post.email') : false;
            I('post.mobile') ? $post['mobile'] = I('post.mobile') : false;
            if(I('post.shz_code')){
                //修改傻孩子号
                $result = update_shz_code($this->user_id, I('post.shz_code'));
                $this->ajaxReturn($result);
            }
            //dump($post);die;
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
     * @apiParam    {String}    model_type      模块类型 0为包车模块1为商城模块2为民宿模块 全部为all
     * @apiParam    {String}    type            0:未使用，1:已使用，2:已过期
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
        }elseif($model_type == 'all'){
            $coupon['limit_store'] = '';
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
     * @api         {POST}   /index.php?m=Api&c=User&a=getGoodsCollect    我的收藏路线 done 管少秋
     * @apiName     getGoodsCollect
     * @apiGroup    User
     * @apiParam    {String}    token       token
     * @apiParam    {Number}    model_type  模块类型 0为包车模块1为商城模块2为民宿模块
     */
    public function getGoodsCollect(){
        $info = input();
        if($info['model_type'] != 0){
            $data = $this->userLogic->get_goods_collect($this->user_id, -1, I('model_type', 1));
            unset($data['show']);
            unset($data['page']);
        }else{
            $data = $this->userLogic->get_lines_collect($this->user_id);
        }
        $this->ajaxReturn($data);
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=User&a=collectLine   收藏取消路线操作
     * @apiName     collectLine
     * @apiGroup    User
     * @apiParam    {String}    token   token
     * @apiParam    {String}    line_id 路线ID
     * @apiParam    {String}    action  收藏collect  取消cancel
     */
    public function collectLine(){
        $data = input();
        $where = [
            'user_id' => $this->user_id,
            'goods_id' => $data['line_id'],
            'model_type' => 0,
        ];
        $count = M('goods_collect')->where($where)->count();
        if($data['action'] == 'collect'){
            if($count > 0){
                $this->ajaxReturn(['status' => -1, 'msg' => '您已经收藏过该路线了']);
            }
            $add = [
                'user_id' => $this->user_id,
                'goods_id' => $data['line_id'],
                'cat_id3' => 0,
                'add_time' => time(),
                'model_type' => 0,
            ];
            $result = M('goods_collect')->save($add);
            if(!$result){
                $this->ajaxReturn(['status' => -1, 'msg' => '失败']);
            }
            $this->ajaxReturn(['status' => 1, 'msg' => '成功']);
        }else{
            if($count > 0){
                $result = M('goods_collect')->where($where)->delete();
                $this->ajaxReturn(['status' => 1, 'msg' => '成功']);
            }
            $this->ajaxReturn(['status' => -1, 'msg' => '取消收藏失败']);
        }
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
     * @api             {GET}   /index.php?m=api&c=user&a=recharge  11.钱包-充值 !暂停 wxx
     * @apiDescription  用户充值获取调起支付需要的参数
     * @apiName         recharge
     * @apiGroup        User
     * @apiParam  {string} token    token.
     * @apiParam  {string=wx,zfb} payWay    支付方式.
     * @apiParam  {number{0.01-10000}} amount  充值金额.
     * @apiSuccess {string} aliPayParams 调起支付宝支付需要的参数.
     * @apiSuccess {object} wxPayParams 调起支付宝支付需要的参数.
     * @apiSuccessExample {json} 成功获取支付宝参数:
     *  {
     *      "status": 1,
     *      "msg": "SUCCESS",
     *      "result": {
     *          "aliPayParams": "app_id=2017052207306091&biz_content=%7B%22subject%22%3A%22TODO%5Cu777f%5Cu9014%5Cu79d1%5Cu6280%5C%2F%5Cu6d41%5Cu91cf%5Cu8fbe%5Cu4eba%22%2C%22out_trade_no%22%3A%22RC6319892017091414514151080%22%2C%22product_code%22%3A%22QUICK_MSECURITY_PAY%22%2C%22timeout_express%22%3A%2290m%22%2C%22total_amount%22%3A%221989%22%2C%22body%22%3A%5B%7B%22userId%22%3A63%2C%22amount%22%3A%221989%22%2C%22orderSn%22%3A%22RC6319892017091414514151080%22%7D%5D%7D&charset=UTF-8&format=json&method=alipay.trade.app.pay&notify_url=http%3A%2F%2Fshz.api.user.ruitukeji.dev%2Findex.php%2FApi%2FCallback%2Falipay&prod_code=&sign=iPiIDB6Uy3aYwIXyhCNFxvt3fWDrA52V%2B614pSqad3FX7uFraLvPBzz9r%2BAta5nKStvOJWUWDID6dlSCNPs3at7kuPOHtGQjWQV9rdp1%2FnI%2FMjNsbJogIi31imQe07P9lIl93WXfBPnl%2FwdP44d8gueMgcZM8lCXgo6njz1bKkQ%3D&sign_type=RSA&timestamp=2017-09-14+14%3A51%3A41&version=1.0"
     *      }
     *  }
     *
     * @apiSuccessExample {json} 成功获取微信参数:
     *   {
     *       "status": 1,
     *       "msg": "SUCCESS",
     *       "result":{
     *           "wxPayParams":{
     *               "appid": "wxd8d4b0dc3305513a",
     *               "noncestr": "FOylukKq9knth3Pu",
     *               "package": "Sign=WXPay",
     *               "partnerid": "1480848402",
     *               "prepayid": "wx201709141507312e02bb88bf0986708106",
     *               "timestamp": 1505372851,
     *               "sign": "52F9C44B2AB1F25FE0DC0C85947CB780",
     *               "jsConfig": {
     *                   "appId": "wxd8d4b0dc3305513a",
     *                   "nonceStr": "d4xwft7qene3qz0mqdrgx8vo4gza8y5c",
     *                   "package": "prepay_id=wx201709141507312e02bb88bf0986708106",
     *                   "signType": "MD5",
     *                   "timeStamp": "1505372851",
     *                   "sign": "2FD9846FF5C194884DBCAE04AD2A0616",
     *                   "paySign": "2FD9846FF5C194884DBCAE04AD2A0616"
     *               }
     *          }
     *      }
     *   }
     *
     */
     public function recharge(Request $request){
         if(!$request->isGet()){
             return $this->returnJson();
         }
         $reqParams = $this->getReqParams(['payWay','amount']);
         $rule =[
             'payWay'=>'require|in:wx,zfb',
             'amount'=>'require|between:0.01,100000000'
         ];
         $this->validateParams($reqParams,$rule);
         $userLogic = new  UserLogic();
         return $this->returnJson($userLogic->getRechargeParams($reqParams,$this->user));
     }


    /**
     * @api         {POST}   /index.php?m=Api&c=User&a=withdrawals    12.钱包-申请提现 ok wxx
     * @apiName     withdrawals
     * @apiGroup    User
     * @apiParam  {string} token    token.
     * @apiParam  {number}  amount   提现金额 应当小于等于用户余额.
     * @apiParam  {string=zfb,wx,bank}  withdrawalsWay 提现方式 zfb=支付宝,wx=微信,bank=银行汇款.
     * @apiParam  {string}  account 提现账户.
     * @apiParam  {string}  person  提现人姓名.
     * @apiParam  {string}  bankName   提现银行  当提现方式为银行汇款时此项必填.
     * @apiParam  {string}  bankOfDeposit 提现人开户行 当提现方式为银行汇款时此项必填.
     * @apiParam  {string}  [phone] 提现人联系电话.
     *
     * @apiSuccess  {string}  amount 提现金额.
     * @apiSuccess  {string}  balance 账号余额.
     *
     * @apiSuccessExample {json} 提交成功
     *   {
     *       "status": 1,
     *       "msg": "提交成功",
     *           "result": {
     *               "balance": "5.00"
     *           }
     *   }
     *
     * @apiSuccessExample {json} 提交失败
     *   {
     *       "status": -1,
     *       "msg": "您有提现申请在处理中。如有疑问请联系客服。",
     *       "result": {}
     *   }
     *
     */
    public function withdrawals(Request $request){
        if(!$request->isPost()){
            return $this->returnJson();
        }

        $reqParams = $this->getReqParams(['amount', 'withdrawalsWay', 'account', 'person', 'bankOfDeposit', 'phone']);
        $userBlance = $this->user['user_money'];
        $rule = [
            'amount' => "require|between:0.01,$userBlance",
            'withdrawalsWay' => "require|in:wx,zfb,bank",
            'account' => "require",
            'person' => "require",
        ];
        $this->validateParams($reqParams, $rule);
        if($reqParams['withdrawalsWay'] == 'bank' && (empty($reqParams['bankName']) || empty($reqParams['bankOfDeposit']))){
            return $this->returnJson(4001, '开户行必须填写。');
        }
        $withdrawalsLogic = new WithdrawalsLogic();
        return $this->returnJson($withdrawalsLogic->applyWithdrawals($reqParams, $this->user));
    }

    /**
     * @api             {GET}   /index.php?m=Api&c=User&a=accountLog   13.钱包-明细 ok wxx
     * @apiDescription  我的钱包 获取当前登录用的帐号明显 时间倒序排列
     * @apiName         accountLog
     * @apiGroup        User
     * @apiParam  {string} token    token.
     * @apiParam  {number} [startTime]    起始时间 时间戳.
     * @apiParam  {number} [endTime=当前时间]      结束时间 时间戳.
     * @apiParam  {number=0,1,2,3,4} [type=0]         类型 0=全部 1=充值 2=提现 3=消费 4=退款.
     * @apiParam  {number} [p=1]        页码.
     * @apiParam  {number} [pageSize=20]   每页数据量.
     *
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list    列表.
     * @apiSuccess {number} list.id  id.
     * @apiSuccess {number} list.type 类型.
     * @apiSuccess {string} list.typeName 类型名称.
     * @apiSuccess {number} list.timeStamp 变动时间戳.
     * @apiSuccess {string} list.timeFmt 格式化的变动时间.
     * @apiSuccess {string} list.changeMoney   变动金额 带+/-.
     * @apiSuccess {string} list.userBalance   用户余额.
     * @apiSuccess {string} list.remark   备注.
     * @apiSuccess {string} list.orderSn   订单号.
     *
     * @apiSuccessExample {json} SUCCESS
     *   {
     *       "status": 1,
     *       "msg": "SUCCESS",
     *       "result": {
     *           "p": 1,
     *           "totalPages": 5,
     *           "list": [
     *               {
     *                   "changeMoney": "+0.01",
     *                   "id": 299,
     *                   "orderSn": "RC632017091618575865858",
     *                   "remark": "充值",
     *                   "timeFmt": "2017.09.16",
     *                   "timeStamp": 1505559504,
     *                   "type": 1,
     *                   "typeName": "充值",
     *                   "userBalance": "6.05"
     *               },
     *               {
     *                   "changeMoney": "+0.01",
     *                   "id": 298,
     *                   "orderSn": "RC632017091616350873728",
     *                   "remark": "充值",
     *                   "timeFmt": "2017.09.16",
     *                   "timeStamp": 1505555947,
     *                   "type": 1,
     *                   "typeName": "充值",
     *                   "userBalance": "0.00"
     *               }
     *           ]
     *       }
     *   }
     *
     *
     */
    public function accountLog(Request $request){
        if(!$request->isGet()){
            return $this->returnJson();
        }
        $reqParams = $this->getReqParams(['startTime', 'endTime' => time(), 'type' => 0]);
        $rule = [
            'type' => 'require|in:0,1,2,3,4'
        ];
        $this->validateParams($reqParams, $rule);
        $userLogic = new UserLogic();
        return $this->returnJson($userLogic->getUserAccountLogPageByTimeAndType($reqParams, $this->user));
    }


    public function dynamic(Request $request){
        if($request->isGet()){
            return $this->getDynamicList($request);
        }
        if($request->isPost()){
            return $this->postDynamic($request);
        }
        if($request->isDelete()){
            return $this->deleteDynamic($request);
        }
        return $this->returnJson();
    }


    /**
     * @api             {POST}   /index.php?m=Api&c=User&a=dynamic   20.发布动态 ok wxx
     * @apiDescription  发布动态
     * @apiName         postDynamic
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} img    封面图片 多张用“|” 分割 ，第一张为默认封面.
     * @apiParam {string} title  标题.
     * @apiParam {string} content 内容.
     *
     */
    private function postDynamic($request){

        $reqParams = $this->getReqParams(['img', 'title', 'content']);
        $rule = [
            'img' => 'require|max:1000',
            'title' => 'require|max:200',
            'content' => 'require|max:1000',
        ];
        $this->validateParams($reqParams, $rule);
        $dynamicLogic = new DynamicLogic();
        return $this->returnJson($dynamicLogic->createDynamic($reqParams, $this->user));

    }


    /**
     * @api             {GET}   /index.php?m=Api&c=User&a=getDynamicDetail   21.动态详情 ok wxx
     * @apiDescription  动态详情
     * @apiName         getDynamicDetail
     * @apiGroup        User
     * @apiParam  {string} [token]    token.
     *
     * @apiSuccess {number} id     id.
     * @apiSuccess {string} img    图片 多张用“|” 分割.
     * @apiSuccess {string} title  标题.
     * @apiSuccess {string} subTitle 副标题.
     * @apiSuccess {string} content 内容.
     * @apiSuccess {number} timeStamp  发布时间戳.
     * @apiSuccess {string} timeFmt    格式化发布时间.
     * @apiSuccess {number} praiseNum  点赞数量.
     * @apiSuccess {number} collectNum 收藏数量.
     * @apiSuccess {number} readNum    阅读量.
     * @apiSuccess {number} isCollect  是否收藏.
     * @apiSuccess {number} isPraise   是否点赞.
     * @apiSuccess {number} ownerId    发布人id.
     * @apiSuccess {number} ownerName  发布人昵称.
     * @apiSuccess {number} ownerAvatar 发布人头像.
     *
     * @apiSuccessExample {json} SUCCESS
     *  {
     *      "status": 1,
     *      "msg": "SUCCESS",
     *      "result": {
     *                  "id": 13,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "content": "简介：全人类的命运掌握在一小搓勇敢的战士手上，他们驾驶最先进的战斗机，对抗邪恶的侵略力量，保卫自由的太阳系，为未来而战。但是，当盟友变成敌人而敌人成为盟友时，战线和忠诚已经模糊不清，人类开始考虑如何自救。点评：本片由《辛普森一家》动画片的导演尼尔森-辛执导，属于香港和美国合拍的制作，真人和3D结合，美国特效和香港功夫结合，算是有潜力可挖。香港老戏骨刘家辉也亲自上阵，但影片隐隐约约中还能让人感觉到一丝浓浓的山寨气。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。这部魔幻风格的偶像剧把过往电影中的这部魔幻风格的偶像剧把过往电影中的魔幻符号一网打尽，也是最适合接班《饥饿游戏》的电影，但在北美和香港，该片的票房和口碑都只能说一般。",
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729853,
     *                  "timeFmt": "2017.09.18"
     *                  "isCollect": 0,
     *                  "ownerId": 1,
     *                  "ownerName": "",
     *                  "ownerAvatar": ""
     *              }
     *          ]
     *      }
     *  }
     *
     */
    public function getDynamicDetail(Request $request){
        if(!$request->isGet()){
            return $this->returnJson();
        }
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $this->checkToken();
        $dynamicLogic = new DynamicLogic();
        return $this->returnJson($dynamicLogic->getDynamicDetailWithUserId($id, $this->user_id));
    }


    /**
     * @api             {GET}   /index.php?m=Api&c=User&a=dynamic   22.我的动态列表 ok wxx
     * @apiDescription  获取当前用户的动态列表 时间倒序排列
     * @apiName         getDynamicList
     * @apiGroup        User
     * @apiParam  {string} token    token.
     * @apiParam  {number} [p=1]        页码.
     * @apiParam  {number} [pageSize=20]   每页数据量.
     *
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list         列表.
     * @apiSuccess {number} list.id     id.
     * @apiSuccess {string} list.img    封面图片.
     * @apiSuccess {string} list.title  标题.
     * @apiSuccess {string} list.subTitle 副标题.
     * @apiSuccess {number} list.timeStamp  发布时间戳.
     * @apiSuccess {string} list.timeFmt    格式化发布时间.
     * @apiSuccess {number} list.praiseNum  点赞量.
     * @apiSuccess {number} list.readNum  阅读量.
     *
     *
     * @apiSuccessExample {json} SUCCESS
     *  {
     *      "status": 1,
     *      "msg": "SUCCESS",
     *      "result": {
     *          "p": 1,
     *          "totalPages": 4,
     *          "list": [
     *              {
     *                  "id": 13,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729853,
     *                  "timeFmt": "2017.09.18"
     *              },
     *              {
     *                  "id": 12,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729850,
     *                  "timeFmt": "2017.09.18"
     *              }
     *          ]
     *      }
     *  }
     *
     */
    private function getDynamicList($request){
        $dynamicLogic = new DynamicLogic();
        return $this->returnJson($dynamicLogic->getDynamicPageByUserId($this->user_id));
    }


    /**
     * @api             {DELETE}   /index.php?m=Api&c=User&a=dynamic   24.删除动态 ok wxx
     * @apiDescription  发布动态
     * @apiName         deleteDynamic
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} id    要删除的动态id.
     *
     */
    private function deleteDynamic(Request $request){
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $dynamicLogic = new DynamicLogic();
        return $this->returnJson($dynamicLogic->deleteDynamic($id, $this->user_id));

    }


    public function collectDynamic(Request $request){
        if($request->isPost()){
            return $this->postCollectDynamic($request);
        }
        if($request->isGet()){
            return $this->getCollectDynamicList($request);
        }
        if($request->isDelete()){
            return $this->deleteCollectDynamic($request);
        }
        return $this->returnJson();

    }

    /**
     * @api             {POST}   /index.php?m=Api&c=User&a=collectDynamic   26.动态收藏 ok wxx
     * @apiDescription  收藏动态
     * @apiName         postCollectDynamic
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} id    要收藏的动态id.
     */
    private function postCollectDynamic(Request $request){
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $collectLogic = new UserCollectLogic();
        $dynamicLogic = new DynamicLogic();
        if($dynamicLogic->where('act_id', $id)->count() == 0){
            return $this->returnJson(4002, '你要收藏的动态已经不存在。');
        }

        return $this->returnJson($collectLogic->addCollect($this->user_id, UserCollectLogic::TYPE_DYNAMIC, $id));
    }

    /**
     * @api             {GET}   /index.php?m=Api&c=User&a=collectDynamic   27.我收藏的动态列表 ok wxx
     * @apiDescription  获取当前用户收藏的动态列表 时间倒序排列
     * @apiName         getCollectDynamicList
     * @apiGroup        User
     * @apiParam  {string} token    token.
     * @apiParam  {number} [p=1]        页码.
     * @apiParam  {number} [pageSize=20]   每页数据量.
     *
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list         列表.
     * @apiSuccess {number} list.id     id.
     * @apiSuccess {string} list.img    封面图片.
     * @apiSuccess {string} list.title  标题.
     * @apiSuccess {string} list.subTitle 副标题.
     * @apiSuccess {number} list.timeStamp  发布时间戳.
     * @apiSuccess {string} list.timeFmt    格式化发布时间.
     * @apiSuccess {number} list.praiseNum  点赞量.
     * @apiSuccess {number} list.readNum    阅读量.
     * @apiSuccess {number} list.ownerId    发布人id.
     * @apiSuccess {number} list.ownerName  发布人昵称.
     * @apiSuccess {number} list.readAvatar 发布人头像.
     *
     * @apiSuccessExample {json} SUCCESS
     *  {
     *      "status": 1,
     *      "msg": "SUCCESS",
     *      "result": {
     *          "p": 1,
     *          "totalPages": 4,
     *          "list": [
     *              {
     *                  "id": 13,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729853,
     *                  "timeFmt": "2017.09.18"
     *                  "ownerId": 1,
     *                  "ownerName": "",
     *                  "ownerAvatar": ""
     *              },
     *              {
     *                  "id": 12,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729850,
     *                  "timeFmt": "2017.09.18"
     *                  "ownerId": 1,
     *                  "ownerName": "",
     *                  "ownerAvatar": ""
     *              }
     *          ]
     *      }
     *  }
     *
     */
    private function getCollectDynamicList($request){
        $dynamicLogic = new DynamicLogic();
        return $this->returnJson($dynamicLogic->getCollectDynamicPage($this->user_id));
    }

    /**
     * @api             {DELETE}   /index.php?m=Api&c=User&a=collectDynamic   28.动态取消收藏 ok wxx
     * @apiDescription  取消收藏动态
     * @apiName         deleteCollectDynamic
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} id    要取消收藏的动态id.
     */
    private function deleteCollectDynamic(Request $request){
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $collectLogic = new UserCollectLogic();
        return $this->returnJson($collectLogic->removeCollect(UserCollectLogic::TYPE_DYNAMIC, $id, $this->user_id));
    }


    public function strategy(Request $request){
        if($request->isPost()){
            return $this->postStrategy($request);
        }
        if($request->isGet()){
            return $this->getStrategyList($request);
        }
        if($request->isDelete()){
            return $this->deleteStrategy($request);
        }

        return $this->returnJson();
    }

    /**
     * @api             {POST}   /index.php?m=Api&c=User&a=strategy   31.发布攻略 ok wxx
     * @apiDescription  发布攻略
     * @apiName         postStrategy
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} img    图片  多张用“|” 分割 ，第一张为默认封面.
     * @apiParam {string} title  标题.
     * @apiParam {string} content 内容.
     * @apiParam {number} countryId 国家id.
     * @apiParam {number} cityId    城市id.
     * @apiParam {string} summary 简介.
     *
     */
    private function postStrategy(Request $request){
        $reqParams = $this->getReqParams(['img', 'title', 'content', 'countryId', 'cityId', 'summary']);
        $rule = [
            'img' => 'require|max:1000',
            'title' => 'require|max:200',
            'summary' => 'require|max:200',
            'content' => 'require|max:1000',
            'countryId' => 'require',
            'cityId' => 'require',
        ];
        $this->validateParams($reqParams, $rule);
        $userLogic = new StrategyLogic();
        return $this->returnJson($userLogic->createStrategy($reqParams, $this->user));
    }

    /**
     * @api             {GET}   /index.php?m=Api&c=User&a=strategy   32.我的攻略列表 ok wxx
     * @apiDescription  获取当前用户的动态列表 时间倒序排列
     * @apiName         getStrategyList
     * @apiGroup        User
     * @apiParam  {string} token    token.
     * @apiParam  {number} [p=1]        页码.
     * @apiParam  {number} [pageSize=20]   每页数据量.
     *
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list         列表.
     * @apiSuccess {number} list.id     id.
     * @apiSuccess {string} list.img    封面图片.
     * @apiSuccess {string} list.title  标题.
     * @apiSuccess {string} list.subTitle 副标题.
     * @apiSuccess {number} list.timeStamp  发布时间戳.
     * @apiSuccess {string} list.timeFmt    格式化发布时间.
     * @apiSuccess {number} list.praiseNum  点赞数量.
     *
     */
    private function getStrategyList($request){
        $strategyLogic = new StrategyLogic();
        return $this->returnJson($strategyLogic->getStrategyPageByUserId($this->user_id));
    }

    /**
     * @api             {DELETE}   /index.php?m=Api&c=User&a=strategy   34.删除攻略 ok wxx
     * @apiDescription  删除攻略
     * @apiName         deleteStrategy
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} id    要删除的攻略id.
     *
     */
    private function deleteStrategy($request){
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $strategyLogic = new StrategyLogic();
        return $this->returnJson($strategyLogic->deleteStrategy($id, $this->user_id));
    }

    public function collectStrategy(Request $request){
        if($request->isPost()){
            return $this->postCollectStrategy($request);
        }
        if($request->isGet()){
            return $this->getCollectStrategyList($request);
        }
        if($request->isDelete()){
            return $this->deleteCollectStrategy($request);
        }
        return $this->returnJson();

    }


    /**
     * @api             {POST}   /index.php?m=Api&c=User&a=collectStrategy   36.攻略收藏 ok wxx
     * @apiDescription  收藏攻略
     * @apiName         postCollectStrategy
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} id    要收藏的攻略id.
     */
    private function postCollectStrategy(Request $request){
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $collectLogic = new UserCollectLogic();
        $strategyLogic = new StrategyLogic();
        if($strategyLogic->where('guide_id', $id)->count() == 0){
            return $this->returnJson(4002, '你要收藏的攻略已经不存在。');
        }

        return $this->returnJson($collectLogic->addCollect($this->user_id, UserCollectLogic::TYPE_STRATEGY, $id));
    }

    /**
     * @api             {GET}   /index.php?m=Api&c=User&a=collectStrategy   37.我收藏的攻略列表 ok wxx
     * @apiDescription  获取当前用户收藏的动态列表 时间倒序排列
     * @apiName         getCollectStrategyList
     * @apiGroup        User
     * @apiParam  {string} token    token.
     * @apiParam  {number} [p=1]        页码.
     * @apiParam  {number} [pageSize=20]   每页数据量.
     *
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list         列表.
     * @apiSuccess {number} list.id     id.
     * @apiSuccess {string} list.img    封面图片.
     * @apiSuccess {string} list.title  标题.
     * @apiSuccess {string} list.subTitle 副标题.
     * @apiSuccess {number} list.timeStamp  发布时间戳.
     * @apiSuccess {string} list.timeFmt    格式化发布时间.
     * @apiSuccess {number} list.praiseNum  点赞量.
     * @apiSuccess {number} list.readNum    阅读量.
     * @apiSuccess {number} list.ownerId    发布人id.
     * @apiSuccess {number} list.ownerName  发布人昵称.
     * @apiSuccess {number} list.readAvatar 发布人头像.
     *
     * @apiSuccessExample {json} SUCCESS
     *  {
     *      "status": 1,
     *      "msg": "SUCCESS",
     *      "result": {
     *          "p": 1,
     *          "totalPages": 4,
     *          "list": [
     *              {
     *                  "id": 13,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729853,
     *                  "timeFmt": "2017.09.18"
     *                  "ownerId": 1,
     *                  "ownerName": "",
     *                  "ownerAvatar": ""
     *              },
     *              {
     *                  "id": 12,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729850,
     *                  "timeFmt": "2017.09.18"
     *                  "ownerId": 1,
     *                  "ownerName": "",
     *                  "ownerAvatar": ""
     *              }
     *          ]
     *      }
     *  }
     *
     */
    private function getCollectStrategyList($request){
        $strategyLogic = new StrategyLogic();
        return $this->returnJson($strategyLogic->getCollectStrategyPage($this->user_id));
    }

    /**
     * @api             {DELETE}   /index.php?m=Api&c=User&a=collectDynamic   38.攻略取消收藏 ok wxx
     * @apiDescription  取消收藏攻略
     * @apiName         deleteCollectStrategy
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} id    要取消攻略的动态id.
     */
    private function deleteCollectStrategy(Request $request){
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $collectLogic = new UserCollectLogic();
        return $this->returnJson($collectLogic->removeCollect(UserCollectLogic::TYPE_STRATEGY, $id, $this->user_id));
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




    public function collectPackPro(Request $request){
        if($request->isPost()){
            return $this->postCollectPackPro($request);
        }
        if($request->isGet()){
            return $this->getCollectPackProPage($request);
        }
        if($request->isDelete()){
            return $this->deleteCollectPackPro($request);
        }
        return $this->returnJson();

    }

    /**
     * @api             {POST}   /index.php?m=Api&c=User&a=collectPackPro   41.收藏包车产品 ok wxx
     * @apiDescription  收藏包车产品
     * @apiName         postCollectPackPro
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} id    要收藏的id.
     */
    private function postCollectPackPro(Request $request){
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $collectLogic = new UserCollectLogic();
        $pcpLogic = new PackCarProductLogic();
        if($pcpLogic->where('id', $id)->count() == 0){
            return $this->returnJson(4002, '你要收藏的动态已经不存在。');
        }

        return $this->returnJson($collectLogic->addCollect($this->user_id, UserCollectLogic::TYPE_PACKCAR, $id));
    }

    /**
     * @api             {GET}   /index.php?m=Api&c=User&a=collectPackPro   42.我收藏的包车产品列表 todo wxx
     * @apiDescription  获取当前用户收藏的动态列表 时间倒序排列
     * @apiName         getCollectPackProPage
     * @apiGroup        User
     * @apiParam  {string} token    token.
     * @apiParam  {number} [p=1]        页码.
     * @apiParam  {number} [pageSize=20]   每页数据量.
     *
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list         列表.
     * @apiSuccess {number} list.id     id.
     * @apiSuccess {string} list.img    封面图片.
     * @apiSuccess {string} list.title  标题.
     * @apiSuccess {number} list.timeStamp  发布时间戳.
     * @apiSuccess {string} list.timeFmt    格式化发布时间.
     *
     *
     */
    private function getCollectPackProPage($request){
        $dynamicLogic = new PackCarProductLogic();
        return $this->returnJson($dynamicLogic->getCollectPage($this->user_id));
    }

    /**
     * @api             {DELETE}   /index.php?m=Api&c=User&a=collectPackPro   43.取消收藏包车产品 ok wxx
     * @apiDescription  取消收藏
     * @apiName         deleteCollectPackPro
     * @apiGroup        User
     * @apiParam {string} token    token.
     * @apiParam {string} id    要取消收藏的id.
     */
    private function deleteCollectPackPro(Request $request){
        $id = input('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $collectLogic = new UserCollectLogic();
        return $this->returnJson($collectLogic->removeCollect(UserCollectLogic::TYPE_PACKCAR, $id, $this->user_id));
    }



}
