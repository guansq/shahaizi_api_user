<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/9
 * Time: 16:55
 */

namespace app\common\logic;

use think\Model;
use think\Page;

class PackLineLogic extends Model{

    protected $table = 'ruit_pack_line';

    public function get_all_pack_line($where){
        $count = $this->where($where)->count();
        $page = new Page($count);
        $list = $this->field('seller_id,line_id,line_buy_num,city,line_title,cover_img,line_price,seller_id,line_detail,create_at')
            ->where($where)
            ->order('order_by')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        // dd($this->getLastSql());
        foreach($list as &$val){
            $val['star'] = getLineStar($val['seller_id'], 6);
            $val['line_detail'] = json_decode(htmlspecialchars_decode($val['line_detail']));
            $val['create_at'] = shzDate($val['create_at']);
        }
        $result = [
            'totalPages' => $page->totalPages,
            'list' => $list
        ];
        return $result;
    }

    public function get_pack_line($where){
        $list = M('pack_line')
            ->field('seller_id,line_id,line_buy_num,city,line_title,cover_img,line_price,seller_id,line_detail,create_at,start_time,end_time')
            ->where($where)
            ->limit(9)
            ->select();
        foreach($list as &$val){
            $val['star'] = getLineStar($val['seller_id'], 6);
            $val['line_detail'] = json_decode(htmlspecialchars_decode($val['line_detail']));
            $val['create_at'] = shzDate($val['create_at']);
        }
        return $list;
    }

    public function get_local_drv(){
        $drv = M('seller')
            ->field('seller_id,head_pic,seller_name,drv_code,province,city,plat_start')
            ->where(['is_driver' => 1])
            ->where(['enabled' => 1])
            ->select();
        foreach($drv as &$val){
            $result = getDrvIno($val['seller_id']);
            $val['province'] = getCityName($val['province']);
            $val['city'] = getCityName($val['city']);
            $val['star'] = $result['star'];
            $val['line'] = $result['line'];
        }
        return $drv;
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe: 获取精品线路
     * @return array
     */
    public function getCommPackLine($city = ''){
        $sellerLogic = new SellerLogic();
        $where = [
            'is_state' => 1,
            'is_comm' => 1,
        ];
        if(!empty($city)){
            $where['city'] = ['LIKE', "%$city%"];
        }
        $list = $this->field('seller_id,line_id,line_buy_num,city,line_title,cover_img,line_price,seller_id,line_detail,is_admin,create_at')
            ->where($where)
            ->order('order_by')
            ->limit(3)
            ->select();
        foreach($list as &$val){
            $val['star'] = getLineStar($val['seller_id'], 6);
            $val['line_detail'] = json_decode(htmlspecialchars_decode($val['line_detail']));
            $val['create_at'] = shzDate($val['create_at']);
            $sellerInfo = $sellerLogic->getInfoById($val['seller_id']);
            if(empty($sellerInfo)){
                $val['driver'] = null;  // 不要返回 []  android端会解析失败。
                continue;
            }
            $val['driver'] = [
                'id' => $sellerInfo['seller_id'],
                'avatar' => $sellerInfo['head_pic'],
                'nickname' => $sellerInfo['nickname'],
                'is_driver' => $sellerInfo['is_driver'],
                'drv_code' => $sellerInfo['drv_code'],
                'plat_start' => $sellerInfo['plat_start'],
                'country_name' => $sellerInfo['country_name'],
                'province_name' => $sellerInfo['province_name'],
                'city_name' => $sellerInfo['city_name'],
                'district_name' => $sellerInfo['district_name'],
            ];
        }
        return $list;
    }
}