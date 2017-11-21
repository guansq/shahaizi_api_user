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
        $userLogic = new UserLogic();
        $user = $userLogic->find($data['publish_id']);
        pushMessage('动态评论', '您有一条新动态被评论', $user['push_id'], $user['user_id'], 0);
        return ['status'=>1,'msg'=>'评论成功'];
    }
}