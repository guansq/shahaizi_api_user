<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

use ruitu\PageVo;
use think\Model;
use think\Page;

class PackCarProductLogic extends Model{

    protected $table = 'ruit_pack_car_product';

    const TYPE_AIRPLANE = 1;  //接送机
    const TYPE_PACKCAR  = 2;  // 包车


    public function getPageByType($type){
        $where = [
            'is_show' => 1,
            'type' => $type
        ];
        $total = $this->where($where)->count();

        $page = new Page($total);
        if(empty($total)){
            return resultArray(4004);
        }
        $fields = [
            'id',
            'publish_time' => 'publishTime',
            'price',
            'title',
            'img' => 'imgs',
        ];
        $list = $this->where($where)->field($fields)->order('create_at DESC')->select();
        foreach($list as &$item){
            $item['publishTimeFmt'] = date('Y.m.d', $item['publishTime']);
            $item['priceFmt'] = moneyFormat($item['price']);
            // fixme
            $item['imgs'] = explode('|', $item['imgs']);
        }
        $pageVo = new PageVo($page, $list);
        return resultArray(2000, '', $pageVo);
    }


}