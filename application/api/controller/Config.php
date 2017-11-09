<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 9:21
 */

namespace app\api\controller;

use app\common\logic\ConfigLogic;
use app\common\logic\SuggestionFeedbackLogic;
use app\common\logic\SuggestionFeedbackTypeLogic;
use think\Request;

class Config extends Base{

    public $configLogic;

    function __construct(){
        $this->configLogic = new ConfigLogic();
    }

    /**
     * @api         {GET}   index.php?m=Api&c=Config&a=getCountryNumber    得到国家区号done  管少秋
     * @apiName     getCountryNumber
     * @apiGroup    Config
     *
     */
    public function getCountryNumber(){
        $result = $this->configLogic->get_country_number();
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' => $result]);
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=Config&a=getAllConfig    得到所有配置 wxx
     * @apiName     getAllConfig
     * @apiGroup    Config
     *
     */
    public function getAllConfig(){

    }

    /**
     * @api         {GET}   /index.php?m=Api&c=Config&a=aboutUs    关于我们 h5页面 wxx
     * @apiName     aboutUs
     * @apiGroup    Config
     *
     */
    public function aboutUs(){

    }

    /**
     * @api         {GET}   /index.php?m=Api&c=Config&a=helpCenter    帮助中心 h5页面 wxx
     * @apiName     helpCenter
     * @apiGroup    Config
     *
     */
    public function helpCenter(){

    }


    /**
     * @api {GET}  /index.php?m=Api&c=Config&a=articleInfo&type=0     单次接送以及私人定制的 退订政策|费用说明done
     * @apiName     articleInfo
     * @apiGroup    Config
     * @apiParam {string} type  单次接送0 私人定制1
     * @apiSuccessExample {json} SUCCESS
    {
    "status": 1,
    "msg": "成功！",
    "result": {
    "policy": {
    "title": "单次接送严格退订政策",
    "content": "<p><img src=\"http://img.shahaizi.cn/2e1cb201711090900555650.png\" title=\"\" alt=\"\"/></p>"
    },
    "explain": {
    "title": "单次接送费用说明",
    "content": "<p>这里是单次接送费用说明</p>"
    }
    }
    }
     */
    public function articleInfo ()
    {
        //40(私人定制退订政策)-45(私人定制费用说明)
        //39(单次接送退订政策)-46(单次接送费用说明)
        $type = I("type",0);
        $info = [
            0 =>[
                'policy' => 39,
                'explain' => 46,
            ],
            1 =>[
                'policy' => 40,
                'explain' => 45,
            ]
        ];
        $arrInfo = [];
        foreach($info[$type] as $key=>$val){
            $article = M("article")->field('title,content') -> where("article_id = $val") -> find();
            if(!empty($article)){
                $article["content"] = htmlspecialchars_decode($article["content"]);
            }
            $arrInfo[$key] = $article;
        }
        $this->ajaxReturn(resultArray(1,"成功！",$arrInfo));
    }

    /**
     * @api         {GET}   /api/config/feedBackType    获取意见反馈类型 ok wxx
     * @apiName     feedBackType
     * @apiGroup    Config
     * @apiSuccess  {Array} list
     * @apiSuccess  {Number} list.id    id
     * @apiSuccess  {String} list.name  name
     *
     */
    public function feedBackType(Request $request){
        if(!$request->isGet()){
            return $this->returnJson(4000);
        }
        $sFeedbackTypeLogic = new SuggestionFeedbackTypeLogic();
        return $this->returnJson($sFeedbackTypeLogic->getList());
    }

    /**
     * @api         {POST}   /api/config/feedBack    提交意见反馈 ok wxx
     * @apiName     feedBack
     * @apiGroup    Config
     * @apiParam  {String} [token]    token
     * @apiParam  {Number} typeId       id
     * @apiParam  {String} [imgs]       图片多张用‘|’分割
     * @apiParam  {String} content      内容
     */
    public function feedBack(Request $request){
        if(!$request->isPost()){
            return $this->returnJson(4000);
        }
        $params = $this->getReqParams(['typeId', 'imgs', 'content']);
        //验证规则
        $rule = [
            'typeId' => 'require',
            'content' => 'require',
        ];
        $this->validateParams($params,$rule);
        $this->checkToken();
        $sFeedbackLogic = new SuggestionFeedbackLogic();
        $this->returnJson($sFeedbackLogic->addFeedback($params, $this->user));
    }

    /**
     * @api     {GET}       /api/Config/vipTelephone    Vip电话
     * @apiName     vipTelephone
     * @apiGroup    Config
     * @apiSuccessExample {json}    Success-Response:
     *           Http/1.1   200 OK
    {
    "status": 1,
    "msg": "成功",
    "result": {
    "id": 2,
    "telephone": "1201",
    "content": "120救援电话",
    "created_at": 1506395417,
    "order_desc": 2,
    "is_show": 1
    }
    }
     */
    public function vipTelephone(){
        $result = $this->configLogic->getServiceTel();
        $this->ajaxReturn($result);
    }


}