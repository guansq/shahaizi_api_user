<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 14:34
 */

use think\Validate;
use service\HttpService;
use service\MsgService;
use DesUtils\DesUtils;
use think\Db;


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
            'msg' => empty($msg) ? '' : $msg,
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
    $arrOrder = $desClass->naturalOrdering([$sendData['rt_appkey'],$sendData['req_time'],$sendData['req_action']]);
    $skArr = explode('_',config('app_access_key'));
    return $desClass->strEnc($arrOrder,$skArr[0],$skArr[1],$skArr[2]);//签名
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
        return '';
    }
    $star = Db::name('pack_comment')->where('seller_id',$seller_id)->avg('star');
    $line = Db::name('pack_line')->where('seller_id',$seller_id)->order('create_at desc')->limit(1)->find();
    return  [
        'star' => $star,
        'line' => $line
    ];
}

/*
 * 得到评价星级type 6为路线
 */

function getLineStar($seller_id,$type){
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
function sendSMSbyApi($phone,$content){
    $msgService = new MsgService();
    $str = '【傻孩子APP】'.$content;
    $result = $msgService->sendSms($phone,$str);
    return $result;
}

/*
 * 推送信息 推送给货主为$rt_key='wztx_shipper' 推送给司机为 $rt_key='wztx_driver'
 */
function pushInfo($token,$title,$content,$rt_key='wztx_driver',$type='private'){
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
            "extras" => ['type'=>$type]
        ]
    ];
    $desClass = new DesUtils();
    $arrOrder = $desClass->naturalOrdering([$sendData['rt_appkey'],$sendData['req_time'],$sendData['req_action']]);
    $skArr = explode('_',config('app_access_key'));
    $sendData['sign'] = $desClass->strEnc($arrOrder,$skArr[0],$skArr[1],$skArr[2]);//签名
    $result = HttpService::post(getenv('APP_API_HOME').'push',http_build_query($sendData));
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
    $arrOrder = $desClass->naturalOrdering([$sendData['rt_appkey'],$sendData['req_time'],$sendData['req_action']]);
    $skArr = explode('_',config('app_access_key'));
    $sendData['sign'] = $desClass->strEnc($arrOrder,$skArr[0],$skArr[1],$skArr[2]);//签名
    $result = HttpService::post('http://mps.ruitukeji.com/SendEmail/sendHtml',http_build_query($sendData));
    return json_decode($result,true);
}



/*
 * 时间的处理
 */
function shzDate($time){
    if(empty($time)){
        return $time;
    }
    return strval(date('Y-m-d H:i:s',$time));
}

/*
 * 金钱的处理-->统一后两位小数点
 */
function shzMoney($num,$ispre = false){
    $num = $num > 0 ? $num : 0;
    //number_format(10000, 2, '.', '')
    $formattedNum = number_format($num, 2,'.', '') ;
    if($ispre){
        return '¥'.$formattedNum;
    }else{
        return strval($formattedNum);
    }
}


