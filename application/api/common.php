<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 14:34
 */

use DesUtils\DesUtils;
use service\HttpService;
use service\MsgService;
use think\Db;


// 接口返回json 数据
if(!function_exists('getCodeMsg')){
    function getCodeMsg($code = 'all'){
        $CODE_MSG = [
            0 => '未知错误',
            2000 => 'SUCCESS',
            // 客户端异常
            4000 => '非法请求',
            4001 => '请求缺少参数',
            4002 => '请求参数格式错误',
            4003 => '请求参数格式错误',
            4004 => '暂无数据',
            4005 => '重复的请求',
            // 客户端异常-用户鉴权
            4010 => '无权访问',
            4011 => 'token丢失',
            4012 => 'token无效',
            4013 => 'token过期',
            4014 => '账号或密码错误',
            4015 => '签名校验失败',

            // 服务端端异常
            5000 => '服务端异常',
            5001 => '服务端忙',
            5010 => '代码异常',
            5020 => '数据库操作异常',
            5030 => '文件操作异常',

            // 调用第三方接口异常
            6000 => '调用第三方接口异常',
        ];
        if(key_exists($code, $CODE_MSG)){
            return $CODE_MSG[$code];
        }
        if($code == 'all'){
            return $CODE_MSG;
        }
        return '';
    }
}

// 返回数组
if(!function_exists('resultArray')){
    function resultArray($result = 0, $msg = '', $data = []){
        $code = $result;
        if(is_array($result)){
            $code = $result['status'];
            $msg = $result['msg'];
            $data = $result['result'];
        }
        if(empty($data)){
            $data = new stdClass();
        }
        $info = [
            'status' => $code,
            'msg' => empty($msg) ? getCodeMsg($code) : $msg,
            'result' => $data
        ];
        return $info;
    }
}

// 接口返回json 数据
if(!function_exists('returnJson')){
    function returnJson($result = 0, $msg = '', $data = []){
        $ret = resultArray($result, $msg, $data);
        header('Content-type:application/json; charset=utf-8');
        header("Access-Control-Allow-Origin: *");
        exit(json_encode($ret));
    }
}

/*
 * 生成签名
 */
function createSign($sendData){
    $desClass = new DesUtils();
    $arrOrder = $desClass->naturalOrdering([$sendData['rt_appkey'], $sendData['req_time'], $sendData['req_action']]);
    $skArr = explode('_', config('app_access_key'));
    return $desClass->strEnc($arrOrder, $skArr[0], $skArr[1], $skArr[2]);//签名
}

/**
 * Auther: WILL<314112362@qq.com>
 * Time: 2017-3-20 17:51:09
 * Describe: 校验文件
 * @return bool
 */
if(!function_exists('validateFile')){
    function validateFile($files = [], $rule = ['size' => 1024*1024*20]){
        if(empty($files)){
            returnJson(4001, '缺少文件');
        }
        if(is_array($files)){
            foreach($files as $file){
                $validate = $file->check($rule);
                if(!$validate){
                    returnJson(4002, '', $file->getError());
                }
            }
            return true;
        }
        if(!$files->check($rule)){
            returnJson(4002, '', $files->getError());
        }
        return true;
    }
}

/**
 * 得到ID的身份信息
 */
function getIDType($seller_id){
    if(empty($seller_id)){
        return '';
    }
    return Db::name('seller')->field('store_id,drv_id,home_id')->find();
}

/**
 * 得到司导身份信息
 */
function getDrvIno($seller_id){
    if(empty($seller_id)){
        return 0;
    }
    $star = Db::name('pack_comment')->where('seller_id', $seller_id)->avg('star');
    $line = Db::name('pack_line')
        ->field('line_title')
        ->where('seller_id', $seller_id)
        ->order('create_at desc')
        ->limit(1)
        ->find();
    $line = empty($line) ? '' : $line['line_title'];
    return [
        'star' => empty($star) ? config('APP_DEFAULT_STAR') : $star,
        'line' => $line
    ];
}

/*
 * 得到评价星级type 6为路线
 */

function getLineStar($seller_id, $type){
    if(empty($seller_id)){
        return '';
    }
    $where = [
        'seller_id' => $seller_id,
        'type' => $type
    ];
    $star = Db::name('pack_comment')->where($where)->avg('star');
    return $star;
}

