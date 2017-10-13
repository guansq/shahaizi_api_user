<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 18:35
 */

namespace app\api\controller;

use app\common\logic\AdLogic;
use app\common\logic\PackLineLogic;

class PackLine extends Base{


    /**
     * @api         {GET}   /index.php?m=Api&c=PackLine&a=home  包车定制首页  done    管少秋
     * @apiName     home
     * @apiGroup    PackLine
     * @apiParam    {String}    city    城市
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     * {
     * "status": 1,
     * "msg": "成功",
     * "result": {
     * "index": [
     * {
     * "id": 1,
     * "name": "接机",
     * "sort": 1
     * },
     * {
     * "id": 2,
     * "name": "送机",
     * "sort": 2
     * },
     * {
     * "id": 3,
     * "name": "单次接送",
     * "sort": 3
     * },
     * {
     * "id": 4,
     * "name": "快速预订",
     * "sort": 4
     * },
     * {
     * "id": 5,
     * "name": "私人定制",
     * "sort": 5
     * },
     * {
     * "id": 6,
     * "name": "按天包车游",
     * "sort": 6
     * }
     * ],
     * "banner": [
     * {
     * "ad_link": "http://dev.tpshop.cn/index.php/Home/Topic/detail/topic_id/1",
     * "ad_name": "自定义广告名称",
     * "ad_code": "/public/upload/ad/2016/09-19/57dfb0fbf3660.jpg"
     * },
     * {
     * "ad_link": "javascript:void();",
     * "ad_name": "自定义广告名称",
     * "ad_code": "/public/upload/ad/2016/09-19/57dfb118f00cd.jpg"
     * },
     * {
     * "ad_link": "javascript:void();",
     * "ad_name": "自定义广告名称",
     * "ad_code": "/public/upload/ad/2016/09-19/57dfb1767a5bb.jpg"
     * },
     * {
     * "ad_link": "www.baidu.com",
     * "ad_name": "sec",
     * "ad_code": "/public/upload/ad/2017/09-06/25123a234d51076968680e09c9d27e8e.jpg"
     * }
     * ],
     * "line": [],
     * "driver": [
     * {
     * "seller_id": 17,
     * "head_pic": null,
     * "seller_name": "少秋",
     * "drv_code": "20170908-1",
     * "province": 0,
     * "city": 0,
     * "star": 4,
     * "plat_start": 4,
     * "line": null
     * }
     * ]
     * }
     * }
     */
    public function home(){
        $city = input('city');
        $city = '';
        $index = M('pack_index')->where('type','pack_index')->order('sort asc')->select();
        //获取轮播图
        $banner = M('ad')
            ->where('pid', AdLogic::AD_POSITION_CAR)
            ->field(array('ad_name', 'ad_code'))
            ->cache(true, TPSHOP_CACHE_TIME)
            ->select();
        //精选路线
        $packLogic = new PackLineLogic();
        $line = $packLogic->getCommPackLine($city);
        foreach($line as &$val){
            unset($val['line_detail']);
        }
        //当地司导
        $driver = $packLogic->get_local_drv();
        $home = [
            'index' => $index,
            'banner' => $banner,
            'line' => $line,
            'driver' => $driver
        ];
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' => $home]);
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=PackLine&a=getPackLine     查询路线 ok will
     * @apiName     getPackLine
     * @apiGroup    PackLine
     * @apiParam    {String}    [city]    根据城市筛选
     * @apiParam    {String}    [time]    根据时间筛选   2017-9-14
     * @apiParam    {Number}    [line_buy_num]    根据时间筛选
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     *{
     * "line_id" : "11",//线路ID
     * "seller_id" : "11",//商家ID
     * "cover_img" : "http://xxx.jpg",//线路风景
     * "line_title" : "线路标题",//线路标题
     * "line_sum" : "",//游玩次数
     * "line_grade" : "",//线路评分
     * "line_level" : "",//线路等级
     *}
     */
    public function getPackLine(){

        $city = I('city');
        $line_buy_num = I('line_buy_num');
        $time = empty(I('time')) ? '' : strtotime(I('time'));
        $where = ['is_state' => 1];
        !empty($city) && $where['city'] = ['like', "%$city%"];
        !empty($time) && $where['update_at'] = ['between', [$time, $time + 86400]];//更新时间
        !empty($line_buy_num) && $where['line_buy_num'] = ['egt', $line_buy_num];

        $packLogic = new PackLineLogic();
        $line = $packLogic->get_all_pack_line($where);
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' => $line]);
    }


    /**
     * @api         {GET}   /index.php?m=Api&c=PackLine&a=getQualityLine     得到精品路线 done 管少秋
     * @apiName     getQualityLine
     * @apiGroup    PackLine
     * @apiParam    {String}    [city]    根据城市筛选
     * @apiParam    {String}    [time]    根据时间筛选   2017-9-14
     * @apiParam    {Number}    [line_buy_num]    根据时间筛选
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
     *{
     * "line_id" : "11",//线路ID
     * "seller_id" : "11",//商家ID
     * "cover_img" : "http://xxx.jpg",//线路风景
     * "line_title" : "线路标题",//线路标题
     * "line_sum" : "",//游玩次数
     * "line_grade" : "",//线路评分
     * "line_level" : "",//线路等级
     *}
     */
    public function getQualityLine(){
        $where = [];
        // $city = I('city');
        $time = empty(I('time')) ? '' : strtotime(I('time'));
        $line_buy_num = I('line_buy_num');
        !empty($city) && $where['city'] = ['LIKE', "%{$city}%"];
        !empty($time) && $where['update_at'] = ['between', [$time, $time + 86400]];//更新时间
        !empty($line_buy_num) && $where['line_buy_num'] = ['egt', $line_buy_num];
        //精选路线
        $packLogic = new PackLineLogic();
        $line = $packLogic->getPackLinePageByWhere($where);
        $this->returnJson(2000, '', $line);
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=PackLine&a=detail    路线详情 ok will
     * @apiName     detail
     * @apiGroup    PackLine
     * @apiParam    {Number}  id   id.
     * @apiParam    {String}  [token]    token.
     * @apiSuccess  {Number}  id        id.
     * @apiSuccess  {Number}  isCollect     是否收藏.
     * @apiSuccess  {Number}  isPraise      是否点赞.
     * @apiSuccess  {Number}  isAdmin       是否是平台路线.
     * @apiSuccess  {Number}  playDay       游玩天数.
     */
    public function detail(){
        $pkgLineLogic = new PackLineLogic();
        $id = input('id');

        $pkgLine = $pkgLineLogic->find($id);
        if(empty($pkgLine)){
            return $this->returnJson(4004);
        }
        $this->checkToken();
        $pkgLine = $pkgLineLogic->getDetailByModel($pkgLine,$this->user);
        $ret = [
            'id'=>$pkgLine['line_id'],
            'title'=>$pkgLine['line_title'],
            'isAdmin'=>$pkgLine['is_admin'],
            'isCollect'=>$pkgLine['is_collect'],
            'isPraise'=>$pkgLine['is_praise'],
            'playDay'=>intval($pkgLine['play_day']),
        ];
        return $this->returnJson(2000,'',$ret);


    }


}