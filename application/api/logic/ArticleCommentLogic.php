<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/19
 * Time: 14:16
 */

namespace app\api\logic;

class ArticleCommentLogic extends BaseLogic{

    public function saveComment($data){
        $result = M('article_comment')->save($data);
        if(!$result){
            return ['status'=>-1,'msg'=>'评论失败'];
        }
        return ['status'=>1,'msg'=>'评论成功'];
    }
}