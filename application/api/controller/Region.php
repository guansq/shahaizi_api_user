<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 15:20
 */

namespace app\api\controller;
use app\common\logic\RegionLogic;

class Region extends Base{

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getIndexCity     得到国外地区的首级列表done
     * @apiName     getIndexCity
     * @apiGroup    Region
     */
    public function getIndexCity(){
        $regionLogic = new RegionLogic();
        $where = ['parent_id'=>0];//顶级城市
        $result = $regionLogic->get_city_info($where);
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getChildCity     得到国外地区的子级列表done
     * @apiName     getChildCity
     * @apiGroup    Region
     * @apiParam    {Number}    parent_id       把当前的ID字段座位parent_id传过来
     */
    public function getChildCity(){
        $regionLogic = new RegionLogic();
        $where = ['parent_id'=>I('parent_id/d')];//顶级城市
        $result = $regionLogic->get_city_info($where);
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getAllCity  得到国内全部城市（一级全部返回）done
     * @apiName     getAllCity
     * @apiGroup    Region
     */
    public function getAllCity(){
        $regionLogic = new RegionLogic();
        $result = $regionLogic->get_all_city();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /*
     * @api {GET}   /index.php?m=Api&c=Region&a=getAllCityInArr  得到国际城市 无限级信息done
     * @apiName     getAllCityInArr
     * @apiGroup    Region
     */
    public function getAllCityInArr(){
        $regionLogic = new RegionLogic();
        $result = $regionLogic->index();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }
}