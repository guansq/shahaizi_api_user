<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:30
 */
namespace app\common\logic;

use think\Model;
use think\Page;
use think\Db;
class LocalTalentLogic extends Model{

    /*
     * 得到当地达人列表
     */
    public function get_local_list(){

        $count = M('article_local_talent')->count();
        $Page = new Page($count, 10);
        //echo $Page->totalPages;die;
        $local_list = M('article_local_talent')->order('good_num desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach($local_list as &$val){
            $str = '';
            $type = getIDType($val['seller_id']);
            if(!empty($type['store_id'])){
                $str .= '店主-';
            }
            if(!empty($type['drv_id'])){
                $str .= '司导-';
            }
            if(!empty($type['home_id'])){
                $str .= '房东-';
            }
            if(!empty($str)){
                $val['type_info'] = substr($str,0,-1);
            }
        }
        $result = ['totalPages' => $Page->totalPages,'list'=>$local_list];
        $return = [
            'status'    =>1,
            'msg'       =>'',
            'result'    =>$result,
        ];
        return $return;
    }

    /*
     * 得到当地达人详情
     */
    public function get_local_detail($where){
        $info = M('article_local_talent')->where($where)->find();
        if(empty($info)){
            $this->ajaxReturn(['status'=>-1,'msg'=>'没有该记录']);
        }
        $str = '';
        $type = getIDType($info['seller_id']);
        if(!empty($type['store_id'])){
            $str .= '店主-';
        }
        if(!empty($type['drv_id'])){
            $str .= '司导-';
        }
        if(!empty($type['home_id'])){
            $str .= '房东-';
        }
        if(!empty($str)){
            $info['type_info'] = substr($str,0,-1);
        }
        $return = [
            'status'    =>1,
            'msg'       =>'',
            'result'    =>$info,
        ];
        return $return;
    }
}