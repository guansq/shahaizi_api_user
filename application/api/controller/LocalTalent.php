<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 15:52
 */
class LocalTalent {

    /*
     * @api  {GET} /index.php?m=Api&c=LocalTalent&a=getIndexLocalTalent     得到首页当地达人列表
     * @apiName    LocalTalent
     * @apiGroup   Index
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
    public function getIndexLocalTalent(){

    }

    /**
     * @api {GET}   /index.php?m=Api&c=LocalTalent&a=getLocalTalentList     得到达人列表（未完成）
     * @apiName     getLocalTalentList
     * @apiGroup    Talent
     * @apiParam    token {String}  token.
     * @apiParam    [p] {String}    第几页，默认1
     * @apiSuccessExample   Success-Response
     *      Http/1.1    200 OK
     * {
     *      "status": 1,
     *      "msg": "获取成功",
     *      "result": [
     *          {
     *          "talent_id" :   "1",  //视屏ID
     *          "cover_img" :   "http://xxxx.jpg",  //视屏封面图
     *          "name"      :   "张三",  //发布人姓名
     *          "city" :   "东京",  //发布人所在城市
     *          "id_type" :   "",  //身份标签（有几个身份？）
     *          "good_num" :   "111",  //点赞数
     *          },
     *          {
     *          "talent_id" :   "1",  //视屏ID
     *          "cover_img" :   "http://xxxx.jpg",  //视屏封面图
     *          "name"      :   "张三",  //发布人姓名
     *          "city" :   "东京",  //发布人所在城市
     *          "id_type" :   "",  //身份标签（有几个身份？）
     *          "good_num" :   "111",  //点赞数
     *          }
     *      ]
     * }
     *
     */
    public function getLocalTalentList(){

    }

    /**
     * @api {GET}   /index.php?m=api&c=LocalTalent&a=getLocalTalentDetail    得到当地达人详情（未完成）
     * @apiName getLocalTalentDetail
     * @apiGroup    Talent
     * @apiParam    talent_id {String}  当地达人
     * @apiSuccessExample   Success-Response
     *      Http/1.1    200 OK
     * {
     *  "talent_id" :    “”，
     *  "drv_code" :    “”，//司导CODE
     *  "store_id" :    “”，//店主ID
     *  "user_id" :    “”，//房东
     *  "talent_id" :    “”，
     *  "talent_id" :    “”，
     *  "cover_img" :   "http://xxx.jpg",
     *  "video_url" :   "http://xxx.mp4",
     *  "name"      :   "张三",  //发布人姓名
     *  "id_type" :   "",  //身份标签（有几个身份？）
     *  "city"  :   "xxxxxx",//所在城市地址
     *  "good_num"  :   "111",//点赞数
     *  "desc"  :   "111"//简介
     * }
     */
    public function getLocalTalentDetail(){

    }

}