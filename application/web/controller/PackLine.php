<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class PackLine extends WebBase{


    public function detail(Request $request){
        $id = input('id');
        $lineLogic = new PackLineLogic();
        $sellerLogic = new SellerLogic();
        $line = $lineLogic->find($id);
        if(empty($line)){
            return $this->error('你要查看的路线已经不存在');
        }
        $driver =$sellerLogic->find($line['seller_id']);

        $line = $line->toArray();
        $driver = $driver->toArray();
        $this->assign('line',$line);
        $this->assign('driver',$driver);
        return $this->fetch('detail');
    }


}