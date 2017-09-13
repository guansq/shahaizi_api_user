<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
 * Time: 10:05
 */
namespace app\common\logic;

use think\Model;
use think\Page;


class PackOrderLogic extends Model{

    /*
     * 得到 包车订单
     */
    public function get_pack_order($type){
        $where = ['status'=>$type];
        $count = M('pack_order')->$where($where)->count();
        $page = new Page($count,10);//每页10
        M('pack_order')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
    }
}