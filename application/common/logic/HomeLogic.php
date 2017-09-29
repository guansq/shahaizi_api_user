<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/6
 * Time: 16:00
 */
namespace app\common\logic;
use think\Model;
class HomeLogic extends Model
{
    public function getHomeInfo($city){
        $regionLogic = new RegionLogic();
        $regCtrLogic = new RegionCountryLogic();
        if(empty($city)){
            $localList = M('article_local_talent')->limit(4)->order('good_num desc')->select();//当地达人
        }else{
            $localList = M('article_local_talent')->where(['city'=>['like',"%{$city}%"]])->limit(4)->order('good_num desc')->select();//当地达人
        }

        $guideList = M('article_hot_guide')->where('is_hot',1)->order('sort')->limit(4)->select();//热门攻略
        $newList = M('article_new_action')->where('is_hot',1)->order('sort')->limit(4)->select();//最新动态
        foreach($localList as &$val){
            $str = '';
            $type = getIDType($val['seller_id']);
            if(!empty($type['store_id'])){
                $str .= '店主-';
            }
            if(!empty($type['drv_id'])){
                $str .= '司导-';
            }
            if(!empty($type['home_id'])){
                $str .= '房东-';
            }
            if(!empty($str)){
                $val['type_info'] = substr($str,0,-1);
            }else{
                $val['type_info'] = '';
            }
        }
        foreach($guideList as &$val){
            $str = '';
            $type = getIDType($val['seller_id']);
            if(!empty($type['store_id'])){
                $str .= '店主-';
            }
            if(!empty($type['drv_id'])){
                $str .= '司导-';
            }
            if(!empty($type['home_id'])){
                $str .= '房东-';
            }
            if(!empty($str)){
                $val['type_info'] = substr($str,0,-1);
            }else{
                $val['type_info'] = '';
            }
            $val['city'] = $regCtrLogic->where('id',$val['city_id'])->value('name');
            $val['country'] = $regCtrLogic->where('id',$val['reg_children_id'])->value('name');
        }
        foreach($newList as &$val){
            $str = '';
            $type = getIDType($val['seller_id']);
            if(!empty($type['store_id'])){
                $str .= '店主-';
            }
            if(!empty($type['drv_id'])){
                $str .= '司导-';
            }
            if(!empty($type['home_id'])){
                $str .= '房东-';
            }
            if(!empty($str)){
                $val['type_info'] = substr($str,0,-1);
            }else{
                $val['type_info'] = '';
            }
        }


        return [
            'local' => $localList,
            'hot' => $guideList,
            'new' => $newList
        ];
    }
}