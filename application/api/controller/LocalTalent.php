<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 15:52
 */
namespace app\api\controller;
use app\common\logic\LocalTalentLogic;
use app\common\logic\UserPraiseLogic;
use think\Request;

class LocalTalent extends Base{

    public $localLogic;

    public function __construct(){
        parent::__construct();
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
     * @apiParam    {String}    token  token.
     * @apiParam    {String}    [p]    第几页，默认1
     * @apiParam    {String}    city    城市名称
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
        $city = I('city','');
        $list = $this->localLogic->get_local_list($city);
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
     *  "good_num"  :   11//点赞数
     * }
     */
    public function getLocalTalentDetail(){
        $talent_id = I('talent_id');
        if(empty($talent_id)){
            $this->ajaxReturn(['status'=>-1,'msg'=>'当地达人ID不能为空']);
        }
        $where = ['talent_id'=>$talent_id];
        $user_id = $this->user_id;
        $result = $this->localLogic->get_local_detail($where,$user_id,$talent_id);
        $this->ajaxReturn($result);
    }


    /**
     * @api {POST}   /api/LocalTalent/praise    当地达人点赞 ok wxx
     * @apiName praise
     * @apiGroup    Talent
     * @apiParam  {String} token    token.
     * @apiParam  {Number}  id   当地达人id
     */
    public function praise(){
        $id = I('id');
        if(empty($id)){
            return $this->returnJson(4002, '缺少参数id');
        }
        $praiseLogic = new UserPraiseLogic();
        $talentLogic = new LocalTalentLogic();
        if($talentLogic->where('talent_id', $id)->count() == 0){
            return $this->returnJson(4002, '你要点赞的达人已经不存在。');
        }
        return $this->returnJson($praiseLogic->addPraise($this->user_id, UserPraiseLogic::TYPE_TALENT, $id));
    }



}