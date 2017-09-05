<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/7
 * Time: 9:57
 */

namespace app\api\controller;

class Recommend extends BaseController {
    /**
     * @api {GET}   recommend/showMyRecommInfo      显示我的推荐信息
     * @apiName     showMyRecommInfo
     * @apiGroup    Recommend
     * @apiHeader   {String}    authorization-token         token.
     * @apiSuccess  {String}    code            推荐码
     *
     */
    public function showMyRecommInfo() {
        $ret = model('sp_base_info', 'logic')->getRecommCode($this->loginUser);
        returnJson($ret);
    }


    /**
     * @api {GET}   recommend/showMyRecommList      显示我的推荐列表
     * @apiName     showMyRecommList
     * @apiGroup    Recommend
     * @apiHeader   {String}    authorization-token         token.
     * @apiParam {Number} [page=1]                       页码.
     * @apiParam {Number} [pageSize=20]                  每页数据量.
     * @apiSuccess  {Array}     list                        列表
     * @apiSuccess  {String}    list.avatar                 被推荐人头像
     * @apiSuccess  {String}    list.name                   被推荐人名称
     * @apiSuccess  {String}    list.bonus                 奖励金
     * @apiSuccess {Number} page                         页码.
     * @apiSuccess {Number} pageSize                     每页数据量.
     * @apiSuccess {Number} dataTotal                    数据总数.
     * @apiSuccess {Number} pageTotal                    总页码数.
     */
    public function showMyRecommList() {
        $pageParam = $this->getPagingParams();
        $ret = model('sp_base_info', 'logic')->getRecommIDs($this->loginUser, $pageParam);
        if (empty($ret)) {
            returnJson(4004, '暂时没有推荐列表');
        }
        $list = [];
        foreach ($ret['list'] as $k => $v) {
            $bonus = model('sp_base_info', 'logic')->getRecommBonus(['type' => 0, 'status' => 0, 'invite_id' => $v['id'], 'share_id' => $this->loginUser['id']]);
            $v['bonus'] = empty($bonus) ? 0 : $bonus;
            $v['bonus'] = wztxMoney($v['bonus']);
            $list[$k]['avatar'] = $v['avatar'];
            $list[$k]['name'] = $v['real_name'];
            $list[$k]['phone'] = $v['phone'];
            $list[$k]['bonus'] = $v['bonus'];
        }
        $ret['list'] = $list;
        returnJson(2000, '成功', $ret);
    }
}