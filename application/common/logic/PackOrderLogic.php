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
     * 得到 我的包车订单
     */
    public function get_pack_order($type,$user_id){
        if($type == 'all'){
            $where = [
                'status'=> ['in','0,3,4,5'],
                'user_id'=>$user_id
            ];
        }else{
            $where = [
                'status'=>$type,
                'user_id'=>$user_id
            ];
        }
        $count = M('pack_order')->where($where)->count();
        $page = new Page($count,10);//每页10
        $order_list = M('pack_order')->field('order_sn,seller_id,status,title,customer_name,drv_name,create_at,drv_phone,total_price,real_price')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $result = [
            'totalPages' => $page->totalPages,
            'list' => $order_list
        ];
        if(empty($order_list)){
            $return = [
                'status' => -1,
                'msg' => '数据为空'
            ];
        }else{
            $return = [
                'status' => 1,
                'msg' => '成功',
                'result' => $result
            ];
        }
        return $return;
    }
}