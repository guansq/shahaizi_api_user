<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 16:55
 */

namespace app\common\logic;

use think\Model;
use think\Db;

class PackLineLogic extends Model{

    public function get_pack_line(){
        $list = M('pack_line')->field('seller_id,line_id,line_buy_num,line_title,cover_img,line_price,seller_id,line_detail,create_at')->where(['is_comm'=>1])->select();
        foreach($list as &$val){
            $val['star'] = getLineStar($val['seller_id'],6);
            $val['line_detail'] = json_decode(htmlspecialchars_decode($val['line_detail']));
        }
        return $list;
    }

    public function get_local_drv(){
        $drv = M('seller')->field('seller_id,head_pic,seller_name,drv_code,province,city')->where(['is_driver'=>1])->select();
        foreach($drv as &$val){
            $result = getDrvIno($val['seller_id']);
            $val['province'] = getCityName($val['province']);
            $val['city'] = getCityName($val['city']);
            $val['star'] = $result['star'];
            $val['line'] = $result['line'];
        }
        return $drv;
    }
}