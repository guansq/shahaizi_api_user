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
 * Author: dyr
 * Date: 2016-08-09
 */

namespace app\common\logic;

use think\Model;

/**
 * 线路评论逻辑
 * Class CatsLogic
 * @package common\Logic
 */
class PackCommentLogic extends Model{
    protected $table ='ruit_pack_comment';

    public function getCommentBylineId($lineId){
        $sellerLogic = new SellerLogic();

        $total = $this->where('line_id',$lineId)->count();
        $list = $this->where('line_id',$lineId)->select();
        if($total == 0){
            return [
                'total'=>0,
                'list'=>[]
            ];
        }
        foreach($list as &$item){

            $seller = $sellerLogic->find($item['']);

            $ownerId =
            $item[]=[
                'ownerId'=>$ownerId,
                'ownerId'=>$ownerId,
            ];
        }

        $ret = [
            'total'=>$total,
            'list'=>$list
        ];
        return $ret;
    }

}