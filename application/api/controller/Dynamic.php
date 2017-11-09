<?php
/**
 * 动态相关控制器
 */

namespace app\api\controller;

use app\api\logic\DynamicLogic;
use think\Request;

class Dynamic extends Base{

    public function index(Request $request){
        if($request->isGet()){
            $id = input('id');
            if(empty($id)){
                return $this->getPage();
            }
            return $this->getDetail($id);
        }
        if($request->isDelete()){
            return $this->delete();
        }
        return $this->returnJson();
    }


    /**
     * @api             {GET}   /Api/Dynamic   02.动态列表 ok wxx
     * @apiDescription  获取全部动态 时间倒序排列
     * @apiName         getPage
     * @apiGroup        Dynamic
     * @apiParam  {number} [p=1]        页码.
     * @apiParam  {number} [pageSize=20]   每页数据量.
     * @apiParam  {String='time,praise'} [sort_field=time]  排序字段.
     * @apiParam  {String='asc,desc'} [sort_type=desc]      排序方式.
     *
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list         列表.
     * @apiSuccess {number} list.id     id.
     * @apiSuccess {string} list.img    封面图片.
     * @apiSuccess {string} list.title  标题.
     * @apiSuccess {string} list.subTitle 副标题.
     * @apiSuccess {number} list.timeStamp  发布时间戳.
     * @apiSuccess {string} list.timeFmt    格式化发布时间.
     * @apiSuccess {number} list.praiseNum  点赞量.
     * @apiSuccess {number} list.readNum  阅读量.
     * @apiSuccess {Object} list.owner  发布人信息.
     *
     * @apiSuccessExample {json} SUCCESS
     *  {
     *      "status": 1,
     *      "msg": "SUCCESS",
     *      "result": {
     *          "p": 1,
     *          "totalPages": 4,
     *          "list": [
     *              {
     *                  "id": 13,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729853,
     *                  "timeFmt": "2017.09.18"
     *              },
     *              {
     *                  "id": 12,
     *                  "img": "http://img002.21cnimg.com/photos/album/20150702/m600/2D79154370E073A2BA3CD4D07868861D.jpeg",
     *                  "title": "小黄人大闹天空",
     *                  "subTitle": null,
     *                  "readNum": 0,
     *                  "praiseNum": 0,
     *                  "timeStamp": 1505729850,
     *                  "timeFmt": "2017.09.18"
     *              }
     *          ]
     *      }
     *  }
     *
     */
    private function getPage(){
        $reqParams = $this->getReqParams(['sort_field' => 'time', 'sort_type' => 'desc']);
        $rule = [
            'sort_field' => 'require|in:time,praise',
            'sort_type' => 'require|in:asc,desc',
        ];
        $this->validateParams($reqParams, $rule);
        $sortField = 'create_at';
        if($reqParams['sort_field'] == 'praise'){
            $sortField = 'good_num';
        }

        $dynamicLogic = new DynamicLogic();
        $this->returnJson($dynamicLogic->getDynamicPage($sortField, $reqParams['sort_type']));
    }


}