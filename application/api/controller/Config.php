<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 9:21
 */
namespace app\api\controller;
use app\common\logic\ConfigLogic;
use app\common\logic\SuggestionFeedbackTypeLogic;
use think\Request;

class Config extends Base{

    public $configLogic;

    function __construct(){
        $this->configLogic = new ConfigLogic();
    }

    /**
     * @api {GET}   index.php?m=Api&c=Config&a=getCountryNumber    得到国家区号done  管少秋
     * @apiName     getCountryNumber
     * @apiGroup    Config
     *
     */
    public function getCountryNumber(){
        $result = $this->configLogic->get_country_number();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Config&a=getAllConfig    得到所有配置 wxx
     * @apiName     getAllConfig
     * @apiGroup    Config
     *
     */
    public function getAllConfig(){

    }

    /**
     * @api {GET}   /index.php?m=Api&c=Config&a=aboutUs    关于我们 h5页面 wxx
     * @apiName     aboutUs
     * @apiGroup    Config
     *
     */
    public function aboutUs(){

    }

    /**
     * @api {GET}   /index.php?m=Api&c=Config&a=helpCenter    帮助中心 h5页面 wxx
     * @apiName     helpCenter
     * @apiGroup    Config
     *
     */
    public function helpCenter(){

    }

    /**
     * @api {GET}   /api/config/feedBackType    获取意见反馈类型 ok wxx
     * @apiName     feedBack
     * @apiGroup    Config
     *
     */
    public function feedBackType(Request $request){
        if(!$request->isGet()){
            return $this->returnJson(4000);
        }
        $sFeedbackTypeLogic =  new SuggestionFeedbackTypeLogic();
        return $this->returnJson($sFeedbackTypeLogic->getList());
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Config&a=feedBack    提交意见反馈 wxx
     * @apiName     feedBack
     * @apiGroup    Config
     *
     */
    public function feedBack(Request $request){
        if(!$request->isPost()){
            return $this->returnJson(4000);
        }

    }


}