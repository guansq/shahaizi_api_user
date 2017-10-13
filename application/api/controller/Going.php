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