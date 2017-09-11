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
    /**
     * @api {GET}   index.php?m=Api&c=Config&a=getCountryNumber    得到国家区号done
     * @apiName     getCountryNumber
     * @apiGroup    Config
     *
     */
    public $configLogic;

    function __construct(){
        $this->configLogic = new ConfigLogic();
    }

    public function getCountryNumber(){
        $result = $this->configLogic->get_country_number();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }
}