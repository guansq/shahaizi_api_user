<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 16:18
 */
class NewAction{

    /**
     * @api {GET}   index.php?m=Api&c=NewAction&a=getIndexNewAction     得到首页最新消息
     * @apiName getIndexNewAction
     * @apiGroup    Index
     * @apiParam   token {String}   token.
     * @apiSuccessExample   Success-Response
     *      Http/1.1    200 OK
     * {
     *      "talent_id" :   "1",  //视屏ID
     *      "cover_img" :   "http://xxxx.jpg",  //视屏封面图
     *      "title" :   "文章标题",  //文章标题
     *      "name"      :   "张三",  //发布人姓名
     *      "good_num" :   "111",  //点赞数
     *
     * }
     */
    public function getIndexNewAction(){

    }
}