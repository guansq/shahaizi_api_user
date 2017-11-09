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
     * @api {GET}   /index.php?m=Api&c=Region&a=getIndexCity     得到国外地区的首级列表done  管少秋
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
     * @api {GET}   /index.php?m=Api&c=Region&a=getChildCity     得到国外地区的子级列表done    管少秋
     * @apiName     getChildCity
     * @apiGroup    Region
     * @apiParam    {Number}    parent_id       把当前的ID字段座位parent_id传过来
     */
    public function getChildCity(){
        $regionLogic = new RegionLogic();
        $where = ['parent_id'=>I('parent_id/d')];//顶级城市
        $parent_id = I('parent_id/d');
        $result = $regionLogic->get_city_info($where);
        if($parent_id == 1){
            foreach($result as $key => &$val){
                if($val['id'] == 7){//中国
                    unset($result[$key]);
                }
            }
        }
        $result = array_merge($result);
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getAllCity  得到国内全部城市（省市信息）done  管少秋
     * @apiName     getAllCity
     * @apiGroup    Region
     */
    public function getAllCity(){
        $regionLogic = new RegionLogic();
        $result = $regionLogic->get_all_city();
        $temp = [];
        $filter_arr = ['县','市辖区','市辖县','省属虚拟市','省直辖行政单位'];
        foreach($result as $val){
            if(!in_array($val['name'],$filter_arr)){
                $temp[] = $val;
            }
        }
        //print_r($temp);die;
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$temp]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getAllCityByCountryId  获取国家下的所有城市 ok wxx
     * @apiName     getAllCityByCountryId
     * @apiGroup    Region
     * @apiParam    {Number} [countryId=中国id]  国家id
     */
    public function getAllCityByCountryId  (){

        $countryId = input('countryId',RegionLogic::CHINA_ID);
        $regionLogic = new RegionLogic();
        $result = $regionLogic->getAllCityByCountryId($countryId);
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getHotCityByCountryId  获取国家下的热门城市 ok wxx
     * @apiName     getHotCityByCountryId
     * @apiGroup    Region
     * @apiParam    {Number} [countryId=中国id]  国家id
     */
    public function getHotCityByCountryId(){

        $countryId = input('countryId',RegionLogic::CHINA_ID);
        $regionLogic = new RegionLogic();
        $result = $regionLogic->getHotCityByCountryId($countryId);
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /*
     * @api {GET}   /index.php?m=Api&c=Region&a=getAllCityInArr  得到国际城市 无限级信息done   管少秋
     * @apiName     getAllCityInArr
     * @apiGroup    Region
     */
    public function getAllCityInArr(){
        $regionLogic = new RegionLogic();
        $result = $regionLogic->index();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getChildHotCity     得到子热门城市done     管少秋
     * @apiName     getChildHotCity
     * @apiGroup    Region
     * @apiParam    {String}    [id]  父级城市ID
     */
    public function getChildHotCity(){
        $id = I('id');
        $regionLogic = new RegionLogic();
        $result = $regionLogic->getChildHotCity($id);
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }


    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getHotCity     得到热门城市 ok wxx
     * @apiName     getHotCity
     * @apiGroup    Region
     */
    public function getHotCity(){
        $regionLogic = new RegionLogic();
        $result = $regionLogic->getHotCity();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=searchCity      搜索城市done    管少秋
     * @apiName     searchCity
     * @apiGroup    Region
     * @apiParam    {String}    name    城市名称
     */
    public function searchCity(){
        $name = I('name');
        $regionLogic = new RegionLogic();
        $result = $regionLogic->search_city($name);
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$result]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getAllCountry       得到所有洲下面的国家   管少秋
     * @apiName     getAllCountry
     * @apiGroup    Region
     */
    public function getAllCountry(){
        $regionLogic = new RegionLogic();
        $return = $regionLogic->get_country();
        $this->ajaxReturn($return);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getCountry       得到所有的国家   管少秋
     * @apiName     getCountry
     * @apiGroup    Region
     */
    public function getCountry(){
        $regionLogic = new RegionLogic();
        $where = ['level'=>2];//顶级城市
        $result = $regionLogic->get_city_info($where);
        $temp = [];
        foreach($result as $val){
            if($val['id'] != 7){
                $temp[] = $val;
            }
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$temp]);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=Region&a=getAllCountryCity       得到所有全部国家城市   管少秋
     * @apiName     getAllCountryCity
     * @apiGroup    Region
     */
    public function getAllCountryCity(){
        $regionLogic = new RegionLogic();
        $list = $regionLogic->get_all_country_city();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功','result'=>$list]);
    }
}