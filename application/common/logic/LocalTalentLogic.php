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

        $where = [
            'is_del' => 0
        ];
        if(!empty($city)){
            $where['city'] = ['like', "%{$city}%"];
        }
        $field = [
            'talent_id' => 'id',
            'user_id' => 'user_id',
            'title' => 'title',
            'cover_img' => 'img',
            'summary' => 'summary',
            'name' => 'name',
            'country_id' => 'countryId',
            'city_id' => 'cityId',
            'city' => 'cityName',
            'lable' => 'lable',
            'good_num' => 'praiseNum',
        ];

        $dbRet = self::where($where)->order('sort,create_at DESC')->limit(3)->field($field)->select();
        if($dbRet){
            $user_praise = new UserPraiseLogic();
            //1:用户2:司导3:房东4:店主
            foreach($dbRet as &$v){
                $v['praiseNum'] = $user_praise->countLocalTalent($v['id']);
                $v['city'] = getCountryName($v['countryId']).'·'.getCityName($v['cityId']);
                //1:用户2:司导3:房东4:店主
                if($v['lable'] == 1){
                    $user_info = get_user_info($v['user_id'],0);
                    $v['name'] = $user_info['nickname'];
                }else{
                    $seller_info = get_drv_info($v['user_id']);
                    $v['name'] = $seller_info['nickname'];
                }
            }
        }
        return $dbRet;
    }

    /*
     * 得到当地达人列表
     */
    public function get_local_list($city){
        if(empty($city)){
            $where = [];
        }else{
            $where = ['city' => ['like', "%{$city}%"]];
        }
        $count = M('article_local_talent')->where($where)->count();
        $Page = new Page($count, 10);
        //echo $Page->totalPages;die;
        $local_list = M('article_local_talent')
            ->where($where)
            ->order('good_num desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $user_praise = new UserPraiseLogic();
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
                $val['type_info'] = substr($str, 0, -1);
            }
            //$val['city'] = getCountryName($val['country_id']).'·'.getCityName($val['city_id']);

            //1:用户2:司导3:房东4:店主
            if($val['lable'] == 1){
                $user_info = get_user_info($val['user_id'],0);
                $val['name'] = $user_info['nickname'];
            }else{
                $seller_info = get_drv_info($val['user_id']);
                $val['name'] = $seller_info['nickname'];
            }
            $val['good_num'] = $user_praise->countLocalTalent($val['talent_id']);

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
    public function get_local_detail($where,$user_id,$talent_id){
        $info = M('article_local_talent')->where($where)->find();
        if(empty($info)){
            $this->ajaxReturn(['status' => -1, 'msg' => '没有该记录']);
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
            $info['type_info'] = substr($str, 0, -1);
        }
        $info['is_good'] = 0;
        $user_praise = new UserPraiseLogic();
        if(!empty($user_id)){

            $info['is_good'] = $user_praise->isPraised($talent_id,$user_id,UserPraiseLogic::TYPE_TALENT);
        }
        $info['good_num'] = $user_praise->countLocalTalent($talent_id);
        $info['content'] = htmlspecialchars_decode($info['content']);
        $return = [
            'status' => 1,
            'msg' => '',
            'result' => $info,
        ];
        return $return;
    }
}