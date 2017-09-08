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
class HotLogic extends Model{
    /*
     * 得到热门攻略列表
     */
    public function get_hot_list(){
        $count = M('article_hot_guide')->count();
        $Page = new Page($count, 10);
        $hot_list = M('article_hot_guide')->order('good_num desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
     * 得到热门攻略详情
     */
    public function get_hot_detail($guide_id){
        $info = M('article_hot_guide')->where(['guide_id'=>$guide_id])->find();
        if(empty($info)){
            $this->ajaxReturn(['status'=>-1,'msg'=>'没有该记录']);
        }
        $commentLogic = new CommentLogic();
        $commentList = $commentLogic->getArticleComment($guide_id,1);
        $result = [
            'info' => $info,
            'comment' => $commentList['list'],
        ];
        return ['status'=>1,'msg'=>'成功','result'=>$result];
    }


}