/*
 * 发送短信
 */
function sendSMSbyApi($phone, $content){
    $msgService = new MsgService();
    $str = '【傻孩子APP】'.$content;
    $result = $msgService->sendSms($phone, $str);
    return $result;
}

/*
 * 推送信息 推送给货主为$rt_key='wztx_shipper' 推送给司机为 $rt_key='wztx_driver'
 */
function pushInfo($token, $title, $content, $rt_key = 'wztx_driver', $type = 'private'){
    $sendData = [
        "platform" => "all",
        "rt_appkey" => $rt_key,
        "req_time" => time(),
        "req_action" => 'push',
        "alert" => $title,
        "regIds" => $token,
        //"platform" => "all",
        "androidNotification" => [
            "alert" => $title,
            "title" => $content,
            "builder_id" => "builder_id",
            "priority" => 0,
            "style" => 0,
            "alert_type" => -1,
            "extras" => ['type' => $type]
        ]
    ];
    $desClass = new DesUtils();
    $arrOrder = $desClass->naturalOrdering([$sendData['rt_appkey'], $sendData['req_time'], $sendData['req_action']]);
    $skArr = explode('_', config('app_access_key'));
    $sendData['sign'] = $desClass->strEnc($arrOrder, $skArr[0], $skArr[1], $skArr[2]);//签名
    $result = HttpService::post(getenv('APP_API_HOME').'push', http_build_query($sendData));
}

/*
 * 发送邮件
 */
function sendMail($to, $title, $content){
    $sendData = [
        'rt_appkey' => '2017ShaHaiZi_uQbJFDUPPUGc6MiN_j99YeHXpb3fsAT0V',
        "req_time" => time(),
        "req_action" => 'sendHtml',
        'fromName' => '傻孩子APP',//发送人名
        'to' => $to,
        'subject' => $title,
        'html' => $content,
        'from' => 'tan3250204@sina.com',//平台的邮件头
    ];
    $desClass = new DesUtils();
    $arrOrder = $desClass->naturalOrdering([$sendData['rt_appkey'], $sendData['req_time'], $sendData['req_action']]);
    $skArr = explode('_', config('app_access_key'));
    $sendData['sign'] = $desClass->strEnc($arrOrder, $skArr[0], $skArr[1], $skArr[2]);//签名
    $result = HttpService::post('http://mps.ruitukeji.com/SendEmail/sendHtml', http_build_query($sendData));
    return json_decode($result, true);
}


/*
 * 时间的处理
 */
function shzDate($time){
    if(empty($time)){
        return $time;
    }
    return strval(date('Y-m-d H:i:s', $time));
}

/*
 * 金钱的处理-->统一后两位小数点
 */
function shzMoney($num, $ispre = false){
    $num = $num > 0 ? $num : 0;
    //number_format(10000, 2, '.', '')
    $formattedNum = number_format($num, 2, '.', '');
    if($ispre){
        return '¥'.$formattedNum;
    }else{
        return strval($formattedNum);
    }
}

/*
 * 得到省市区
 */

function getCityName($id){
    $name = M('region')->where('id', $id)->value('name');
    return empty($name)?'':$name;
}

/*
 * 进行支付成功后的后续操作 //订单信息,用户信息,优惠价格,支付方式,是否可以用优惠券,优惠券ID
 */

