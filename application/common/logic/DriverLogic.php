<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/8
 * Time: 12:40
 */
namespace app\common\logic;

use think\Model;
use think\Page;
use think\Db;

class DriverLogic extends Model{

    /*
     * 得到司导列表
     */
    public function get_driver_list(){
        $where = ['is_driver' => 1, 'drv_id' => ['<>',0]];
        $count = M('seller')->where($where)->count();
        $Page = new Page($count, 10);
        $list = M('seller')->field('seller_id,drv_id,drv_code,head_pic,seller_name')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if(empty($list)){
            return ['status'=>-1,'msg'=>'没有记录'];
        }
        //去计算评分
        foreach($list as &$val){
            $star = M('pack_comment')->where('seller_id',$val['seller_id'])->avg('star');
            $val['star'] = $star;
        }
        $result = [
            'totalPages' => $Page->totalPages,
            'list' => $list
        ];
        return [
            'status' => 1,
            'msg' => '成功',
            'result' => $result
        ];
    }

}