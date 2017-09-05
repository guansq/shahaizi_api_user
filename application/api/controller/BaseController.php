<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/12
 * Time: 9:51
 */

namespace app\api\controller;

use jwt\JwtHelper;
use service\ToolsService;
use think\Controller;

class BaseController extends Controller {

    protected $getParams;
    protected $controller;
    protected $action;
    protected $loginUser;

    public function _initialize() {

        parent::_initialize();
        // CORS 跨域 Options 检测响应 允许跨域
        ToolsService::corsOptionsHandler();
        $this->getRequestInfo();
        $this->tokenGrantCheck();
    }


    /**
     * token检测
     */
    public function tokenGrantCheck() {

        //不需要token验证的控制器方法
        $except_controller = [
            "User" => ["login", "reg", "forget", 'computeQlfScore'],
            "Index" => ["apiCode", 'lastApk', 'appConfig', 'sendCaptcha','test','getArticle'],
            "Car" => ['getallcarstyle'],
        ];
        if (!array_key_exists($this->controller, $except_controller) || !in_array($this->action, $except_controller[$this->controller])) {
            $token = request()->header('authorization-token', '');
            $except_token_controller = [
                "Index" => ["home"],
                "Message" => ["index","detail","getunread"],
            ];
            if (array_key_exists($this->controller, $except_token_controller) && in_array($this->action, $except_token_controller[$this->controller])) {
                if(empty($token)){
                    $this->loginUser = '';
                }else{
                    $ret= JwtHelper::checkHomeToken($token);
                    if(empty($ret)){
                        $this->loginUser = '';
                    }else{
                        $this->loginUser = $ret;
                    }
                }
            }else{
                if (empty($token)) {
                    returnJson(4011);
                }
                $this->loginUser = JwtHelper::checkToken($token);
                $spBaseInfo = model('SpBaseInfo', 'logic')->findInfoByUserId($this->loginUser['id']);
                if (empty($spBaseInfo)) {
                    returnJson(4011);
                }
                if($spBaseInfo['is_black'] === 1){
                    returnJson(4000,'用户被加入黑名单');
                }
                $this->loginUser['type'] = $spBaseInfo['type'];
                $this->loginUser['real_name'] = $spBaseInfo['real_name'];
                $this->loginUser['balance'] = $spBaseInfo['balance'];
                $this->loginUser['company_id'] = $spBaseInfo['company_id'];
                if(!empty($spBaseInfo['company_id'])){
                    $this->loginUser['company_name'] = getCompanyName($this->loginUser);
                }
                //$this->loginUser['sup_code'] = $supplierInfo['code'];
                //$this->loginUser['sup_name'] = $supplierInfo['name'];
            }
        }
    }
    /**
     * 获得get参数方法
     */
    protected function getRequestInfo() {
        $this->controller = request()->controller();
        $this->action = request()->action();
        $this->getParams = array_filter(input("param."), function ($v) {
            return $v != "";
        });

    }

    /**
     * 获得请求参参数
     */
    protected function getReqParams($keys = []) {
        $params = input("param.");
        $ret = [];
        //        if(empty($params)){
        //            return [];
        //        }
        if (empty($keys)) {
            return $params;
        }

        foreach ($keys as $k => $v) {
            if (is_numeric($k)) { // 一维数组
                $ret[$v] = array_key_exists($v, $params) ? $params[$v] : '';
                continue;
            }
            $ret[$k] = array_key_exists($k, $params) ? $params[$k] : (empty($v) ? '' : $v);
        }

        return $ret;
    }

    /**
     * 获得分页参参数
     */
    protected function getPagingParams() {

        $DEFAULT_PAGING_PARAMS = config('config_app.DEFAULT_PAGING_PARAMS');
        $pageParams = [
            'page' => input('page', $DEFAULT_PAGING_PARAMS['page']),
            'pageSize' => input('pageSize', $DEFAULT_PAGING_PARAMS['pageSize']),
        ];
        return $pageParams;
    }

    protected function checkLogin($encodeData) {
        //$this->getParams["lal"];
        return JwtHelper::checkToken($encodeData);
    }


    protected function flexClass() {
        $class = new \ReflectionClass($this);
        $flex_methods = (array)$class->getMethods(\ReflectionMethod::IS_PUBLIC);
        array_walk($flex_methods, function ($v) use (&$methods) {
            $current = (array)$v;
            if (trim($current["class"]) != __CLASS__ && trim($current["class"]) != "think\Controller") {
                //                $methods[basename($current["class"])] = $current;
                $methods[basename($current["class"])][] = $current["name"];
            }
        });
        return $methods;
    }

    /**
     * 参数效验
     */
    protected function paramCheck($guards) {
        if (array_key_exists($this->action, $guards)) {
            $paramDiff = implode(",", array_diff($guards[$this->action], array_keys($this->getParams)));
            if ($paramDiff) {
                returnJson(4011);
            }
        }

    }
}