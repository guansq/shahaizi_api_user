<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\api\logic;


/**
 *  消息
 * Class CatsLogic
 * @package common\Logic
 */
class MessageLogic extends BaseLogic{

    protected $table = 'ruit_article';

    //  状态 1:显示 0:隐藏
    const STATUS_HIDE = 0;
    const STATUS_SHOW = 1;

    public function getSystemMsgPage(){
        $count = $this->where('is_open', self::STATUS_SHOW)->count();
        // todo
        $count = 0;
        if(empty($count)){
            return resultArray(4004, '');
        }

        $ret = [
            // todo
        ];
        return resultArray(2000, '', $ret);
    }

}