<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 16:12
 */
namespace app\api\controller;

use app\common\logic\GuideLogic;
use app\common\logic\HotLogic;

class HotGuide extends Base{
    public $hotLogic;

    public function __construct(){
        parent::__construct();
        $this->hotLogic = new HotLogic();
    }

    /**
     * @api {GET}   /index.php?m=Api&c=HotGuide&a=getHotGuideList   得到热门攻略列表done  管少秋
     * @apiName getHotGuideList
     * @apiParam    {String}    city    城市名
     * @apiParam    {String}    p   页数
     * @apiGroup HotGuide
     * @apiSuccessExample   Success-Response
     *      Http/1.1    200 OK
     * {
     *      "guide_id" :   "1",  //ID
     *      "cover_img" :   "http://xxxx.jpg",  //攻略图片
     *      "title"     :   "我得标题很长很长",  //攻略标题
     *      "summary"   :   "这是我的摘要",  //发布人摘要
     *      "name"      :   "张三",  //发布人姓名
     *      "city" :   "东京",  //发布人所在城市
     *      "type_info" :   "",  //身份标签（有几个身份？）
     *      "good_num" :   "111",  //点赞数
     *
     * }
     */
    public function getHotGuideList(){
        $city = I('city');
//        print_r($city);die;
        $return = $this->hotLogic->get_hot_list($city);
        $this->ajaxReturn($return);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=HotGuide&a=getGuideList   得到攻略列表done  管少秋
     * @apiName getGuideList
     * @apiParam    {String}    p   页数
     * @apiParam    {String}    country_name   国家名称
     * @apiGroup HotGuide
     * @apiSuccessExample   Success-Response
     *      Http/1.1    200 OK
     * {
     *      "guide_id" :   "1",  //ID
     *      "cover_img" :   "http://xxxx.jpg",  //攻略图片
     *      "title"     :   "我得标题很长很长",  //攻略标题
     *      "summary"   :   "这是我的摘要",  //发布人摘要
     *      "name"      :   "张三",  //发布人姓名
     *      "city" :   "东京",  //发布人所在城市
     *      "type_info" :   "",  //身份标签（有几个身份？）
     *      "good_num" :   "111",  //点赞数
     *
     * }
     */
    public function getGuideList(){
        $country_name = I('country_name');
        $return = $this->hotLogic->get_list($country_name);
        $this->ajaxReturn($return);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=HotGuide&a=getHotGuideDetail      得到热门攻略详情done   管少秋
     * @apiName     getHotGuideDetail
     * @apiGroup    HotGuide
     * @apiParam    {String}    guide_id        热门攻略详情ID
     * @apiSuccessExample   Success-Response
     * Http/1.1 200 OK
    {
    "status": 1,
    "msg": "成功",
    "result": {
    "info": {
    "guide_id": 5,
    "isPraise": 0,是否点赞
    "isCollect": 0,是否收藏
    "title": "敲开斜角巷的石砖，探寻巫师们的魔法世界",
    "cover_img": "https://z0.muscache.com/im/pictures/a35b3599-8a40-4022-8337-6677d9b94f52.jpg?aki_policy=large",
    "summary": "英国魔法之旅",
    "user_id": 50,
    "user_name": "Ning",
    "city": "英国",
    "content": "这条约克大教堂不远处的巷子叫The Shambles，是的，就是传说中哈利波特里“斜角巷”的原型。我不是哈迷，但这条巷子当真有味道！这是英国最古老的街道，也是欧洲保存最完好的中世纪街道。虽然人潮汹涌，街道两旁都是各种纪念品商店，但整条街的风情还是显露无疑。古街路面上铺满鹅卵石，街道两边的房子向中间倾斜，房顶几乎相接，外墙乍看之下好像纸糊的一样弱不禁风，实际却已历经成百上千年的岁月。阳光洒过，巷子内留下一片神秘的淡紫蓝色投影，彷佛自成一界，而肉眼看不到的魔法世界就藏匿其中，巫师们的精彩故事正静悄悄上演。",
    "read_num": 331,
    "good_num": 46,
    "status": null,
    "create_at": 1495296000,
    "update_at": 1499356800
    },
    "comment": [
    {
    "head_pic": null,
    "nickname": "18451847701",
    "add_time": 1504839306,
    "spec_key_name": "",
    "content": "这是我的评论",
    "impression": null,
    "comment_id": 1,
    "zan_num": 100,
    "is_anonymous": 0,
    "reply_num": null,
    "img": [
    "/public/upload/goods/2016/04-21/57187dbb16571.jpg",
    "/public/upload/goods/2016/04-21/57187dd92a26f.jpg",
    "/public/upload/goods/2016/04-21/57187dd8e18e8.jpg"
    ],
    "parent_id": [
    {
    "reply_id": 1,
    "comment_id": 1,
    "parent_id": 0,
    "content": "one",
    "user_name": "a",
    "to_name": "",
    "deleted": 0,
    "reply_time": 2017
    },
    {
    "reply_id": 2,
    "comment_id": 1,
    "parent_id": 1,
    "content": "two2",
    "user_name": "b",
    "to_name": "a",
    "deleted": 0,
    "reply_time": 2017
    }
    ]
    }
    ]
    }
    }
     *
     */
    public function getHotGuideDetail(){
        $guide_id = I('guide_id', 0);
        $guideLogic = new GuideLogic();
        $user_id = $this->user_id;
        $result = $guideLogic->get_hot_detail($guide_id,$user_id);
        $this->ajaxReturn($result);
    }

}