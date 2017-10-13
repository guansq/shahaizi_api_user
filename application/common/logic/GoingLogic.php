<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/13
 * Time: 13:59
 */
namespace app\common\logic;

class GoingLogic extends BaseLogic{
    public static function getGoingInfo(){
        $regionLogic = new RegionLogic();
        $regCtrLogic = new RegionCountryLogic();
        $praiseLogic = new UserPraiseLogic();
        //$sellerLogic = new SellerLogic();
        $usersLogic = new UsersLogic();
        $guideList = M('article_hot_guide')->where('is_hot',1)->order('sort,update_at DESC')->limit(4)->select();//热门攻略

        foreach($guideList as &$val){
            $country =   $regCtrLogic->where('id',$val['country_id'])->value('name');
            $city =  $regionLogic->where('id',$val['city_id'])->value('name');
            $val['city'] ="{$country}·{$city}";
            $val['country'] = $regCtrLogic->where('id',$val['country_id'])->value('name');
            $val['praiseNum'] = $praiseLogic->countPraiseOfGuide($val['guide_id']);
            $val['owner'] =  $usersLogic->getBaseInfoById($val['user_id']);
        }

        //得到可靠司导
        $where = [
            'is_driver' => 1,
            'enabled' => 1,
            'is_reliable' => 1,
        ];
        $drv = M('seller')
            ->field('seller_id,head_pic,seller_name,drv_code,country_id,province,city,plat_start')
            ->where($where)
            ->select();
        foreach($drv as &$val){
            $result = getDrvIno($val['seller_id']);
            $val['province'] = getCityName($val['province']);
            $val['city'] = getCityName($val['country_id']).'.'.getCityName($val['city']);
            $val['star'] = $result['star'];
            $val['line'] = $result['line'];
        }
        return [
            'guideList' => $guideList,
            'reliable_drv' => $drv,
        ];
    }
}