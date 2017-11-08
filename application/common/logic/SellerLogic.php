<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: lhb
 * Date: 2017-05-15
 */

namespace app\common\logic;

/**
 *  逻辑类
 */
class SellerLogic extends BaseLogic{
    protected $table = 'ruit_seller';

    public static function findByDrvCode($drv_code){
        return self::where('drv_code', $drv_code)->find();
    }

    public static function findByDrvId($seller_id){
        return self::where('seller_id', $seller_id)->find();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe: 根据ID查询seller详情
     * @param $id
     */
    public function getInfoById($id){
        $regCouLogic = new RegionCountryLogic();
        $regionLogic = new RegionLogic();
        $seller = $this->find($id);
        if(empty($seller)){
            return [];
        }

        $seller = $seller->toArray();
        //  `province` int(6) DEFAULT '0' COMMENT '省份',
        //  `city` int(6) DEFAULT '0' COMMENT '市区',
        //  `district` int(6) DEFAULT '0' COMMENT '县',
        $seller['country_name'] = $regCouLogic->where('id', $seller['country_id'])->value('name').'';
        $seller['province_name'] = $regionLogic->where('id', $seller['province'])->value('name').'';
        $seller['city_name'] = $regionLogic->where('id', $seller['city'])->value('name').'';
        $seller['district_name'] = $regionLogic->where('id', $seller['district'])->value('name').'';
        $seller['plat_start'] = empty($seller['plat_start']) ? 4 : $seller['plat_start'];
        return $seller;
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe: 根据ID查询seller详情
     * @param $id
     */
    public static function getBaseInfoById($id, $viewerId = 0, $isAnonymous = 0){
        $regCouLogic = new RegionCountryLogic();
        $regionLogic = new RegionLogic();
        $filed = [
            'seller_id' => 'sellerId',
            'nickname',
            'seller_name' => 'sellerName',
            'hx_user_name' => 'hxName',
            'head_pic' => 'avatar',
            'country_id' => 'countryId',
            'city' => 'cityId',
            'plat_start' => 'platStart',
        ];
        $seller = self::field($filed)->find($id);
        if(empty($seller)){
            return [];
        }

        $seller = $seller->toArray();
        $seller['countryName'] = $regCouLogic->where('id', $seller['countryId'])->value('name').'';
        $seller['nickname'] = $isAnonymous == 1 ? hidMiddleStr($seller['nickname']) : $seller['nickname'];
        $seller['cityName'] = $regionLogic->where('id', $seller['city'])->value('name').'';
        $seller['platStart'] = intval($seller['platStart']);
        return $seller;
    }

    public function getCarTypeName($id){
        $ret = M('pack_car_bar')->where('id',$id)->find();
        return $ret;
    }
    public function getCarInfo($id){
        $ret = M('pack_car_info')->where('car_id',$id)->find();
        return $ret;
    }
}