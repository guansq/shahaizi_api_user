<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 15:34
 */

namespace app\common\logic;

use think\Model;

class RegionLogic extends BaseLogic{

    const CHINA_ID = 7;
    protected $table = 'ruit_region';

    /*
     * 得到城市信息
     */
    public function get_city_info($where){
        $result = M('region_country')->where($where)->select();
        return $result;
    }

    /*
     * 得到所有城市信息
     */
    public function get_all_city(){
        $allCity = $this->where(['level' => 2])->select();
        return $allCity;

    }

    public function index(){
        $allCity = M('region_country')->select();
        $result = $this->tree($allCity);
        return $result;
    }

    public function tree($data, $pid = 0){
        $child = array();
        foreach($data as $k => $v){
            if($v['parent_id'] == $pid){
                $child[] = $v;
            }
        }

        if(empty($child)){
            return null;
        }

        foreach($child as $ke => $vo){
            $current_child = $this->tree($data, $vo['id']);
            if($current_child){
                $child[$ke]['child'] = $current_child;
            }
        }
        return $child;
    }

    /*
     * 得到热门城市
     */
    public function getChildHotCity($id){
        return $this->where(['is_hot' => 1, 'parent_id' => $id])->select();
    }

    /*
     * 得到热门城市
     */
    public function getHotCity(){
        return $this->where(['is_hot' => 1, 'level' => 2])->select();
    }

    /*
     * 搜索城市
     */
    public function search_city($name){
        return M('region_country')->where(['name' => ['like', "%{$name}%"]])->select();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:获取某国下的所有城市
     * @param $countryId
     */
    public function getAllCityByCountryId($countryId){
        return $this->where('country_id', $countryId)->where('level', 1)->select();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:获取某国下的热门城市
     * @param $countryId
     */
    public function getHotCityByCountryId($countryId){
        return $this->where('country_id', $countryId)
            ->where('level', 2)
            ->where('is_hot', 1)
            ->select();
    }
}