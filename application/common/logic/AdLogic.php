<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

use think\Model;

class AdLogic extends BaseLogic{
    protected $table = 'ruit_ad';
    const  AD_POSITION_HOME     = 10;  //	用户端首页轮播
    const  AD_POSITION_CAR      = 11;  //	用户端包车定制内轮播
    const  AD_POSITION_HOUSE    = 12;  //	用户端民宿预约内轮播
    const  AD_POSITION_KEEPSAKE = 13;  //	用户端纪念商城内轮播
    const  AD_POSITION_GOOUT    = 14;  //	用户端出行轮播

}