<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 15:34
 */
namespace app\common\logic;

use think\Model;
use think\Db;

class RegionLogic extends Model{
    /*
     * 得到城市信息
     */
    public function get_city_info($where){
        $result = M('region_new')->where($where)->select();
        return $result;
    }

    /*
     * 得到所有城市信息
     */
    public function get_all_city(){
        $allCity = M('region_new')->where(['level'=>['in','3,4'],'parent_id'=>3426])->select();
        return $allCity;

    }

    public function index(){
        $allCity = M('region_new')->select();
        $result = $this->tree($allCity);
        return $result;
    }

    public function tree($data,$pid=0){
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
            $current_child = $this->tree($data,$vo['id']);
            if($current_child){
                $child[$ke]['child'] = $current_child;
            }
        }
        return $child;
    }

    /*
     * 得到热门城市
     */
    public function get_hot_city($id){
        return M('region_new')->where(['is_hot'=>1,'parent_id'=>$id])->select();
    }

    /*
     * 搜索城市
     */
    public function search_city($name){
        return M('region_new')->where(['name'=>['like',"%{$name}%"]])->select();
    }
}