<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 16:12
 */
namespace app\api\controller;

use app\common\HotLogic;
class HotGuide extends Base{
    public $hotLogic;

    public function __construct(){
        $this->hotLogic = new HotLogic();
    }

    /*
     * @api {GET}   index.php?m=Api&c=HotGuide&a=getHotGuideList   得到热门动态列表
     * @apiName getHotGuideList
     * @apiGroup Index
     * @apiParam   token {String}   token.
     * @apiSuccessExample   Success-Response
     *      Http/1.1    200 OK
     * {
     *      "talent_id" :   "1",  //ID
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
        $this->hotLogic->get_hot_list();
    }
}