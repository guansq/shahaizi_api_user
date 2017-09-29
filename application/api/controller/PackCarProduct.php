<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 18:35
 */

namespace app\api\controller;


use app\common\logic\PackCarProductLogic;
use think\Request;

class PackCarProduct extends Base{

    public function index(Request $request){

        if($request->isGet()){
            return $this->getList($request);
        }
        if($request->isDelete()){
        }

        return $this->returnJson();
    }
    /**
     *
     * @api             {GET}   /api/packCarProduct   02.包车产品列表 fixme wxx
     * @apiDescription  包车-按天包车-包车产品列表
     * @apiName         getList
     * @apiGroup        PackCarProduct
     * @apiParam  {Number} [p=1]        页码.
     * @apiParam  {Number} [pageSize=20]   每页数据量.
     *
     * @apiSuccess {Number} p          当前页码
     * @apiSuccess {Number} pageSize   列表每页显示行数
     * @apiSuccess {Number} totalRows  总行数
     * @apiSuccess {Number} totalPages 分页总页面数
     * @apiSuccess {Array}  list         列表.
     * @apiSuccess {Number} list.id             id.
     * @apiSuccess {Array}  list.imgs           图片.
     * @apiSuccess {String} list.title          标题.
     * @apiSuccess {Number} list.publishTime    发布时间戳.
     * @apiSuccess {String} list.publishTimeFmt 发布时格式化.
     * @apiSuccess {Number} list.price          单价.
     * @apiSuccess {String} list.priceFmt       单价格式化.
     *
     */
    private function getList(Request $request){
        $pcpLogic = new PackCarProductLogic();
        return $this->returnJson($pcpLogic->getPage());
    }

}