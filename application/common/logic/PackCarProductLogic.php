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

    public function getPage(){
        $where = ['is_show' => 1];
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
        ];
        $list = $this->where($where)->field($fields)->order('create_at DESC')->select();
        foreach($list as &$item){
            $item['publishTimeFmt'] = date('Y.m.d', $item['publishTime']);
            $item['priceFmt'] = moneyFormat($item['price']);
            $item['imgs'] = ['http://gimg1.bitautoimg.com/ResourceFiles/0/3/406/20170712111916756.jpg','http://www.sinaimg.cn/qc/photo_auto/photopng/08/02/1470990802.png'];
        }
        $pageVo = new PageVo($page, $list);
        return resultArray(2000, '', $pageVo);
    }


}