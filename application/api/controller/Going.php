<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/13
 * Time: 13:47
 */

namespace app\api\controller;
use app\common\logic\AdLogic;

use app\common\logic\GoingLogic;

class Going extends Base{

    /**
     * @api         {GET}   /index.php?m=Api&c=Going&a=home  出行首页  done    管少秋
     * @apiName     home
     * @apiGroup    Going
     * @apiParam    {String}    city    城市
     * @apiSuccessExample {json}    Success-Response
     *  Http/1.1    200 OK
    {
    "status": 1,
    "msg": "成功",
    "result": {
    "index": [
    {
    "id": 7,
    "name": "签证",
    "type": "going_index",
    "sort": 1
    },
    {
    "id": 8,
    "name": "民宿",
    "type": "going_index",
    "sort": 2
    },
    {
    "id": 9,
    "name": "包车",
    "type": "going_index",
    "sort": 3
    },
    {
    "id": 10,
    "name": "攻略",
    "type": "going_index",
    "sort": 4
    },
    {
    "id": 11,
    "name": "美食",
    "type": "going_index",
    "sort": 5
    }
    ],
    "banner": [
    {
    "ad_link": "",
    "ad_name": "自定义广告名称",
    "ad_code": "http://img.shahaizi.cn/43594201710131143077397.jpg"
    }
    ],
    "guideList": [
    {
    "guide_id": 2,
    "title": "日本·小樽",
    "cover_img": "http://img.shahaizi.cn/638c1201710101627158502.jpg",
    "summary": "小樽是日本北海道西南部港市，札幌的外港，大约在100 年前作为北海道的海上大门发展起来，异常繁荣，不少银行和企业纷纷来此发展，被人称为“北方的华尔街”。",
    "user_id": 0,
    "user_name": "平台",
    "content": "<p></p>",
    "read_num": 27,
    "status": 1,
    "publish_time": 1507624260,
    "country_id": 8,
    "city_id": 50000,
    "is_admin": 1,
    "is_hot": 1,
    "sort": null,
    "create_at": 1507624308,
    "update_at": 1507871095,
    "city": "日本·东京",
    "country": "日本",
    "praiseNum": 0,
    "owner": null
    },
    {
    "guide_id": 3,
    "title": "测试",
    "cover_img": "http://img.shahaizi.cn/025fc201710111057166046.jpg",
    "summary": "测试测试测试",
    "user_id": 0,
    "user_name": "平台",
    "content": "<p>测试测试测试</p>",
    "read_num": 9,
    "status": 1,
    "publish_time": 1508169600,
    "country_id": 7,
    "city_id": 1,
    "is_admin": 1,
    "is_hot": 1,
    "sort": null,
    "create_at": 1507690672,
    "update_at": 1507866896,
    "city": "中国·北京市",
    "country": "中国",
    "praiseNum": 0,
    "owner": null
    }
    ],
    "reliable_drv": [
    {
    "seller_id": 1,
    "head_pic": "http://img.shahaizi.cn/3ce28e0bb34ab33ff27eece52706adf8.jpeg",
    "seller_name": "",
    "drv_code": "20171010-1",
    "province": "江苏省",
    "city": "苏州市",
    "plat_start": "4",
    "star": 5,
    "line": "互相学习我"
    },
    {
    "seller_id": 2,
    "head_pic": "http://img.shahaizi.cn/b65993b08bef40e3b2bf7c6dce00fd64.jpeg",
    "seller_name": "",
    "drv_code": "20171012-2",
    "province": "江苏省",
    "city": "苏州市",
    "plat_start": "4",
    "star": 5,
    "line": ""
    }
    ]
    }
    }
     */
    public function home(){
        $city = I('city', '');
        $city = ''; // FIXME 目前去掉地区筛选

        $index = M('pack_index')->where('type','going_index')->order('sort asc')->select();

        //获取轮播图
        $banner = M('ad')->where('pid', AdLogic::AD_POSITION_GOOUT)->field(array(
            'ad_link',
            'ad_name',
            'ad_code'
        ))->select();
        $info = GoingLogic::getGoingInfo();
        //可靠司导
        //热门攻略
        $home = [
            'index' => $index,
            'banner' => $banner,
            'guideList' => $info['guideList'],
            'reliable_drv'=> $info['reliable_drv']
        ];
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' => $home]);
    }
}