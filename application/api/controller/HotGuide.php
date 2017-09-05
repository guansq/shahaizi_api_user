<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 16:12
 */

class HotGuide{
    /**
     * @api {GET}   index.php?m=Api&c=HotGuide&a=getIndexHotGuide   得到首页热门动态
     * @apiName getIndexHotGuide
     * @apiGroup Index
     * @apiParam   token {String}   token.
     * @apiSuccessExample   Success-Response
     *      Http/1.1    200 OK
     * {
     *      "talent_id" :   "1",  //视屏ID
     *      "cover_img" :   "http://xxxx.jpg",  //视屏封面图
     *      "name"      :   "张三",  //发布人姓名
     *      "city" :   "东京",  //发布人所在城市
     *      "id_type" :   "",  //身份标签（有几个身份？）
     *      "good_num" :   "111",  //点赞数
     *
     * }
     */
    public function getIndexHotGuide(){

    }
}