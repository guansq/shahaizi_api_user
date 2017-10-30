<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 16:17
 */
namespace app\common\logic;

use think\Model;
use think\Page;
use app\common\logic\CommentLogic;
class HotLogic extends BaseLogic{
    /*
     * 得到热门攻略列表
     */
    public function get_hot_list($city){
        $count = M('article_hot_guide')->count();
        $Page = new Page($count, 10);
        $where['is_hot'] = 1;
        $city && $where['city'] = ['like',"%$city%"];
        $hot_list = M('article_hot_guide')->where($where)->order('sort,update_at DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();//热门攻略
        $praiseLogic = new UserPraiseLogic();
        foreach($hot_list as $k => &$v){
            $v['good_num'] = $praiseLogic->countPraiseOfGuide($v['guide_id']);
        }
        $result = [
            'totalPages' => $Page->totalPages,
            'list' => $hot_list
        ];
        if(empty($hot_list)){
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
    /*
     * 得到攻略列表
     */
    public function get_list($country_name){
        $where = [];
        if($country_name){
            $where['city'] = ['like',"%$country_name%"];
        }
        $count = M('article_hot_guide')->where($where)->count();
        $Page = new Page($count, 10);
        $hot_list = M('article_hot_guide')->where($where)->order('sort,update_at DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();//热门攻略
        $result = [
            'totalPages' => $Page->totalPages,
            'list' => $hot_list
        ];
        $praiseLogic = new UserPraiseLogic();
        foreach($hot_list as $k => &$v){
            $v['good_num'] = $praiseLogic->countPraiseOfGuide($v['guide_id']);
        }
        if(empty($hot_list)){
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
    /*
     * 得到热门攻略详情
     */
    public function get_hot_detail($guide_id){
        $guideLogic =  new GuideLogic();
        $info = $guideLogic->find($guide_id);
        if(empty($info)){
            return ['status'=>-1,'msg'=>'没有该记录'];
        }
        $praiseLogic = new UserPraiseLogic();
        $info['good_num'] = $praiseLogic->countPraiseOfGuide($info['guide_id']);
        $commentLogic = new CommentLogic();
        $commentList = $commentLogic->getArticleComment($guide_id,1);
        $result = [
            'info' => $info,
            'comment' => $commentList['list'],
        ];
        $info->read_num++;
        $info->save();
        return ['status'=>1,'msg'=>'成功','result'=>$result];
    }


}