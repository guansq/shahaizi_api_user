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
        $act_id = M('article_comment')->getLastInsID();
        if(!$result){
            return ['status'=>-1,'msg'=>'评论失败'];
        }

        $userLogic = new UserLogic();
        $user = $userLogic->find($data['publish_id']);
        $commentUser = $userLogic->find($data['user_id']);
        $title = '您有一条新动态被'.$commentUser['nickname'].'评论了';
        $content = '您有一条新动态被'.$commentUser['nickname'].'评论了';
        send_msg_by_article($title,$content,$user['user_id'],$data['article_id'],1);//动态为1
        //pushMessage('动态评论', '您有一条新动态被'.$commentUser['nickname'].'评论了', $user['push_id'], $user['user_id'], 0);
        return ['status'=>1,'msg'=>'评论成功'];
    }
}