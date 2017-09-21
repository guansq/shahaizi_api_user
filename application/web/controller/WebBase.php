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

namespace app\web\controller;
use think\Controller;

class WebBase extends Controller {
    /**
     * 获得请求参参数
     */
    protected function getReqParams($keys = []){
        $params = input("param.");
        $ret = [];
        //        if(empty($params)){
        //            return [];
        //        }

        if(empty($keys)){
            return $params;
        }

        foreach($keys as $k => $v){
            if(is_numeric($k)){ // 一维数组
                $ret[$v] = array_key_exists($v, $params) ? $params[$v] : '';
                continue;
            }
            $ret[$k] = array_key_exists($k, $params) ? $params[$k] : (!isset($v) ? '' : $v);
        }

        return $ret;
    }

    /**
     * Auther: WILL<314112362@qq.com>
     * Time: 2017-3-20 17:51:09
     * Describe: 根据指定交易规则校验参数
     * @return bool
     */
    function validateParams($params = [], $rule = []){
        if(empty($params)){
            return $this->returnJson(-1, '缺少必要参数.');
        }
        if(empty($rule)){
            foreach($params as $k => $v){
                $rule[$k] = 'require';
            }
        }
        $validate = new Validate($rule);
        if($validate->check($params)){
            return true;
        }
        return $this->returnJson(-1, '', $validate->getError());
    }
}