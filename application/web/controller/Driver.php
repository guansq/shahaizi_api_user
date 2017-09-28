<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class Driver extends WebBase{

    public function index(){
        return $this->fetch('driver-detail');
     }

    public function detail(){
        $id = input('id');
        $sellerLogic = new SellerLogic();
        $seller =  $sellerLogic->find($id);
        $this->assign('seller',$seller);
        return $this->fetch('detail');
     }

}
?>