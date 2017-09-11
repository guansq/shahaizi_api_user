<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 14:34
 */

use think\Validate;
use service\HttpService;
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
