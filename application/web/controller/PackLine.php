<?php

namespace app\web\controller;

use app\common\logic\OrderCommentLogic;
use app\common\logic\PackCommentLogic;
use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class PackLine extends WebBase{


    public function detail(Request $request){
        $id = input('id');
        $lineLogic = new PackLineLogic();
        $sellerLogic = new SellerLogic();
        $orderCommentLogic = new OrderCommentLogic();
        $line = $lineLogic->find($id);
        if(empty($line)){
            return $this->error('你要查看的路线已经不存在');
        }


        $line = $line->toArray();
        $line_detail=$line['line_detail'];
        $de_json=html_json($line_detail);
        $line['line_detail'] =object_to_array($de_json);
        $where =[
            'line_id'=>$id,
            'deleted'=>0
        ];
        $line['score'] = intval($orderCommentLogic->where($where)->avg('pack_order_score'));
        $line['line_price_fmt'] = moneyFormat($line['line_price']);
        $driverInfo = $sellerLogic->getInfoById($line['seller_id']);
        //dd($line['line_detail'][0]);  //todo 添加数据校验
        $this->assign('line', $line);
        $this->assign('driver', $driverInfo);

        $commentLogic = new OrderCommentLogic();
        $list = $commentLogic->getListByWere($where);
        $comments =[
            'total'=>count($list),
            'list'=>$list,
        ];
        $this->assign('comments',$comments);
        return $this->fetch();
    }


}