function payPackOrder($pack_order, $user_info, $discount_price, $pay_way, $is_coupon, $coupon_id = ''){
    $packLineLogic =  M('pack_line');
    $real_price = $pack_order['total_price'] - $discount_price;//真实价格
    //需要变更的用户信息
    $user['user_money'] = $user_info['user_money'] - $real_price;//余额
    $user['total_amount'] = $user_info['total_amount'] + $real_price;//增加消费金额
    M('users')->where(['user_id' => $pack_order['user_id']])->update($user);//更新用户余额
    trace('更新用户余额');
    //找出消费的产品表（更改产品的销量信息），且只有非精品路线才会增加预订次数1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游
    if($pack_order['type'] == 3){
        $line_id = $pack_order['line_id'];
        if(!empty($line_id)){
            M('pack_line')->where(['line_id' => $line_id, 'is_del' => 0])->setInc('line_buy_num');//更新预订次数
            $info = $packLineLogic->field(['seller_id','is_comm'])
                ->where(['line_id' => $line_id, 'is_del' => 0])
                ->find();//查看是否精品路线
            if(!empty($info) && $info['is_comm'] == 0){
                $seller = $pack_order['seller_id'];
                $setArr = tpCache('car_setting_money');
                $add_price = $real_price - ($real_price*$setArr['name_line']*0.01);//增加商家余额
                M('seller')->where(['seller_id' => $seller])->setInc('user_money', $add_price);
                trace('增加商家余额');
            }
        }
    }
    //更新优惠券信息  order_id  use_time   drv_id status 1
    if($is_coupon){
        $coupon_data = [
            'order_id' => $pack_order['air_id'],
            'use_time' => time(),
            'drv_id' => $pack_order['drv_id'],
            'staus' => 1
        ];
        M('coupon_list')->where(['id' => $coupon_id])->update($coupon_data);//更新优惠券信息
        trace('更新优惠券信息');
    }
    //更新订单信息
    $order_arr = [
        'status' => 1,
        'pay_way' => $pay_way,
        'coupon_price' => $discount_price,//优惠价格
        'real_price' => $real_price,
        'is_pay' => 1,
        'pay_time' => time(),
    ];
    if($is_coupon){
        $order_arr['discount_id'] = $coupon_id;//优惠券ID
    }
    M('pack_order')->where(['air_id' => $pack_order['air_id']])->update($order_arr);
    trace('更新订单信息');
    //然后记录账户信息 类型 1=充值 2=提现 3=消费 4=退款
    $order_map = [
        1 => '接机',
        2 => '送机',
        3 => '线路游玩',
        4 => '单次接送',
        5 => '私人订制',
        6 => '按天包车游',
    ];
    $account_arr = [
        'user_id' => $pack_order['user_id'],
        'user_money' => $real_price,//使用的余额
        'user_balance' => $user['user_money'],//用户余额
        'frozen_money' => 0,//冻结金额
        'change_time' => time(),
        'desc' => $order_map[$pack_order['type']],
        'order_sn' => $pack_order['order_sn'],
        'order_id' => $pack_order['air_id'],
        'type' => 3,
    ];
    M('account_log')->save($account_arr);
    return ['status' => 1, 'msg' => '成功', 'result' => ['user_money' => $user['user_money']]];//返回余额
}

/*
 * 修改傻孩子号
 */

function update_shz_code($user_id, $shz_code){
    //先判断是否有修改
    $is_update = M('users')->where('user_id', $user_id)->value('is_update_shz');
    if(!$is_update){//未修改
        //查看修改的傻孩子号是否存在
        $where = [
            'user_id' => ['neq', $user_id],
            'shz_code' => $shz_code,
        ];
        $shz_code_count = M('users')->where($where)->count();
        if($shz_code_count === 0){//修改傻孩子号
            $data = [
                'is_update_shz' => 1,//修改了傻孩子号
                'shz_code' => $shz_code
            ];
            $result = M('users')->where('user_id', $user_id)->update($data);
            if($result){
                return ['status' => 1, 'msg' => '成功'];
            }
            return ['status' => -1, 'msg' => '修改失败'];
        }
        return ['status' => -1, 'msg' => '重复的傻孩子号'];
    }
    return ['status' => -1, 'msg' => '您已经修改过傻孩子号了'];
}

/**
 * 获取傻孩子号
 * @return string
 */
function get_shz_code(){
    $shz_code = null;
    // 保证不会有重复傻孩子号存在
    while(true){
        $shz_code = date('YmdHis').rand(1000, 9999); // 傻孩子
        $shz_code_count = M('users')->where("shz_code = '$shz_code'")->count();
        if($shz_code_count == 0){
            break;
        }
    }
    return $shz_code;
}

function get_pack_line($where){
    $list = M('pack_line')
        ->field('seller_id,line_id,line_buy_num,city,line_title,cover_img,line_price,seller_id,line_detail,create_at')
        ->where($where)
        ->select();
    foreach($list as &$val){
        $val['star'] = getLineStar($val['seller_id'], 6);
        $val['line_detail'] = json_decode(htmlspecialchars_decode($val['line_detail']));
        $val['create_at'] = shzDate($val['create_at']);
    }
    return $list;
}


