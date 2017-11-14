<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/6
 * Time: 16:00
 */
namespace app\common\logic;
use think\Model;
class HomeLogic extends BaseLogic
{
    public function getHomeInfo($city){
        $regionLogic = new RegionLogic();
        $regCtrLogic = new RegionCountryLogic();
        $praiseLogic = new UserPraiseLogic();
        $sellerLogic = new SellerLogic();
        $usersLogic = new UsersLogic();
        if(empty($city)){
            $localList = M('article_local_talent')
                ->field('l.*')
                ->alias('l')
                ->join('ruit_seller s','l.seller_id = s.seller_id','LEFT')
                ->where("l.is_admin = 1 OR s.enabled = 1")
                ->limit(10)
                ->order('sort asc')
                ->select();//当地达人
        }else{
            $localList = M('article_local_talent')
                ->field('l.*')
                ->alias('l')
                ->join('ruit_seller s','l.seller_id = s.seller_id','LEFT')
                ->where(['l.city'=>['like',"%{$city}%"]])
                ->where("l.is_admin = 1 OR s.enabled = 1")
                ->limit(10)
                ->order('sort asc')
                ->select();//当地达人
        }

        if(empty($city)){
            $where = "(u.is_lock = 0 OR a.user_id = 0) AND a.is_hot = 1";
        }else{
            $where = "(u.is_lock = 0 OR a.user_id = 0) AND a.is_hot = 1 AND a.city like '%{$city}%'";
            //$guideList = M('article_hot_guide')->where(['city'=>['like',"%{$city}%"],'is_hot'=>1])->order('sort,update_at DESC')->limit(4)->select();//热门攻略
        }
        //热门攻略-->用户未被锁定的才可以查出
        $guideList = M('article_hot_guide')->alias('a')
            ->field('a.*')
            ->join('ruit_users u','a.user_id = u.user_id', 'LEFT')
            ->where($where)
            ->order('a.sort,a.update_at DESC')
            ->limit(10)->select();

        //最新动态-->用户未被锁定的才可以查出
        $newList = M('article_new_action')->alias('a')
            ->field('a.*')
            ->join('ruit_users u','a.user_id = u.user_id','LEFT')
            ->where(['u.is_lock'=>0])
            //->fetchSql(ture)
            ->order('a.sort,a.create_at DESC')
            ->limit(10)->select();

        foreach($newList as $key => &$val){
            if(!empty($val['cover_img'])){
                $temp = explode('|',$val['cover_img']);
                $val['cover_img'] = $temp[0];

            }
        }
        //print_r($newList);die;
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
            //1:用户2:司导3:房东4:店主
            if($val['lable'] == 1){
                $user_info = get_user_info($val['user_id'],0);
                $val['name'] = $user_info['nickname'];
            }else{
                $seller_info = get_drv_info($val['user_id']);
                $val['name'] = $seller_info['nickname'];
            }
            //$val['city'] = getCountryName($val['country_id']).'·'.getCityName($val['city_id']);
            $val['good_num'] = $praiseLogic->countLocalTalent($val['talent_id']);
        }
        foreach($guideList as &$val){
            $country =   $regCtrLogic->where('id',$val['country_id'])->value('name');
            $city =  $regionLogic->where('id',$val['city_id'])->value('name');
            $val['city'] ="{$country}·{$city}";
            $val['country'] = $regCtrLogic->where('id',$val['country_id'])->value('name');
            $val['praiseNum'] = $praiseLogic->countPraiseOfGuide($val['guide_id']);
            $val['owner'] =  $usersLogic->getBaseInfoById($val['user_id']);
        }
        foreach($newList as &$val){
            $val['praiseNum'] = $praiseLogic->countPraiseOfDynamic($val['act_id']);
            $val['owner'] =  $usersLogic->getBaseInfoById($val['user_id']);
        }


        return [
            'local' => $localList,
            'hot' => $guideList,
            'new' => $newList
        ];
    }
}