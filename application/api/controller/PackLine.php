<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 18:35
 */
class PackLine{

    /**
     * @api {GET}   /index.php?m=Api&c=PackLine&a=getQualityLine     得到精品路线（未完成）
     * @apiName     getQualityLine
     * @apiGroup    PackLine
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     *{
     * "line_id" : "11",//线路ID
     * "cover_img" : "http://xxx.jpg",//线路风景
     * "line_title" : "线路标题",//线路标题
     * "line_sum" : "",//游玩次数
     * "line_grade" : "",//线路评分
     * "line_level" : "",//线路等级
     *}
     */
    public function getQualityLine(){

    }


}