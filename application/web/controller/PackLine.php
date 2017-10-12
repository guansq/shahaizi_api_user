<?php

namespace app\web\controller;

use app\common\logic\PackCommentLogic;
use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class PackLine extends WebBase{


    public function detail(Request $request){
        $id = input('id');
        $lineLogic = new PackLineLogic();
        $sellerLogic = new SellerLogic();
        $lineCommentLogic = new PackCommentLogic();
        $line = $lineLogic->find($id);
        if(empty($line)){
            return $this->error('你要查看的路线已经不存在');
        }
        $line = $line->toArray();
        $line_detail=$line['line_detail'];
        $de_json=html_json($line_detail);
        $line['line_detail'] =object_to_array($de_json);;
        $line['plat_start'] = $lineCommentLogic->getStartBylineId($id);
        $line['line_price_fmt'] = '￥'. number_format($line['line_price'],2);
        $driverInfo = $sellerLogic->getInfoById($line['seller_id']);
        $comment = $lineCommentLogic->getCommentPageBylineId($line['line_id'],PHP_INT_MAX);
        //dd($line['line_detail'][0]);  //todo 添加数据校验
        $this->assign('line', $line);
        $this->assign('driver', $driverInfo);
        $this->assign('comment', $comment);
        return $this->fetch();
    }


}