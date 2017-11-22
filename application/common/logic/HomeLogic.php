<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/6
 * Time: 16:00
 */
namespace app\common\logic;
use think\Model;
use app\common\logic\LocalTalentLogic;
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
                ->join('ruit_users u','l.user_id = u.user_id','LEFT')
                ->where("l.is_admin = 1 OR s.enabled = 1 OR u.is_lock = 0")
                ->where(['is_del'=>0])
                ->limit(10)
                ->order('good_num desc')
                //->fetchSql(true)
                ->select();//当地达人
            //echo $localList;die;
        }else{
            $localList = M('article_local_talent')
                ->field('l.*')
                ->alias('l')
                ->join('ruit_seller s','l.seller_id = s.seller_id','LEFT')
                ->join('ruit_users u','l.user_id = u.user_id','LEFT')
                ->where(['l.city'=>['like',"%{$city}%"],'is_del'=>0])
                ->where("l.is_admin = 1 OR s.enabled = 1 OR u.is_lock = 0")
                ->limit(10)
                ->order('good_num desc')
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
            $local = new LocalTalentLogic();
            $val = $local->local_info($val);
        }

        foreach($guideList as &$val){
            //$country =   $regCtrLogic->where('id',$val['country_id'])->value('name');
            //$city =  $regionLogic->where('id',$val['city_id'])->value('name');
            //$val['city'] ="{$country}·{$city}";
            //$val['country'] = $regCtrLogic->where('id',$val['country_id'])->value('name');
            $val['praiseNum'] = $praiseLogic->countPraiseOfGuide($val['guide_id']);
            $val['owner'] =  $usersLogic->getBaseInfoById($val['user_id']);
        }
        //echo '111111111111';die;
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