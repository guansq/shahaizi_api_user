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
use think\Db;
use DesUtils\DesUtils;
use service\HttpService;
use service\MsgService;
use app\common\logic\SellerLogic;
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
            4003 => '非法的请求参数值',
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
        // if(empty($data)){
        //     $data = new stdClass();
        // }
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

/*
 * 敏感词过滤
 */
function wordFilter($text){
    $words = M('words_text')->where(['is_show'=>1])->select();
    if(empty($words)){
        return $text;
    }
    $check_arr = [];
    foreach($words as $val){
        $check_arr[$val['name']] = '*';
    }
    //$check_arr = array_unique($check_arr);
    //print_r($check_arr);die;
    //print_r(strtr($text,$check_arr));die;
    return strtr($text,$check_arr);
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
    $where = [
        'seller_id' => $seller_id,
        'is_del' => 0,
        'is_state' => 1,
    ];
    $star = Db::name('order_comment')->where('seller_id', $seller_id)->avg('drv_rank');
    $line = Db::name('pack_line')
        ->field('line_title')
        ->where($where)
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

function getLineStar($line_id){
    if(empty($line_id)){
        return '';
    }
    $where = [
        'line_id' => $line_id,
    ];
    $star = Db::name('order_comment')->where($where)->avg('line_rank');
    return $star;
}

/*
 * 自动计算会员等级并保存到用户表
 */
function autoNumberLevel($user_id,$total_amount){
    $level = M('user_level')->order('amount desc')->select();
    $level_id = 1;
    foreach($level as $val){
        if($total_amount >= $val['amount']){
            $level_id = $val['level_id'];
            break;

        }
    }
    //保存level_id入库
    M('users')->where(['user_id'=>$user_id])->update(['level'=>$level_id]);
    $levelName = M("user_level")->cache(true)->where("level_id = {$level_id}")->getField("level_name");
    return ['level' => $level_id,'level_name' => $levelName];
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
 * 推送信息 推送的对象，用户为0司导为1
 */
function pushInfo($receive_id,$obj_type,$token, $title, $content, $type = 'private'){
    $sendData = [
        "platform" => "all",
        "rt_appkey" => '2017ShaHaiZiSeller_kiXhfpZs7XdfjwE1_QPhJn8lSkWVtt1RR',
        "req_time" => time(),
        "req_action" => 'push',
        "alert" => $title,
        "regIds" => $token,
        "platform" => "all",
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
    $skArr = explode('_', config('bus_app_access_key'));//像司机推送
    $sendData['sign'] = $desClass->strEnc($arrOrder, $skArr[0], $skArr[1], $skArr[2]);//签名
    //print_r($sendData);die;
    $result = HttpService::post('http://mps.ruitukeji.com/'.'push', http_build_query($sendData));
    $data = [
        'title' => $title,
        'message' => $content,
        'create_at' => time(),
        'content' => $content,
        'type' => $obj_type,
        'receive_id' => $receive_id,
    ];
    M('system_message')->save($data);//保存推送消息到数据库
}

/*
 * 推送消息  推送的对象，用户为0司导为1
 * type 为all推送给全部对象
 */
function pushMessage($title, $content, $pushId = '',$receive_id = '',$obj_type = '', $type = 'private'){
    $push = new \app\api\controller\Push();
    $push->title = $title;
    $push->content = $content;
    $push->pushId = ($type == 'all') ?  '' : $pushId;
    if($obj_type == 1){//设置推送为司导端
        $push->app_key = '17f7ed4f812eeb340553963d';//司导的app_key
        $push->master_secret = '7f49e6a381ee00c4b3a7507a';//司导的app_key
    }
    $result = $push->index();
    if($result['status'] == 1){//将推送的消息保存到数据库
        $data = [
            'title' => $title,
            'message' => $content,
            'create_at' => time(),
            'content' => $content,
            'type' => $obj_type,
            'push_users' => 1,
            'receive_id' => $receive_id,
        ];
        if($obj_type == 1){
            $data['push_users'] = 2;
        }
        M('system_message')->save($data);//保存推送消息到数据库
    }
}

/*
 * 判断是否在黑名单
 */
function userIsLock(){

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
 * 得到国家
 */

function getCountryName($id){
    $name = M('region_country')->where('id', $id)->value('name');
    return empty($name)?'':$name;
}

function getPlatformCharge($return_value = 0)
{
    $config = M("config") -> where("inc_type = 'car_setting_money' AND name = 'name_line'") -> find();
    if($return_value)
        return $config["value"];
    return  $config["value"]."%";
}
/*
 * 进行支付成功后的后续操作 //订单信息,用户信息,优惠价格,支付方式,是否可以用优惠券,优惠券ID
 */

function payPackOrder($pack_order, $user_info, $discount_price, $pay_way, $is_coupon, $coupon_id = ''){
    $packLineLogic =  M('pack_line');
    $real_price = $pack_order['total_price'] - $discount_price;//真实价格
    if($pay_way == 2){//余额支付需要更改用户金额
        //需要变更的用户信息
        $user['user_money'] = $user_info['user_money'] - $real_price;//余额
        $user['total_amount'] = $user_info['total_amount'] + $real_price;//增加消费金额
        M('users')->where(['user_id' => $pack_order['user_id']])->update($user);//更新用户余额
        trace('更新用户余额');
    }
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
            'status' => 1
        ];
        M('coupon_list')->where(['id' => $coupon_id])->update($coupon_data);//更新优惠券信息
        trace('更新优惠券信息');
    }
    //更新订单信息
    $order_arr = [
        'status' => 1,
        'pay_way' => $pay_way,
        'coupon_price' => $discount_price,//优惠价格
        //'real_price' => $real_price,
        'is_pay' => 1,
        'pay_time' => time(),
    ];

    if($pack_order['type'] == 3 && !empty($pack_order['seller_id'] )){ // 用户支付成功后判断订单是否是司导发布的线路，如果是订单状态（status）改为进行中；
        $order_arr['status'] = \app\common\logic\PackOrderLogic::STATUS_UNSTART;
    }

    if(!empty($pack_order['allot_seller_id']) && $pack_order['type'] != 3){//分配司导逻辑   除了路线订单之外
        $seller_id = explode(',',$pack_order['allot_seller_id'])[1];
        //$order_arr['allot_seller_id'] = '';
        $push_info = M('seller')->where("seller_id = $seller_id")->find();
        $mobile = $push_info['country_code'].$push_info['mobile'];
        $content = '您有一条新订单，请及时处理';
        sendSMSbyApi($mobile,$content);
        trace('发送短信');
        $config_str = M('config')->where(array("name"=>"name_car"))->find();
        $order_arr['commission_money'] = round($pack_order['real_price']*$config_str['value']/100);//佣金金额
        $order_arr['seller_money'] = $pack_order['real_price']-$order_arr['commission_money'];//司导金额
        $order_arr['status'] = \app\common\logic\PackOrderLogic::STATUS_UNJXDJ;//已派单_待接单
    }

    if($pack_order['type'] == 3){//对路线进行推送
        $line_id = $pack_order['line_id'];
        $line = M('pack_line')->where('line_id',$line_id)->find();
        if(!empty($line) && $line['is_admin'] == 0){
            $seller = SellerLogic::findByDrvId($line['seller_id']);
            if(!empty($seller)){
                $mobile = $seller['country_code'].$seller['mobile'];
                $content = '您的线路，客人已支付，请及时处理';
                sendSMSbyApi($mobile,$content);
                pushMessage('线路已支付', $content, $seller['device_no'], $seller['seller_id'], 1);
            }
        }
    }

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
    //print_r($user);die;
    $account_arr = [
        'user_id' => $pack_order['user_id'],
        'user_money' => 0-$real_price,//使用的余额
        'user_balance' => $user['user_money'],//用户余额
        'frozen_money' => 0,//冻结金额
        'change_time' => time(),
        'desc' => $order_map[$pack_order['type']],
        'order_sn' => $pack_order['order_sn'],
        'order_id' => $pack_order['air_id'],
        'type' => 3,
    ];
    M('account_log')->save($account_arr);
    trace('生成交易记录');
    $ret =  [
        'user_money' => $user['user_money'],
        'user_money_fmt' => moneyFormat($user['user_money'])
    ];
    return ['status' => 1, 'msg' => '成功', 'result' =>$ret];//返回余额
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

/**
 * 得到推荐码
 */
function get_apply_code(){
    $apply_code = null;
    while(true){
        $apply_code = get_rand_str(6,0);
        $apply_code_count = M('users')->where("apply_code = '$apply_code'")->count();
        if($apply_code_count == 0){
            break;
        }
    }
    return $apply_code;
}
function get_pack_line($where){
    $list = M('pack_line')
        ->field('seller_id,line_id,line_buy_num,city,line_title,cover_img,line_price,seller_id,line_detail,create_at')
        ->where($where)
        ->select();
    foreach($list as &$val){
        $val['star'] = getLineStar($val['line_id']);
        $val['line_detail'] = json_decode(htmlspecialchars_decode($val['line_detail']));
        $val['create_at'] = shzDate($val['create_at']);
    }
    return $list;
}


function get_pack_line_page($where,$firstRow,$listRows){
    $list = M('pack_line')
        ->field('seller_id,line_id,line_buy_num,city,line_title,cover_img,line_price,seller_id,line_detail,create_at')
        ->where($where)
        ->limit($firstRow.','.$listRows)
        ->select();
    foreach($list as &$val){
        $val['star'] = getLineStar($val['line_id']);
        $val['line_detail'] = json_decode(htmlspecialchars_decode($val['line_detail']));
        $val['create_at'] = shzDate($val['create_at']);
    }
    return $list;
}


/**
 * 获取用户信息
 * @param $user_id_or_name  用户id 邮箱 手机 第三方id
 * @param int $type 类型 0 user_id查找 1 邮箱查找 2 手机查找 3 第三方唯一标识查找
 * @param string $oauth 第三方来源
 * @return mixed
 */
function get_user_info($user_id_or_name, $type = 0, $oauth = '')
{
    $map = array();
    if ($type == 0)
        $map['user_id'] = $user_id_or_name;
    if ($type == 1)
        $map['email'] = $user_id_or_name;
    if ($type == 2)
        $map['mobile'] = $user_id_or_name;
    if ($type == 3) {
        $map['openid'] = $user_id_or_name;
        $map['oauth'] = $oauth;
    }
    if ($type == 4) {
        $map['unionid'] = $user_id_or_name;
        $map['oauth'] = $oauth;
    }
    $user = M('users')->where($map)->find();
    return $user;
}

/**
 * 获取司导用户信息
 *
 */
function get_drv_info($seller_id){
    $seller = M('seller')->where(['seller_id'=>$seller_id])->find();
    return $seller;
}
/*
 * 获取用户的fans_num粉丝数attention_num关注数good_num被赞数collection_num被收藏数
 */
function get_user_collect_info($user_id){
    $info = [];
    $info['fans_num'] = M('user_attention')->where(['obj_id'=>$user_id,'obj_type'=>1])->count();
    $info['attention_num'] = M('user_attention')->where(['user_id'=>$user_id,'obj_type'=>1])->count();
    $info['good_num'] = M('user_praise')->where(['obj_owner_id'=>$user_id])->count();
    $info['collection_num'] = M('goods_collect')->where(['obj_owner_id'=>$user_id])->count();
    return $info;
}
/*
 * 发送消息给用户通过攻略或动态的点赞动作
 * 动态1攻略2
 */
function send_msg_by_article($title,$content,$receive_id,$article_id,$article_type){
    $data = [
        'title' => $title,
        'message' => $content,
        'push_users' => 1,
        'create_at' => time(),
        'content' => $content,
        'receive_id' => $receive_id,
        'article_id' => $article_id,
        'article_type' => $article_type,
    ];
    M('system_message')->save($data);//保存消息到数据库
}
/*
 * 发送信息给用户或司导
 */
function send_drv_msg($title,$content,$receive_id){
    $data = [
        'title' => $title,
        'message' => $content,
        'push_users' => 2,
        'type' => 1,
        'create_at' => time(),
        'content' => $content,
        'receive_id' => $receive_id,
    ];
    M('system_message')->save($data);//保存消息到数据库
}
/**
 * 更新会员等级,折扣，消费总额
 * @param $user_id  用户ID
 * @return boolean
 */
function update_user_level($user_id)
{
    $level_info = M('user_level')->order('level_id')->select();
    $total_amount = M('order')->master()->where("user_id=:user_id AND pay_status=1 and order_status not in (3,5)")->bind(['user_id' => $user_id])->sum('order_amount+user_money');
    if ($level_info) {
        foreach ($level_info as $k => $v) {
            if ($total_amount >= $v['amount']) {
                $level = $level_info[$k]['level_id'];
                //$discount = $level_info[$k]['discount']/100;
            }
        }
        $user = session('user');
        $updata['total_amount'] = $total_amount;//更新累计修复额度
        //累计额度达到新等级，更新会员折扣
        if (isset($level) && $level > $user['level']) {
            $updata['level'] = $level;
            //$updata['discount'] = $discount;
        }
        M('users')->where("user_id", $user_id)->save($updata);
    }
}

/**
 *  商品缩略图 给于标签调用 拿出商品表的 original_img 原始图来裁切出来的
 * @param type $goods_id 商品id
 * @param type $width 生成缩略图的宽度
 * @param type $height 生成缩略图的高度
 */
function goods_thum_images($goods_id, $width, $height)
{
    if (empty($goods_id)) return '';
    
    //判断缩略图是否存在
    $path = "public/upload/goods/thumb/$goods_id/";
    $goods_thumb_name = "goods_thumb_{$goods_id}_{$width}_{$height}";

    // 这个商品 已经生成过这个比例的图片就直接返回了
    if (is_file($path . $goods_thumb_name . '.jpg')) return '/' . $path . $goods_thumb_name . '.jpg';
    if (is_file($path . $goods_thumb_name . '.jpeg')) return '/' . $path . $goods_thumb_name . '.jpeg';
    if (is_file($path . $goods_thumb_name . '.gif')) return '/' . $path . $goods_thumb_name . '.gif';
    if (is_file($path . $goods_thumb_name . '.png')) return '/' . $path . $goods_thumb_name . '.png';

    $original_img = M('Goods')->cache(true, 3600)->where("goods_id", $goods_id)->getField('original_img');
    if (empty($original_img)) {
        return '/public/images/icon_goods_thumb_empty_300.png';
    }
    
    $ossClient = new \app\common\logic\OssLogic;
    if (($ossUrl = $ossClient->getGoodsThumbImageUrl($original_img, $width, $height))) {
        return $ossUrl;
    }

    $original_img = '.' . $original_img; // 相对路径
    if (!is_file($original_img)) {
        return '/public/images/icon_goods_thumb_empty_300.png';
    }

    try {
        vendor('topthink.think-image.src.Image');
		if(strstr(strtolower($original_img),'.gif'))
		{
			vendor('topthink.think-image.src.image.gif.Encoder');
			vendor('topthink.think-image.src.image.gif.Decoder');
			vendor('topthink.think-image.src.image.gif.Gif');				
		}		
        $image = \think\Image::open($original_img);

        $goods_thumb_name = $goods_thumb_name . '.' . $image->type();
        // 生成缩略图
        !is_dir($path) && mkdir($path, 0777, true);
        // 参考文章 http://www.mb5u.com/biancheng/php/php_84533.html  改动参考 http://www.thinkphp.cn/topic/13542.html
        $image->thumb($width, $height, 2)->save($path . $goods_thumb_name, NULL, 100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
        //图片水印处理
        $water = tpCache('water');
        if ($water['is_mark'] == 1) {
            $imgresource = './' . $path . $goods_thumb_name;
            if ($width > $water['mark_width'] && $height > $water['mark_height']) {
                if ($water['mark_type'] == 'img') {
                    //检查水印图片是否存在
                    $waterPath = "." . $water['mark_img'];
                    if (is_file($waterPath)) {
                        $quality = $water['mark_quality'] ?: 80;
                        $waterTempPath = dirname($waterPath).'/temp_'.basename($waterPath);
                        $image->open($waterPath)->save($waterTempPath, null, $quality);
                        $image->open($imgresource)->water($waterTempPath, $water['sel'], $water['mark_degree'])->save($imgresource);
                        @unlink($waterTempPath);
                    }
                } else {
                    //检查字体文件是否存在,注意是否有字体文件
                    $ttf = './hgzb.ttf';
                    if (file_exists($ttf)) {
                        $size = $water['mark_txt_size'] ?: 30;
                        $color = $water['mark_txt_color'] ?: '#000000';
                        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                            $color = '#000000';
                        }
                        $transparency = intval((100 - $water['mark_degree']) * (127/100));
                        $color .= dechex($transparency);
                        $image->open($imgresource)->text($water['mark_txt'], $ttf, $size, $color, $water['sel'])->save($imgresource);
                    }
                }
            }
        }
        $img_url = '/' . $path . $goods_thumb_name;

        return $img_url;
    } catch (think\Exception $e) {

        return $original_img;
    }
}

/**
 * 商品相册缩略图
 */
function get_sub_images($sub_img, $goods_id, $width, $height)
{
    //判断缩略图是否存在
    $path = "public/upload/goods/thumb/$goods_id/";
    $goods_thumb_name = "goods_sub_thumb_{$sub_img['img_id']}_{$width}_{$height}";
    
    //这个缩略图 已经生成过这个比例的图片就直接返回了
    if (is_file($path . $goods_thumb_name . '.jpg')) return '/' . $path . $goods_thumb_name . '.jpg';
    if (is_file($path . $goods_thumb_name . '.jpeg')) return '/' . $path . $goods_thumb_name . '.jpeg';
    if (is_file($path . $goods_thumb_name . '.gif')) return '/' . $path . $goods_thumb_name . '.gif';
    if (is_file($path . $goods_thumb_name . '.png')) return '/' . $path . $goods_thumb_name . '.png';

    $ossClient = new \app\common\logic\OssLogic;
    if (($ossUrl = $ossClient->getGoodsAlbumThumbUrl($sub_img['image_url'], $width, $height))) {
        return $ossUrl;
    }

    $original_img = '.' . $sub_img['image_url']; //相对路径
    if (!is_file($original_img)) {
        return '/public/images/icon_goods_thumb_empty_300.png';
    }
    try {
        vendor('topthink.think-image.src.Image');
        if(strstr(strtolower($original_img),'.gif'))
        {
            vendor('topthink.think-image.src.image.gif.Encoder');
            vendor('topthink.think-image.src.image.gif.Decoder');
            vendor('topthink.think-image.src.image.gif.Gif');
        }
        $image = \think\Image::open($original_img);

        $goods_thumb_name = $goods_thumb_name . '.' . $image->type();
        // 生成缩略图
        !is_dir($path) && mkdir($path, 0777, true);
        // 参考文章 http://www.mb5u.com/biancheng/php/php_84533.html  改动参考 http://www.thinkphp.cn/topic/13542.html
        $image->thumb($width, $height, 2)->save($path . $goods_thumb_name, NULL, 100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
        //图片水印处理
        $water = tpCache('water');
        if ($water['is_mark'] == 1) {
            $imgresource = './' . $path . $goods_thumb_name;
            if ($width > $water['mark_width'] && $height > $water['mark_height']) {
                if ($water['mark_type'] == 'img') {
                    //检查水印图片是否存在
                    $waterPath = "." . $water['mark_img'];
                    if (is_file($waterPath)) {
                        $quality = $water['mark_quality'] ?: 80;
                        $waterTempPath = dirname($waterPath).'/temp_'.basename($waterPath);
                        $image->open($waterPath)->save($waterTempPath, null, $quality);
                        $image->open($imgresource)->water($waterTempPath, $water['sel'], $water['mark_degree'])->save($imgresource);
                        @unlink($waterTempPath);
                    }
                } else {
                    //检查字体文件是否存在,注意是否有字体文件
                    $ttf = './hgzb.ttf';
                    if (file_exists($ttf)) {
                        $size = $water['mark_txt_size'] ?: 30;
                        $color = $water['mark_txt_color'] ?: '#000000';
                        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                            $color = '#000000';
                        }
                        $transparency = intval((100 - $water['mark_degree']) * (127/100));
                        $color .= dechex($transparency);
                        $image->open($imgresource)->text($water['mark_txt'], $ttf, $size, $color, $water['sel'])->save($imgresource);
                    }
                }
            }
        }
        return '/' . $path . $goods_thumb_name;
    } catch (think\Exception $e) {

        return $original_img;
    }
}

/**
 * 刷新商品库存, 如果商品有设置规格库存, 则商品总库存 等于 所有规格库存相加
 * @param type $goods_id 商品id
 */
function refresh_stock($goods_id)
{
    $count = M("SpecGoodsPrice")->where("goods_id", $goods_id)->count();
    if ($count == 0) return false; // 没有使用规格方式 没必要更改总库存

    $store_count = M("SpecGoodsPrice")->where("goods_id", $goods_id)->sum('store_count');
    M("Goods")->where("goods_id", $goods_id)->save(array('store_count' => $store_count)); // 更新商品的总库存
}

/**
 * 根据 order_goods 表扣除商品库存
 * @param type $order_id 订单id
 */
function minus_stock($order)
{
    $orderGoodsArr = M('OrderGoods')->master()->where(array('order_id' => $order['order_id']))->select(); // 有可能是刚下完订单的 需要到主库里面去查
    foreach ($orderGoodsArr as $key => $val) {
        // 有选择规格的商品
        if (!empty($val['spec_key'])) {   // 先到规格表里面扣除数量 再重新刷新一个 这件商品的总数量
            $SpecGoodsPrice = new \app\common\model\SpecGoodsPrice();
            $specGoodsPrice = $SpecGoodsPrice::get(['goods_id' => $val['goods_id'], 'key' => $val['spec_key']]);
            $specGoodsPrice->where(['goods_id' => $val['goods_id'], 'key' => $val['spec_key']])->setDec('store_count', $val['goods_num']);
            refresh_stock($val['goods_id']);
        } else {
            $specGoodsPrice = null;
            M('Goods')->where("goods_id", $val['goods_id'])->setDec('store_count', $val['goods_num']); // 直接扣除商品总数量
        }
        update_stock_log($order['user_id'], -$val['goods_num'], $val, $order['order_sn']);//库存出库日志
        M('Goods')->where("goods_id", $val['goods_id'])->setInc('sales_sum', $val['goods_num']); // 增加商品销售量
        //更新活动商品购买量
        if ($val['prom_type'] == 1 || $val['prom_type'] == 2) {
            $GoodsPromFactory = new \app\common\logic\GoodsPromFactory();
            $goodsPromLogic = $GoodsPromFactory->makeModule($val, $specGoodsPrice);
            $prom = $goodsPromLogic->getPromModel();
            if ($prom['status'] == 1 && $prom['is_end'] == 0) {
                $tb = $val['prom_type'] == 1 ? 'flash_sale' : 'group_buy';
                M($tb)->where("id", $val['prom_id'])->setInc('buy_num', $val['goods_num']);
                M($tb)->where("id", $val['prom_id'])->setInc('order_num');
            }
        }
    }
}

/**
 * 商品库存操作日志
 * @param int $muid 操作 用户ID
 * @param int $stock 更改库存数
 * @param array $goods 库存商品
 * @param string $order_sn 订单编号
 */
function update_stock_log($muid, $stock = 1, $goods, $order_sn = '')
{
    $data['ctime'] = time();
    $data['stock'] = $stock;
    $data['muid'] = $muid;
    $data['goods_id'] = $goods['goods_id'];
    $data['goods_name'] = $goods['goods_name'];
    $data['goods_spec'] = empty($goods['spec_key_name']) ? '' : $goods['spec_key_name'];
    $data['store_id'] = $goods['store_id'];
    $data['order_sn'] = $order_sn;
    M('stock_log')->add($data);
}

/**
 * 邮件发送
 * @param $to    接收人
 * @param string $subject 邮件标题
 * @param string $content 邮件内容(html模板渲染后的内容)
 * @throws Exception
 * @throws phpmailerException
 */
function send_email($to, $subject = '', $content = '')
{
    vendor('phpmailer.PHPMailerAutoload');
    //判断openssl是否开启
    $openssl_funcs = get_extension_funcs('openssl');
    if(!$openssl_funcs){
        return array('status'=>-1 , 'msg'=>'请先开启openssl扩展');
    }
    $mail = new PHPMailer;
    $config = tpCache('smtp');
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //调试输出格式
    //$mail->Debugoutput = 'html';
    //smtp服务器
    $mail->Host = $config['smtp_server'];
    //端口 - likely to be 25, 465 or 587
    $mail->Port = $config['smtp_port'];
    if ($mail->Port === 465) $mail->SMTPSecure = 'ssl';// 使用安全协议
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //用户名
    $mail->Username = $config['smtp_user'];
    //密码
    $mail->Password = $config['smtp_pwd'];
    //Set who the message is to be sent from
    $mail->setFrom($config['smtp_user']);
    //回复地址
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    //接收邮件方
    if (is_array($to)) {
        foreach ($to as $v) {
            $mail->addAddress($v);
        }
    } else {
        $mail->addAddress($to);
    }

    $mail->isHTML(true);// send as HTML
    //标题
    $mail->Subject = $subject;
    //HTML内容转换
    $mail->msgHTML($content);
    //Replace the plain text body with one created manually
    //$mail->AltBody = 'This is a plain-text message body';
    //添加附件
    //$mail->addAttachment('images/phpmailer_mini.png');
    //send the message, check for errors
    if (!$mail->send()) {
         return array('status'=>-1 , 'msg'=>'发送失败: '.$mail->ErrorInfo);
    } else {
        return array('status'=>1 , 'msg'=>'发送成功');
    }
}


/**
 * 检测是否能够发送短信
 * @param unknown $scene
 * @return multitype:number string
 */
function checkEnableSendSms($scene)
{

    $scenes = C('SEND_SCENE');
    $sceneItem = $scenes[$scene];
    if (!$sceneItem) {
        return array("status" => -1, "msg" => "场景参数'scene'错误!");
    }
    $key = $sceneItem[2];
    $sceneName = $sceneItem[0];
    $config = tpCache('sms');
    $smsEnable = $config[$key];

    if (!$smsEnable) {
        return array("status" => -1, "msg" => "['$sceneName']发送短信被关闭'");
    }
    //判断是否添加"注册模板"
    $size = M('sms_template')->where("send_scene", $scene)->count('tpl_id');
    if (!$size) {
        return array("status" => -1, "msg" => "请先添加['$sceneName']短信模板");
    }
    return array("status" => 1, "msg" => "可以发送短信");

}

/**
 * 发送短信逻辑
 * @param unknown $scene
 */
function sendSms($scene, $sender, $params,$unique_id=0)
{
    $smsLogic = new \app\common\logic\SmsLogic;
    return $smsLogic->sendSms($scene, $sender, $params, $unique_id);
}

/**
 * 查询快递
 * @param $shipping_code|快递公司编码
 * @param $invoice_no|快递单号
 * @return array  物流跟踪信息数组
 */
function queryExpressInfo($shipping_code, $invoice_no)
{
    $url = "https://m.kuaidi100.com/query?type=" . $shipping_code . "&postid=" . $invoice_no . "&id=1&valicode=&temp=0.49738534969422676";
    $resp = httpRequest($url, "GET");
    return json_decode($resp, true);
}

/**
 * 获取某个商品分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getCatGrandson($cat_id)
{
    $GLOBALS['catGrandson'] = array();
    $GLOBALS['category_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['catGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['category_id_arr'] = M('GoodsCategory')->cache(true, TPSHOP_CACHE_TIME)->getField('id,parent_id');
    // 先把所有儿子找出来
    $son_id_arr = M('GoodsCategory')->where("parent_id", $cat_id)->cache(true, TPSHOP_CACHE_TIME)->getField('id', true);
    foreach ($son_id_arr as $k => $v) {
        getCatGrandson2($v);
    }
    return $GLOBALS['catGrandson'];
}

/**
 * 获取某个文章分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getArticleCatGrandson($cat_id)
{
    $GLOBALS['ArticleCatGrandson'] = array();
    $GLOBALS['cat_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['ArticleCatGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['cat_id_arr'] = M('ArticleCat')->getField('cat_id,parent_id');
    // 先把所有儿子找出来
    $son_id_arr = M('ArticleCat')->where("parent_id", $cat_id)->getField('cat_id', true);
    foreach ($son_id_arr as $k => $v) {
        getArticleCatGrandson2($v);
    }
    return $GLOBALS['ArticleCatGrandson'];
}

/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getCatGrandson2($cat_id)
{
    $GLOBALS['catGrandson'][] = $cat_id;
    foreach ($GLOBALS['category_id_arr'] as $k => $v) {
        // 找到孙子
        if ($v == $cat_id) {
            getCatGrandson2($k); // 继续找孙子
        }
    }
}


/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getArticleCatGrandson2($cat_id)
{
    $GLOBALS['ArticleCatGrandson'][] = $cat_id;
    foreach ($GLOBALS['cat_id_arr'] as $k => $v) {
        // 找到孙子
        if ($v == $cat_id) {
            getArticleCatGrandson2($k); // 继续找孙子
        }
    }
}

/**
 * 获取商品库存, 只有上架的商品才返回库存数量
 * @param type $goods_id 商品id
 * @param type $key 库存 key
 */
function getGoodNum($goods_id, $key)
{
    if (!empty($key)){
        return M("SpecGoodsPrice")
                        ->alias("s")
                        ->join('_Goods_ g ','s.goods_id = g.goods_id','LEFT')
                        ->where(['g.goods_id' => $goods_id, 'key' => $key ,"is_on_sale"=>1])->getField('s.store_count');
    }else{
        return M("Goods")->cache(true,10)->where(array("goods_id"=>$goods_id , "is_on_sale"=>1))->getField('store_count');
    }
}

/**
 * 获取缓存或者更新缓存
 * @param string $config_key 缓存文件名称
 * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
 * @return array or string or bool
 */
function tpCache($config_key, $data = array())
{
    $param = explode('.', $config_key);
    if (empty($data)) {
        //如$config_key=shop_info则获取网站信息数组
        //如$config_key=shop_info.logo则获取网站logo字符串
        $config = F($param[0], '', TEMP_PATH);//直接获取缓存文件
        if (empty($config)) {
            //缓存文件不存在就读取数据库
            $res = D('config')->where("inc_type", $param[0])->select();
            if ($res) {
                foreach ($res as $k => $val) {
                    $config[$val['name']] = $val['value'];
                }
                F($param[0], $config, TEMP_PATH);
            }
        }
        if (count($param) > 1) {
            return $config[$param[1]];
        } else {
            return $config;
        }
    } else {
        //更新缓存
        $result = D('config')->where("inc_type", $param[0])->select();
        if ($result) {
            foreach ($result as $val) {
                $temp[$val['name']] = $val['value'];
            }
            foreach ($data as $k => $v) {
                $newArr = array('name' => $k, 'value' => trim($v), 'inc_type' => $param[0]);
                if (!isset($temp[$k])) {
                    M('config')->add($newArr);//新key数据插入数据库
                } else {
                    if ($v != $temp[$k])
                        M('config')->where("name", $k)->save($newArr);//缓存key存在且值有变更新此项
                }
            }
            //更新后的数据库记录
            $newRes = D('config')->where("inc_type", $param[0])->select();
            foreach ($newRes as $rs) {
                $newData[$rs['name']] = $rs['value'];
            }
        } else {
            foreach ($data as $k => $v) {
                $newArr[] = array('name' => $k, 'value' => trim($v), 'inc_type' => $param[0]);
            }
            M('config')->insertAll($newArr);
            $newData = $data;
        }
        return F($param[0], $newData, TEMP_PATH);
    }
}

/**
 * 记录帐户变动
 * @param int $user_id 用户id
 * @param int $user_money 可用余额变动
 * @param int $pay_points 消费积分变动
 * @param string $desc 变动说明
 * @param int $distribut_money 分佣金额
 * @param int $order_id 订单id
 * @param string $order_sn 订单sn
 * @param $frozen_money 冻结资金
 * @return bool
 */
function accountLog($user_id, $user_money = 0, $pay_points = 0,$desc = '', $distribut_money = 0, $order_id = 0 ,$order_sn = '',$frozen_money=0)
{
    /* 插入帐户变动记录 */
    $account_log = array(
        'user_id' => $user_id,
        'user_money' => $user_money,
        'pay_points' => $pay_points,
        'change_time' => time(),
        'frozen_money' => $frozen_money,
        'desc' => $desc,
        'order_id' => $order_id,
        'order_sn' => $order_sn
    );
    /* 更新用户信息 */
//    $sql = "UPDATE __PREFIX__users SET user_money = user_money + $user_money," .
//        " pay_points = pay_points + $pay_points, distribut_money = distribut_money + $distribut_money WHERE user_id = $user_id";
    $update_data = array(
        'user_money' => ['exp', 'user_money+' . $user_money],
        'pay_points' => ['exp', 'pay_points+' . $pay_points],
        'distribut_money' => ['exp', 'distribut_money+' . $distribut_money],
    );
	if(($user_money+$pay_points+$distribut_money) == 0)
		return false;
    $update = Db::name('users')->where('user_id', $user_id)->update($update_data);
    if ($update) {
        M('account_log')->add($account_log);
        return true;
    } else {
        return false;
    }
}

/**
 * 记录商家的帐户变动
 * @param $store_id 店铺ID
 * @param int $store_money 可用资金
 * @param $pending_money 可用余额变动
 * @param string $desc 变动说明
 * @param int $order_id 订单id
 * @param string $order_sn 订单sn
 * @return bool
 */
function storeAccountLog($store_id, $store_money = 0, $pending_money, $desc = '', $order_id = 0,$order_sn = '')
{
    /* 插入帐户变动记录 */
    $account_log = array(
        'store_id' => $store_id,
        'store_money' => $store_money, // 可用资金
        'pending_money' => $pending_money, // 未结算资金
        'change_time' => time(),
        'desc' => $desc,
        'order_id' => $order_id,
        'order_sn' => $order_sn
    );
    /* 更新用户信息 */
//    $sql = "UPDATE __PREFIX__store SET store_money = store_money + $store_money," .
//        " pending_money = pending_money + $pending_money WHERE store_id = $store_id";
    $update_data = array(
        'store_money' => ['exp', 'store_money+' . $store_money],
        'pending_money' => ['exp', 'pending_money+' . $pending_money],
    );
    $update = Db::name('store')->where('store_id', $store_id)->update($update_data);
    if ($update) {
        M('account_log_store')->add($account_log);
        return true;
    } else {
        return false;
    }
}

/**
 * 订单操作日志
 * 参数示例
 * @param type $order_id 订单id
 * @param type $action_note 操作备注
 * @param type $status_desc 操作状态  提交订单, 付款成功, 取消, 等待收货, 完成
 * @param type $user_id 用户id 默认为管理员
 * @return boolean
 */
function logOrder($order_id, $action_note, $status_desc, $user_id = 0, $user_type = 0)
{
    $status_desc_arr = array('提交订单', '付款成功', '取消', '等待收货', '完成', '退货');
    // if(!in_array($status_desc, $status_desc_arr))
    // return false;

    $order = M('order')->master()->where("order_id", $order_id)->find();
    $action_info = array(
        'order_id' => $order_id,
        'action_user' => $user_id,
        'user_type' => $user_type,
        'order_status' => $order['order_status'],
        'shipping_status' => $order['shipping_status'],
        'pay_status' => $order['pay_status'],
        'action_note' => $action_note,
        'status_desc' => $status_desc, //''
        'log_time' => time(),
    );
    return M('order_action')->add($action_info);
}

/**
 * 获取订单状态的 中文描述名称
 * @param type $order_id 订单id
 * @param type $order 订单数组
 * @return string
 */
function orderStatusDesc($order_id = 0, $order = array())
{
    if (empty($order))
        $order = M('Order')->where("order_id", $order_id)->find();

    // 货到付款
    if ($order['pay_code'] == 'cod') {
        if (in_array($order['order_status'], array(0, 1)) && $order['shipping_status'] == 0)
            return 'WAITSEND'; //'待发货',
    } else // 非货到付款
    {
        if ($order['pay_status'] == 0 && $order['order_status'] == 0)
            return 'WAITPAY'; //'待支付',
        if ($order['pay_status'] == 1 && in_array($order['order_status'], array(0, 1)) && $order['shipping_status'] != 1)
            return 'WAITSEND'; //'待发货',
    }
    if (($order['shipping_status'] == 1) && ($order['order_status'] == 1))
        return 'WAITRECEIVE'; //'待收货',
    if ($order['order_status'] == 2){
        return 'WAITCCOMMENT'; //'待评价',
    }
    if ($order['order_status'] == 3)
        return 'CANCEL'; //'已取消',
    if ($order['order_status'] == 4)
        return 'FINISH'; //'已完成',
    return 'OTHER';
}

/**
 * 获取订单状态的 显示按钮
 * @param type $order_id 订单id
 * @param type $order 订单数组
 * @return array()
 */
function orderBtn($order_id = 0, $order = array())
{
    if (empty($order))
        $order = M('Order')->where("order_id", $order_id)->find();
    /**
     *  订单用户端显示按钮
     * 去支付     AND pay_status=0 AND order_status=0 AND pay_code ! ="cod"
     * 取消按钮  AND pay_status=0 AND shipping_status=0 AND order_status=0
     * 确认收货  AND shipping_status=1 AND order_status=0
     * 评价      AND order_status=1
     * 查看物流  if(!empty(物流单号))
     */
    $btn_arr = array(
        'pay_btn' => 0, // 去支付按钮
        'cancel_btn' => 0, // 取消按钮
        'receive_btn' => 0, // 确认收货
        'comment_btn' => 0, // 评价按钮
        'shipping_btn' => 0, // 查看物流
        'return_btn' => 0, // 退货按钮 (联系客服)
    );

    // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
    if(time() - $order['add_time'] > (86400 * 90))
    {    
        return $btn_arr;
    }
//return $btn_arr;
    // 货到付款
    if ($order['pay_code'] == 'cod') {
        if (($order['order_status'] == 0 || $order['order_status'] == 1) && $order['shipping_status'] == 0) // 待发货
        {
            $btn_arr['cancel_btn'] = 1; // 取消按钮 (联系客服)
        }
        if ($order['shipping_status'] == 1 && $order['order_status'] == 1) //待收货
        {
            $btn_arr['receive_btn'] = 1;  // 确认收货
            $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
        }
    } // 非货到付款
    else {
        if ($order['pay_status'] == 0 && $order['order_status'] == 0) // 待支付
        {
            $btn_arr['pay_btn'] = 1; // 去支付按钮
            $btn_arr['cancel_btn'] = 1; // 取消按钮
        }
        if ($order['pay_status'] == 1 && in_array($order['order_status'], array(0, 1)) && $order['shipping_status'] == 0) // 待发货
        {
            $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
            $btn_arr['cancel_btn'] = 1; // 取消按钮
        }
        if ($order['pay_status'] == 1 && $order['order_status'] == 1 && $order['shipping_status'] == 1) //待收货
        {
            $btn_arr['receive_btn'] = 1;  // 确认收货
            $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
        }
    }
    if ($order['order_status'] == 2) {
        if ($order['is_comment'] == 0) {
            $btn_arr['comment_btn'] = 1;  // 评价按钮
        }
        $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
    }
    if ($order['shipping_status'] != 0 && in_array($order['order_status'], [1,2,4])) {
        $btn_arr['shipping_btn'] = 1; // 查看物流
    }
    if ($order['shipping_status'] == 2 && $order['order_status'] == 1) // 部分发货
    {
        $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
    }

    return $btn_arr;
}

/**
 * 给订单数组添加属性  包括按钮显示属性 和 订单状态显示属性
 * @param type $order
 */
function set_btn_order_status($order)
{
    $order_status_arr = C('ORDER_STATUS_DESC');
    $order['order_status_code'] = $order_status_code = orderStatusDesc(0, $order); // 订单状态显示给用户看的
    $order['order_status_desc'] = $order_status_arr[$order_status_code];
    $orderBtnArr = orderBtn(0, $order);
    return array_merge($order, $orderBtnArr); // 订单该显示的按钮
}


/**
 * 支付完成修改订单
 * $order_sn 订单号
 * $transaction_id  第三方支付交易流水号
 */
function update_pay_status($order_sn, $transaction_id = '')
{
    if (stripos($order_sn, 'recharge') !== false) {
        //用户在线充值
        $order = M('recharge')->where(['order_sn' => $order_sn, 'pay_status' => 0])->find();
        if (!$order) return false;// 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        M('recharge')->where("order_sn", $order_sn)->save(array('pay_status' => 1, 'pay_time' => time(),'transaction_id'=>$transaction_id));
        accountLog($order['user_id'], $order['account'], 0, '会员在线充值');
    } else {
        // 先查看一下 是不是 合并支付的主订单号
        $order_list = M('order')->where("master_order_sn", $order_sn)->select();
        if ($order_list) {
            foreach ($order_list as $key => $val)
                update_pay_status($val['order_sn'], $transaction_id);
            return;
        }
        // 找出对应的订单
        $order = M('order')->master()->where(['order_sn' => $order_sn, 'pay_status' => 0])->find();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if (empty($order)) return false; //如果这笔订单已经处理过了
        // 修改支付状态  已支付
        M('order')->where("order_sn", $order_sn)->save(array('pay_status' => 1, 'pay_time' => time(), 'transaction_id' => $transaction_id));
        // 减少对应商品的库存
        minus_stock($order);
        // 给他升级, 根据order表查看消费记录 给他会员等级升级 修改他的折扣 和 总金额
        update_user_level($order['user_id']);
        // 记录订单操作日志
        logOrder($order['order_id'], '订单付款成功', '付款成功', $order['user_id'], 2);
        //分销设置
        M('rebate_log')->where("order_id", $order['order_id'])->save(array('status' => 1));
        // 成为分销商条件
        //$distribut_condition = tpCache('distribut.condition');
        //if($distribut_condition == 1)  // 购买商品付款才可以成为分销商
        //M('users')->where("user_id = {$order['user_id']}")->save(array('is_distribut'=>1));
        // 给商家待结款字段加上
        //$order_settlement = order_settlement($order['order_id']);
        //M('store')->where("store_id", $order['store_id'])->setInc('pending_money', $order_settlement['store_settlement']); // 店铺 待结算资金 累加
        //虚拟服务类商品支付
        if($order['order_prom_type'] == 5){
            $OrderLogic = new \app\common\logic\OrderLogic();
            $OrderLogic->make_virtual_code($order);
        }
        if ($order['order_prom_type'] == 6) {
            $TeamOrderLogic = new \app\common\logic\TeamOrderLogic();
            $TeamOrderLogic->doOrderPayAfter($order);
        }
        // 赠送积分
        //order_give($order);// 调用送礼物方法, 给下单这个人赠送相应的礼物
        //用户支付, 发送短信给商家
        $res = checkEnableSendSms("4");
        if (!$res || $res['status'] != 1) return;

        $store = M('store')->where("store_id", $order['store_id'])->find();
        if (empty($store['service_phone'])) return;
        $sender = $store['service_phone'];
        $params = array('order_id' => $order['order_id']);
        sendSms("4", $sender, $params);
    }
}

/**
 * 订单确认收货
 * @param $id   订单id
 */
function confirm_order($id, $user_id = 0)
{
    $where['order_id'] = $id;
    if ($user_id) {
        $where['user_id'] = $user_id;
    }
    $order = M('order')->where($where)->find();

    if ($order['order_status'] != 1 || empty($order['pay_time']) || $order['pay_status'] != 1)
        return array('status' => -1, 'msg' => '该订单不能收货确认');

    $data['order_status'] = 2; // 已收货
    $data['pay_status'] = 1; // 已付款
    $data['confirm_time'] = time(); //  收货确认时间
    if ($order['pay_code'] == 'cod') {
        $data['pay_time'] = time();
    }
    $row = M('order')->where(array('order_id' => $id))->save($data);
    if (!$row)
        return array('status' => -3, 'msg' => '操作失败');
    if($order['order_prom_type'] != 5){  //不是虚拟订单送东西
        order_give($order);
    }
    //分销设置
    M('rebate_log')->where(['order_id' => $id, 'status' => ['lt', 4]])->save(array('status' => 2, 'confirm' => time()));

    return array('status' => 1, 'msg' => '操作成功');
}

/**
 * 下单赠送活动：优惠券，积分
 * @param $order|订单数组
 */
function order_give($order)
{
    //促销优惠订单商品
    $prom_order_goods = M('order_goods')->where(['order_id' => $order['order_id'], 'prom_type' => 3])->select();
    //获取用户会员等级
//    $user_level = Db::name('users')->where(['user_id' => $order['user_id']])->getField('level');
    
    if($prom_order_goods){
    	//查找购买商品送优惠券活动
    	foreach ($prom_order_goods as $val) {
    		$prom_goods = M('prom_goods')->where(['store_id' => $order['store_id'], 'type' => 3, 'id' => $val['prom_id']])->find();
    		if ($prom_goods) {
    			//查找优惠券模板
    			$goods_coupon = M('coupon')->where("id", $prom_goods['expression'])->find();
    			// 用户会员等级是否符合送优惠券活动
//    			if (array_key_exists($user_level, array_flip(explode(',', $prom_goods['group'])))) {  //多商家暂时无这个限制
    				//优惠券发放数量验证，0为无限制。发放数量-已领取数量>0
    				if ($goods_coupon['createnum'] == 0 ||
    				($goods_coupon['createnum'] > 0 && ($goods_coupon['createnum'] - $goods_coupon['send_num']) > 0)
    				) {
    					$data = array(
                            'cid' => $goods_coupon['id'],
                            'type' => $goods_coupon['type'],
                            'uid' => $order['user_id'],
                            'send_time' => time(),
                            'store_id'  => $goods_coupon['store_id'],
                            'get_order_id' => $order['order_id'],
                        );
    					M('coupon_list')->add($data);
    					// 优惠券领取数量加一
    					M('Coupon')->where("id", $goods_coupon['id'])->setInc('send_num');
    				}
//    			}
    		}
    	}
    }

    //查找订单满额促销活动
    $prom_order_where = [
        'store_id' => $order['store_id'],
        'type' => ['gt', 1],
        'end_time' => ['gt', $order['pay_time']],
        'start_time' => ['lt', $order['pay_time']],
        'money' => ['elt', $order['goods_price']]
    ];
    $prom_orders = M('prom_order')->where($prom_order_where)->order('money desc')->select();
    $prom_order_count = count($prom_orders);
    // 用户会员等级是否符合送优惠券活动
    for ($i = 0; $i < $prom_order_count; $i++) {
//        if (array_key_exists($user_level, array_flip(explode(',', $prom_orders[$i]['group'])))) {  //多商家暂时无这个限制
            $prom_order = $prom_orders[$i];
            if ($prom_order['type'] == 3) {
                //查找订单送优惠券模板
                $order_coupon = M('coupon')->where("id", $prom_order['expression'])->find();
                if ($order_coupon) {
                    //优惠券发放数量验证，0为无限制。发放数量-已领取数量>0
                    if ($order_coupon['createnum'] == 0 ||
                        ($order_coupon['createnum'] > 0 && ($order_coupon['createnum'] - $order_coupon['send_num']) > 0)
                    ) {
                        $data = array(
                            'cid' => $order_coupon['id'],
                            'type' => $order_coupon['type'],
                            'uid' => $order['user_id'],
                        	'order_id' => $order['order_id'],
                            'send_time' => time(),
                            'store_id' => $order['store_id'],
                            'get_order_id' => $order['order_id'],
                        );
                        M('coupon_list')->add($data);
                        M('Coupon')->where("id", $order_coupon['id'])->setInc('send_num'); //优惠券领取数量加一
                    }
                }
//            }
            //购买商品送积分
            if ($prom_order['type'] == 2) {
                accountLog($order['user_id'], 0, $prom_order['expression'], "订单活动赠送积分");
            }
            break;
        }
    }
    $points = M('order_goods')->where("order_id", $order['order_id'])->sum("give_integral * goods_num");
    $points && accountLog($order['user_id'], 0, $points, "下单赠送积分", 0, $order['order_id'], $order['order_sn']);
}

/**
 * 查看订单是否满足条件参加活动
 * @param order_amount 订单应付金额
 * @param store_id  店铺id
 */
function get_order_promotion($order_amount, $store_id)
{
//	$parse_type = array('0'=>'满额打折','1'=>'满额优惠金额','2'=>'满额送倍数积分','3'=>'满额送优惠券','4'=>'满额免运费');
    $now = time();
    $where = array(
        'store_id' => $store_id,
        'type' => array('lt', 2),
        'end_time' => array('gt', $now),
        'start_time' => array('lt', $now),
        'money' => array('elt', $order_amount)
    );
    $prom = M('prom_order')->where($where)->order('money desc')->find();
    $res = array('order_amount' => $order_amount, 'order_prom_id' => 0, 'order_prom_amount' => 0);
    if ($prom) {
        if ($prom['type'] == 0) {
            $res['order_amount'] = round($order_amount * $prom['expression'] / 100, 2);//满额打折
            $res['order_prom_amount'] = $order_amount - $res['order_amount'];
            $res['order_prom_id'] = $prom['id'];
            $res['order_prom_title'] = $prom['title'];//优惠标题
        } elseif ($prom['type'] == 1) {
            $res['order_amount'] = $order_amount - $prom['expression'];//满额优惠金额
            $res['order_prom_amount'] = $prom['expression'];
            $res['order_prom_id'] = $prom['id'];
            $res['order_prom_title'] = $prom['title'];//优惠标题
        }
    }
    return $res;
}

/**
 * 计算订单金额
 * @param type $user_id 用户id
 * @param type $order_goods 购买的商品
 * @param type $shipping_code 物流code  数组
 * @param type $shipping_price 数组 物流费用, 如果传递了物流费用 就不在计算物流费
 * @param type $province 省份
 * @param type $city 城市
 * @param type $district 县
 * @param type $pay_points 积分   数组
 * @param type $user_money 余额
 * @param type $coupon_id 优惠券  数组
 * @param type $couponCode 优惠码 数组
 */
function calculate_price($user_id = 0, $order_goods = '', $shipping_code = array(), $province = 0, $city = 0, $district = 0, $pay_points = 0, $user_money = 0, $coupon_id = array(), $couponCode = array())
{
    $couponLogic = new \app\common\logic\CouponLogic();
    $goodsLogic = new \app\common\logic\GoodsLogic();
    if (empty($order_goods)){
        return array('status' => -9, 'msg' => '商品列表不能为空', 'result' => '');
    }
    $use_percent_point = tpCache('shopping.point_use_percent');     //最大使用限制: 最大使用积分比例, 例如: 为50时, 未50% , 那么积分支付抵扣金额不能超过应付金额的50%
    if($pay_points>0 && $use_percent_point == 0){
        return array('status' => -1, 'msg' => "该笔订单不能使用积分", 'result' => '积分'); // 返回结果状态
    }
    $user = M('users')->where("user_id", $user_id)->find();// 找出这个用户
    // 判断使用积分 余额
    if ($pay_points && ($pay_points > $user['pay_points'])){
        return array('status' => -5, 'msg' => "你的账户可用积分为:" . $user['pay_points'], 'result' => ''); // 返回结果状态
    }

    if ($user_money && ($user_money > $user['user_money'])){
        return array('status' => -6, 'msg' => "你的账户可用余额为:" . $user['user_money'], 'result' => ''); // 返回结果状态
    }
    $goods_id_arr = get_arr_column($order_goods, 'goods_id');
    $goods_arr = M('goods')->where("goods_id", "in", implode(',', $goods_id_arr))->cache(true, TPSHOP_CACHE_TIME)->getField('goods_id,weight,market_price,is_free_shipping'); // 商品id 和重量对应的键值对

    $shippingIsOk = 1;//所选商品中是否都能配送1：是；-1：不是
    $store_goods_shipping = [];
    foreach ($order_goods as $key => $val) {
        //如果商品不是包邮的
        if ($goods_arr[$val['goods_id']]['is_free_shipping'] == 0) {
            $store_goods_weight[$val['store_id']] += $goods_arr[$val['goods_id']]['weight'] * $val['goods_num']; //累积商品重量 每种商品的重量 * 数量
        }
        $order_goods[$key]['goods_fee'] = $val['goods_num'] * $val['member_goods_price'];    // 小计
        $order_goods[$key]['store_count'] = getGoodNum($val['goods_id'], $val['spec_key']); // 最多可购买的库存数量
        if ($order_goods[$key]['store_count'] <= 0 || $order_goods[$key]['store_count'] < $order_goods[$key]['goods_num'])
            return array('status' => -10, 'msg' => $order_goods[$key]['goods_name'] . '，' . $val['spec_key'] . "库存不足,请重新下单", 'result' => '');

        $cut_fee += $val['goods_num'] * $val['market_price'] - $val['goods_num'] * $val['member_goods_price']; // 共节约
        $anum += $val['goods_num']; // 购买数量
        $goods_price += $order_goods[$key]['goods_fee']; // 商品总价
        $store_goods_price[$val['store_id']] += $order_goods[$key]['goods_fee']; // 每个商家 的商品总价
        $store_goods_shipping[$val['goods_id']] = $goodsLogic->getGoodsDispatching($val['goods_id'], $district);
        if ($store_goods_shipping[$val['goods_id']]['status'] != 1) {
            $shippingIsOk = -1;
        }
    }
    if ($shippingIsOk != 1) {
        return array('status' => -22, 'msg' => "订单中部分商品不支持对当前地址的配送请返回购物车修改", 'result' => ['goods_shipping'=>$store_goods_shipping]);
    }

    // 因为当前方法在没有user_id 的情况下也可以调用, 因此 需要判断用户id
    if ($user_id) {
        // 循环优惠券
        if($coupon_id){
            foreach ($coupon_id as $key => $value){
                $store_coupon_price[$key] = $couponLogic->getCouponMoney($user_id, $value, $key); // 下拉框方式选择优惠券
            }
        }
        if($couponCode){
            //循环优惠券码
            foreach ($couponCode as $key => $value) {
                if (empty($value))
                    continue;
                $coupon_result = $couponLogic->getCouponMoneyByCode($value, $store_goods_price[$key], $key); // 根据 优惠券 号码获取的优惠券
                if ($coupon_result['status'] < 0)
                    return $coupon_result;
                $store_coupon_price[$key] = $coupon_result['result'];
            }
        }
    }

    // 所有 商家优惠券抵消金额
    if (empty($store_coupon_price)) {
        $coupon_price = 0;
    } else {
        $coupon_price = array_sum($store_coupon_price);
    }

    // 计算每个商家的物流费
    foreach ($shipping_code as $key => $value) {
        // 默认免邮费
        $store_shipping_price[$key] = 0;
        // 超出该金额免运费， 店铺 设置 满多少 包邮 .
        $store_free_price = M('store')->where("store_id", $key)->cache(true, 100)->getField('store_free_price');
        // 如果没有设置满额包邮 或者 额度达不到包邮 则计算物流费
        if ($store_free_price == 0 || $store_goods_price[$key] < $store_free_price)
            $store_shipping_price[$key] = $goodsLogic->getFreight($shipping_code[$key], $province, $city, $district, $store_goods_weight[$key], $key);
    }
    $shipping_price = array_sum($store_shipping_price); // 所有 商家物流费

    // 计算每个商家的应付金额
    foreach ($store_goods_price as $k => $v) {
        $store_order_amount[$k] = $v - $store_coupon_price[$k]; // 应付金额  = 商品价格 - 优惠券
        $order_prom = get_order_promotion($store_order_amount[$k], $k); // 拿应付金额再去计算商家的订单活动  看看商家有没订单满额优惠活动
        $store_order_prom_id[$k] = $order_prom['order_prom_id']; // 订单优惠活动id
        $store_order_prom_title[$k] = $order_prom['order_prom_title']; // 优惠标题
        $store_order_prom_amount[$k] = $order_prom['order_prom_amount']; // 订单优惠了多少钱
        $store_order_amount[$k] = $order_prom['order_amount']+$store_shipping_price[$k]; // 订单优惠后是多少钱 + 物流费 得出  应付金额
    }
    $prom_amount=array_sum($store_order_prom_amount);
    // 最终应付金额 = 商品价格 + 物流费 - 优惠券 - 积分 - 余额 - 优惠活动金额
    $order_amount = $goods_price + $shipping_price - $coupon_price-$prom_amount;
    // 订单总价 = 商品总价 + 物流总价
    $total_amount = $goods_price + $shipping_price;

    // 余额支付原理等同于积分
    $user_money = ($user_money > $order_amount) ? $order_amount : $user_money;
    $order_amount = $order_amount - $user_money; //余额支付抵应付金额

    /*判断能否使用积分
     1..积分低于point_min_limit时,不可使用
     2.在不使用积分的情况下, 计算商品应付金额
     3.原则上, 积分支付不能超过商品应付金额的50%, 该值可在平台设置
    */
    $point_rate = tpCache('shopping.point_rate'); //兑换比例: 如果拥有的积分小于该值, 不可使用
    $min_use_limit_point = tpCache('shopping.point_min_limit'); //最低使用额度: 如果拥有的积分小于该值, 不可使用
    
    if ($min_use_limit_point > 0 && $pay_points > 0 && $pay_points < $min_use_limit_point) {
        return array('status' => -1, 'msg' => "您使用的积分必须大于{$min_use_limit_point}才可以使用", 'result' => ''); // 返回结果状态
    }
    // 计算该笔订单最多使用多少积分
    $limit = $order_amount * ($use_percent_point / 100) * $point_rate;
    if (($use_percent_point != 100) && $pay_points > $limit) {
        return array('status' => -1, 'msg' => "该笔订单, 您使用的积分不能大于{$limit}", 'result' => '积分'); // 返回结果状态
    }

    // 积分支付 100 积分等于 1块钱
    $integral_money = ($pay_points / $point_rate);
    $integral_money = ($integral_money > $order_amount) ? $order_amount : $integral_money; // 假设应付 1块钱 而用户输入了 200 积分 2块钱, 那么就让 $pay_points = 1块钱 等同于强制让用户输入1块钱
    $pay_points = $integral_money * $point_rate; //以防用户使用过多积分的情况
    $order_amount = $order_amount - $integral_money; //  积分抵消应付金额

    // 计算每个商家平摊积分余额  和 余额
    $sum_store_order_amount = array_sum($store_order_amount);
    foreach ($store_order_amount as $k => $v) {
        // 当前的应付金额 除以所有商家累加的应付金额,  算出当前应付金额的占比
        $proportion = $v / $sum_store_order_amount;
        if ($pay_points > 0) {
            $store_point_count[$k] = (int)($proportion * $pay_points);
            $store_order_amount[$k] -= $store_point_count[$k] / tpCache('shopping.point_rate'); // 每个商家减去对应积分抵消的余额
        }
        if ($user_money > 0) {
            $store_balance[$k] = round($proportion * $user_money, 2); // 每个商家平摊用了多少余额  保留两位小数
            $store_order_amount[$k] -= $store_balance[$k]; // 每个商家减去余额支付抵消的
        }
        $store_order_amount[$k] = round($store_order_amount[$k], 2);
    }
    // 如果出现除数 除不尽的, 则最后一位加一
    if ($pay_points && array_sum($store_point_count) != $pay_points) {
        $store_point_count[$k] += 1;
        $store_order_amount[$k] -= (1 / $point_rate); // 最后一个积分也算上去
    }

    //订单总价  应付金额  物流费  商品总价 节约金额 共多少件商品 积分  余额  优惠券
    $result = array(
        'total_amount' => $total_amount, // 订单总价
        'order_amount' => $order_amount, // 应付金额      只用于订单在没有参与优惠活动的时候价格是对的, 如果某个商家参与优惠活动 价格会有所变动
        'goods_price' => $goods_price, // 商品总价
        'cut_fee' => $cut_fee, // 共节约多少钱
        'anum' => $anum, // 商品总共数量
        'integral_money' => $integral_money,  // 积分抵消金额
        'user_money' => $user_money, // 使用余额
        'coupon_price' => $coupon_price,// 优惠券抵消金额
        'order_goods' => $order_goods, // 商品列表 多加几个字段原样返回
        'shipping_price' => $shipping_price, // 物流费
        'store_order_prom_amount' => $store_order_prom_amount,// 订单优惠了多少钱
        'store_order_prom_title' => $store_order_prom_title,// 优惠标题
        'store_order_prom_id' => $store_order_prom_id,// 订单优惠活动id
        'store_order_amount' => $store_order_amount, // 订单优惠后是多少钱
        'store_shipping_price' => $store_shipping_price, //每个商家的物流费
        'store_coupon_price' => $store_coupon_price, //每个商家的优惠券金额
        'store_goods_price' => $store_goods_price,//  每个店铺的商品总价
        'store_point_count' => $store_point_count, // 每个商家平摊使用了多少积分
        'store_balance' => $store_balance, // 每个商家平摊用了多少余额
        'goods_shipping'=>$store_goods_shipping
    );
    return array('status' => 1, 'msg' => "计算价钱成功", 'result' => $result); // 返回结果状态
}

/**
 * 订单结算
 * author:当燃
 * date:2016-08-28
 * @param $order_id  订单order_id
 * @param $rec_id 需要退款商品rec_id
 */

function order_settlement($order_id)
{
    $order = M('order')->where(array('order_id' => $order_id,'pay_status'=>1))->find();//订单详情
    
    if ($order) {
    	
        $order['store_settlement'] = $order['shipping_price'];//商家待结算初始金额
        
        $order_goods = M('order_goods')->where(array('order_id' => $order_id))->select();//订单商品
        
        $order['return_totals'] = $prom_and_coupon = $order['settlement'] = $distribut = 0;
        
        $give_integral = $order['store_settlement'] = $order['refund_integral'] = 0;
        
        /* 商家订单商品结算公式(独立商家一笔订单计算公式)
        *  均摊比例 = 这个商品总价/订单商品总价
        *  均摊优惠金额  = 均摊比例 *(代金券抵扣金额 + 优惠活动优惠金额)
        *  商品实际售卖金额  =  商品总价 - 购买此商品赠送积分 - 此商品分销分成 - 均摊优惠金额
        *  商品结算金额  = 商品实际售卖金额 - 商品实际售卖金额*此类商品平台抽成比例
        *  订单实际支付金额  =  订单商品总价 - 代金券抵扣金额 - 优惠活动优惠金额(跟用户使用积分抵扣，使用余额支付无关,积分在商家赠送时平台已经扣取)
        *
        *  整个订单商家结算所得金额  = 所有商品结算金额之和 + 物流费用(商家发货，物流费直接给商家)
        *  平台所得提成  = 所有商品提成之和
        *  商品退款说明 ：如果使用了积分，那么积分按商品均摊退回给用户，但使用优惠券抵扣和优惠活动优惠的金额此商品均摊的就不退了
        *  积分说明：积分在商家赠送时，直接从订单结算金中扣取该笔赠送积分可抵扣的金额
        *  优惠券赠送使用说明 ：优惠券在使用的时直接抵扣商家订单金额,无需跟平台结算，全场通用劵只有平台可以发放，所以由平台自付
        *  交易费率：例如支付宝，微信都会征收交易的千分之六手续费
        */
        
        $point_rate = tpCache('shopping.point_rate');
        $point_rate = 1 / $point_rate; //积分换算比例
        
        foreach ($order_goods as $k => $val) {
        	
            $settlement = $goods_amount = $val['member_goods_price'] * $val['goods_num']; //此商品该结算金额初始值

            $settlement_rate = round($goods_amount / $order['goods_price'], 4);//此商品占订单商品总价比例

            if ($val['give_integral'] > 0 && $val['is_send']<3) {
                $settlement = $settlement - $val['goods_num'] * $val['give_integral'] * $point_rate;//减去购买该商品赠送积分
            }

            if ($val['distribut'] > 0) {
                $settlement = $settlement - $val['distribut'] * $val['goods_num'];//减去分销分成金额
            }

            //均摊优惠金额  = 此商品总价/订单商品总价*优惠总额
            if ($order['order_prom_amount'] > 0 || $order['coupon_price'] > 0) {
                $prom_and_coupon = $settlement_rate * ($order['order_prom_amount'] + $order['coupon_price']);
                $settlement = $settlement - $prom_and_coupon;//减去优惠券抵扣金额和优惠折扣
            }
            
            if ($val['is_send'] == 3) {
				$return_info = M('return_goods')->where(array('rec_id'=>$val['rec_id']))->find();
            	$order['return_totals'] += $return_info['refund_deposit'] + $return_info['refund_money']; //退款退还金额
            	$order['refund_integral'] += $return_info['refund_integral'];//退款退还积分
            	$order_goods[$k]['settlement'] = 0;
            	$order_goods[$k]['goods_settlement'] = 0;
            }else{
            	$order_goods[$k]['settlement'] = round($settlement * $val['commission']/100, 2);//每件商品平台抽成所得
            	$order_goods[$k]['goods_settlement'] = round($settlement, 2) - $order_goods[$k]['settlement'];//每件商品该结算金额
            	$give_integral = $val['give_integral'] * $val['goods_num'];//订单赠送积分
            	$distribut = $val['distribut'] * $val['goods_num'];//订单分销分成
            }
            
            $order['store_settlement'] += $order_goods[$k]['goods_settlement']; //订单所有商品结算所得金额之和
            $order['settlement'] += $order_goods[$k]['settlement'];//平台抽成之和
            $order['give_integral'] += $give_integral;
            $order['distribut'] += $distribut;
            $order['integral'] = $order['integral'] - $order['refund_integral'];//订单使用积分
            $order['goods_amount'] += $goods_amount;//订单商品总价
        }
        
        $order['store_settlement'] += $order['shipping_price'];//整个订单商家结算所得金额
        //$order['store_settlement'] = round($order['store_settlement']*(1-0.006),2);//支付手续费
    }

    return $order;
}

/**
 * 获取商品一二三级分类
 * @return type
 */
function get_goods_category_tree()
{
    $result = S('common_get_goods_category_tree');
    if($result)  
        return $result;
    $tree = $arr = $brr = $crr = $hrr = $result = array();
    $cat_list = M('goods_category')->where("is_show", 1)->order('sort_order')->cache(true)->select();//所有分类
    if($cat_list){
    	foreach ($cat_list as $val) {
    		if ($val['level'] == 2) {
    			$arr[$val['parent_id']][] = $val;
    			if($val['is_hot'] == 1){
    				$hrr[$val['parent_id']][] = $val;
    			}
    		}
    		
    		if ($val['level'] == 3) {
    			$crr[$val['parent_id']][] = $val;
    			$path = explode('_', $val['parent_id_path']);
    			if($val['is_hot'] == 0 && count($brr[$path[1]])<12){
    				$brr[$path[1]][] = $val;//楼层左下方三级分类
    			}else if($val['is_hot'] == 1 && count($hrr[$path[1]])<6){
    				$hrr[$path[1]][] = $val;//导航栏右边推荐分类
    			}
    		}

    		if ($val['level'] == 1) {
    			$tree[] = $val;
    		}
    	}
    	
    	foreach ($arr as $k => $v) {
    		foreach ($v as $kk => $vv) {
    			$arr[$k][$kk]['sub_menu'] = empty($crr[$vv['id']]) ? array() : $crr[$vv['id']];//导航栏右侧三级分类
    		}
    	}
    	
    	foreach ($tree as $val) {
    		$val['hmenu'] = empty($hrr[$val['id']]) ? array() : $hrr[$val['id']];//导航栏右侧推荐分类
    		$val['smenu'] = empty($brr[$val['id']]) ? array() : $brr[$val['id']];//楼层三级分类
    		$val['tmenu'] = empty($arr[$val['id']]) ? array() : $arr[$val['id']];//楼层以及导航栏二级分类
    		$result[$val['id']] = $val;
    	}
    }
    S('common_get_goods_category_tree',$result);
    return $result;
}

/**
 * 写入静态页面缓存
 */
function write_html_cache($html){
    $html_cache_arr = C('HTML_CACHE_ARR');    
    $m_c_a_str = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME; // 模块_控制器_方法
    $m_c_a_str = strtolower($m_c_a_str);
  
    // 如果是首页直接生成静态页面
    if('home_index_index' == $m_c_a_str)
    {
        //file_put_contents('./index.html', $html);         
    }
    
    //exit('write_html_cache写入缓存<br/>');
    foreach($html_cache_arr as $key=>$val)
    {
        $val['mca'] = strtolower($val['mca']);
        if ($val['mca'] != $m_c_a_str) //不是当前 模块 控制器 方法 直接跳过
            continue;

        //if(!is_dir(RUNTIME_PATH.'html'))
            //mkdir(RUNTIME_PATH.'html');
        //$filename =  RUNTIME_PATH.'html'.DIRECTORY_SEPARATOR.$m_c_a_str;
        $filename =  $m_c_a_str;
        // 组合参数  
        if(isset($val['p']))
        {
            foreach($val['p'] as $k=>$v)
                $filename.='_'.$_GET[$v];
        }
        //echo $filename.= '.html';
        \think\Cache::set($filename,$html,$val['t']);
        //file_put_contents($filename, $html);
    }
}

/**
 * 读取静态页面缓存
 */
function read_html_cache(){
    $html_cache_arr = C('HTML_CACHE_ARR');    
    $m_c_a_str = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME; // 模块_控制器_方法
    $m_c_a_str = strtolower($m_c_a_str);
    //exit('read_html_cache读取缓存<br/>');
    foreach($html_cache_arr as $key=>$val)
    {
        $val['mca'] = strtolower($val['mca']);
        if ($val['mca'] != $m_c_a_str) //不是当前 模块 控制器 方法 直接跳过
            continue;

        //$filename =  RUNTIME_PATH.'html'.DIRECTORY_SEPARATOR.$m_c_a_str;
        $filename =  $m_c_a_str;
        // 组合参数        
        if(isset($val['p']))
        {
            foreach($val['p'] as $k=>$v)
                $filename.='_'.$_GET[$v];
        }
        $filename.= '.html';
        $html = \think\Cache::get($filename);
        if($html)
        {                        
            exit($html);
        }
    }
}

/**
 * 清空系统缓存
 */
function cleanCache(){
   // delFile(RUNTIME_PATH);
    \think\Cache::clear(); 
}

/**
 * 获取授权年份
 */
function buyYear()
{
    $buy_year = C('buy_year');
    $years[''] = '近三个月订单';
    $years['_this_year'] = '今年内订单';
    
    while(true)
    {
      if($buy_year == date('Y'))
         break;
      $years2['_'.$buy_year] = $buy_year.'年订单';
      $buy_year++;
    }   
    if($years2)
    {
        krsort($years2);
        $years = array_merge($years,$years2) ;
    } 
    return $years;
}

/**
 * 获取分表操作的表名
 */
function select_year()
{
    if(C('buy_version') == 1)
        return I('select_year');
    else
        return '';
}

/**
 *  根据order_sn 定位表
 */
function getTabByOrdersn($order_sn)
{       
    if(C('buy_version') == 0)
        return '';
    $tabName = '';
    $table_index = M('table_index')->cache(true)->select();    
    // 截取年月日时分秒
    $select_year = substr($order_sn, 0, 14);    
    foreach($table_index as $k => $v)
    {
        if(strcasecmp($select_year,$v['min_order_sn']) >= 0 && strcasecmp($select_year,$v['max_order_sn']) <= 0)
        //if($select_year > $v['min_order_sn'] && $select_year < $v['max_order_sn'])
        {
            $tabName = str_replace ('order','',$v['name']);
            break;
        }
    }
    return $tabName;  
}
/*
 * 根据 order_id 定位表名
 */
function getTabByOrderId($order_id)
{        
    if(C('buy_version') == 0)
        return '';
    
    $tabName = '';    
    $table_index = M('table_index')->cache(true)->select();      
    foreach($table_index as $k => $v)
    {
        if($order_id >= $v['min_id'] && $order_id <= $v['max_id'])
        {
            $tabName = str_replace ('order','',$v['name']);
            break;
        }
    }
    return $tabName;  
}

/**
 * 根据筛选时间 定位表名
 */
function getTabByTime($startTime='', $endTime='')
{
   if(C('buy_version') == 0)
        return '';
   
   $startTime = preg_replace("/[:\s-]/", "", $startTime);  // 去除日期里面的分隔符做成跟order_sn 类似
   $endTime = preg_replace("/[:\s-]/", "", $endTime);
   // 查询起始位置是今年的
   if(substr($startTime,0,4) == date('Y'))
   {
       $table_index = M('table_index')->where("name = 'order'")->cache(true)->find();
       if(strcasecmp($startTime,$table_index['min_order_sn']) >= 0)
               return '';
       else
               return '_this_year';      
   }
   else
   {
       $tabName = '_'.substr($startTime,0,4);
   }   
   $years = buyYear(); 
   $years = array_keys($years);
   return in_array($tabName, $years) ? $tabName : '';    
}

/**
 * 获取完整地址
 */
function getTotalAddress($province_id, $city_id, $district_id, $twon_id, $address='')
{
    static $regions = null;
    if (!$regions) {
        $regions = M('region')->cache(true)->getField('id,name');
    }
    $total_address  = $regions[$province_id] ?: '';
    $total_address .= $regions[$city_id] ?: '';
    $total_address .= $regions[$district_id] ?: '';
    $total_address .= $regions[$twon_id] ?: '';
    $total_address .= $address ?: '';
    return $total_address;
}

function moneyFormat($money ){
  return  '¥'. number_format($money,2);
}