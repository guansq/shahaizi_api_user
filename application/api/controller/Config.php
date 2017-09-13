<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 9:21
 */
namespace app\api\controller;
use app\common\logic\ConfigLogic;
class Config extends Base{

    public $configLogic;

    function __construct(){
        $this->configLogic = new ConfigLogic();
    }

    /**
     * @api {GET}   index.php?m=Api&c=Config&a=getCountryNumber    得到国家区号done
     * @apiName     getCountryNumber
     * @apiGroup    Config
     *
     */
    public function getCountryNumber(){
        $result = $this->configLogic->get_country_number();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   index.php?m=Api&c=Config&a=getEmergencyCall    得到紧急电话（待完成）
     * @apiName     getEmergencyCall
     * @apiGroup    Config
     *
     */
    public function getEmergencyCall(){

    }

    /**
     * @api {GET}   index.php?m=Api&c=Config&a=aboutUs    关于我们（待完成）
     * @apiName     aboutUs
     * @apiGroup    Config
     *
     */
    public function aboutUs(){

    }

    /**
     * @api {GET}   index.php?m=Api&c=Config&a=helpCenter    帮助中心（待完成）
     * @apiName     helpCenter
     * @apiGroup    Config
     *
     */
    public function helpCenter(){

    }
}