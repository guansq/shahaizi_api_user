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

        $line['line_detail'] = json_encode($line['line_detail'], true);
        $line = $line->toArray();
        $line['plat_start'] = $lineCommentLogic->getStartBylineId($id);
        $driverInfo = $sellerLogic->getInfoById($line['seller_id']);
        $comment = $lineCommentLogic->getCommentPageBylineId($line['line_id'],PHP_INT_MAX);
        //dd($comment);  //todo 添加数据校验
        $this->assign('line', $line);
        $this->assign('driver', $driverInfo);
        $this->assign('comment', $comment);
        return $this->fetch();
    }


}