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

use think\Model;

/**
 *  逻辑类
 */
class SellerLogic extends Model{
    protected $table = 'ruit_seller';

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe: 根据ID查询seller详情
     * @param $id
     */
    public function getInfoById($id){
        $regionLogic = new RegionLogic();
        $seller = $this->find($id);
        if(empty($seller)){
            return [];
        }

        $seller = $seller->toArray();
        //  `province` int(6) DEFAULT '0' COMMENT '省份',
        //  `city` int(6) DEFAULT '0' COMMENT '市区',
        //  `district` int(6) DEFAULT '0' COMMENT '县',
        $seller['province_name'] = $regionLogic->where('id',$seller['province'])->value('name');
        $seller['city_name'] = $regionLogic->where('id',$seller['city'])->value('name');
        $seller['district_name'] = $regionLogic->where('id',$seller['district'])->value('name');
        return $seller;
    }

}