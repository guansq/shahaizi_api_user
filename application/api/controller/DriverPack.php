<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 19:40
 */
class DriverPack{

    /**
     * @api {GET}   /index.php?m=Api&c=PackLine&a=getLocalLine     得到当地司导
     * @apiName     getLocalLine
     * @apiGroup    DriverPack
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     *{
     * "drv_id"   : "11",//司导ID
     * "head_pic" : "http://xxx.jpg",//司导图片
     * "user_name" : "司导姓名",
     * "comment_level" : "1",//评价等级
     * "local" : "",//位置
     * "level" : "",//等级
     * "grade" : "",//评分
     *}
     */
    public function getLocalLine(){

    }

    /**
     * @api {GET}   /index.php?m=Api&c=DriverPack&a=getDriverDetail 司导详情
     * @apiName     getDriverDetail
     * @apiGroup    DriverPack
     * @apiParam    drv_id  {String}    司导ID
     * @apiSuccessExample   {json}  Success-Response
     * Http/1.1 200 OK
     *{
     *  "head_pic" : "http://xxx.jpg",//司导头像
     *  "putonghua" : "",//普通话
     *  "language" : "",//精通外语
     *  "putonghua" : "",//东京
     *  "putonghua" : "",//职业
     * }
     */
    public function getDriverDetail(){

    }
}