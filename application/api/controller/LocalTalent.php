<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 15:52
 */
namespace app\api\controller;
use app\common\logic\LocalTalentLogic;

class LocalTalent extends Base{

    public $localLogic;

    public function __construct(){
        $this->localLogic = new LocalTalentLogic();
    }

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
     * @api {GET}   /index.php?m=Api&c=LocalTalent&a=getLocalTalentList     得到达人列表  传入p 为 n代表第n页 done  管少秋
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
     *          "talent_id" :   "1",  //当地达人ID
     *          "title"     :   "",//标题
     *          "cover_img" :   "http://xxxx.jpg",  //视屏封面图
     *          "name"      :   "张三",  //发布人姓名
     *          "city" :   "东京",  //发布人所在城市
     *          "type_info" :   "",  //身份标签（有几个身份？）
     *          "good_num" :   "111",  //点赞数
     *          },
     *          {
     *          "talent_id" :   "1",  //视屏ID
     *          "cover_img" :   "http://xxxx.jpg",  //视屏封面图
     *          "name"      :   "张三",  //发布人姓名
     *          "city" :   "东京",  //发布人所在城市
     *          "type_info" :   "",  //身份标签（有几个身份？）
     *          "good_num" :   "111",  //点赞数
     *          }
     *      ]
     * }
     *
     */
    public function getLocalTalentList(){
        $list = $this->localLogic->get_local_list();
        $this->ajaxReturn($list);
    }

    /**
     * @api {GET}   /index.php?m=api&c=LocalTalent&a=getLocalTalentDetail    得到当地达人详情done  管少秋
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
        $talent_id = I('talent_id');
        if(empty($talent_id)){
            $this->ajaxReturn(['status'=>-1,'msg'=>'当地达人ID不能为空']);
        }
        $where = ['talent_id'=>$talent_id];
        $result = $this->localLogic->get_local_detail($where);
        $this->ajaxReturn($result);
    }



}