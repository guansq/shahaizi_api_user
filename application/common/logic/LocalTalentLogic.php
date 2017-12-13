<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:30
 */

namespace app\common\logic;

use think\Page;

class LocalTalentLogic extends BaseLogic{

    protected $table = 'ruit_article_local_talent';

    //1:用户2:司导3:房东4:店主


    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe:
     * @param $city
     */
    public static function getListAtHome($city){

    }

    /*
     * 得到当地达人列表
     */
    public function get_local_list($city){
        $count = M('article_local_talent')
            ->alias('l')
            ->join('ruit_seller s','l.seller_id = s.seller_id','LEFT')
            ->join('ruit_users u','l.user_id = u.user_id','LEFT')
            ->where("l.is_admin = 1 OR s.enabled = 1 OR u.is_lock = 0")
            ->where(['is_del'=>0])
            ->count();
        $Page = new Page($count, 10);
        if(empty($city)){
            $local_list = M('article_local_talent')
                ->field('l.*')
                ->alias('l')
                ->join('ruit_seller s','l.seller_id = s.seller_id','LEFT')
                ->join('ruit_users u','l.user_id = u.user_id','LEFT')
                ->where("l.is_admin = 1 OR s.enabled = 1 OR u.is_lock = 0")
                ->where(['is_del'=>0])
                ->limit(10)
                ->order('good_num desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();//当地达人
            //echo $localList;die;
        }else{
            $local_list = M('article_local_talent')
                ->field('l.*')
                ->alias('l')
                ->join('ruit_seller s','l.seller_id = s.seller_id','LEFT')
                ->join('ruit_users u','l.user_id = u.user_id','LEFT')
                ->where(['l.city'=>['like',"%{$city}%"],'is_del'=>0])
                ->where("l.is_admin = 1 OR s.enabled = 1 OR u.is_lock = 0")
                ->limit(10)
                ->order('good_num desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();//当地达人
        }
        //$user_praise = new UserPraiseLogic();
        foreach($local_list as &$val){
            $val = $this->local_info($val);
        }
        $result = ['totalPages' => $Page->totalPages, 'list' => $local_list];
        $return = [
            'status' => 1,
            'msg' => '',
            'result' => $result,
        ];
        return $return;
    }

    /*
     * 得到当地达人详情
     */
    public function get_local_detail($where){
        $info = M('article_local_talent')->where($where)->find();
        if(empty($info)){
            $this->ajaxReturn(['status' => -1, 'msg' => '没有该记录']);
        }
        $info = $this->local_info($info);
        $return = [
            'status' => 1,
            'msg' => '',
            'result' => $info,
        ];
        return $return;
    }

    /*
     * 当地达人总体
     */
    public function local_info($info){
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
            $info['type_info'] = substr($str, 0, -1);
        }
        //1:用户2:司导3:房东4:店主
        if($info['lable'] == 1){
            $user_info = get_user_info($info['user_id'],0);
            $info['name'] = $user_info['nickname'];
        }else{
            $seller_info = get_drv_info($info['seller_id']);
            $info['name'] = $seller_info['nickname'];
        }
        $info['is_good'] = 0;
        $user_praise = new UserPraiseLogic();
        $info['praiseNum'] = $user_praise->countLocalTalent($info['id']);
        //$info['city'] = getCountryName($info['country_id']).'·'.getCityName($info['city_id']);
        //print_r(session('user')['user_id']);die;
        $user_id = session('user')['user_id'];
        if(!empty($user_id)){
            $info['is_good'] = $user_praise->isPraised($info['talent_id'],$user_id,UserPraiseLogic::TYPE_TALENT);
        }
        $info['good_num'] = $user_praise->countLocalTalent($info['talent_id']);
        $info['content'] = htmlspecialchars_decode($info['content']);
        return $info;
    }
}