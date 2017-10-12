<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use app\common\logic\PackCarInfo;

use think\Request;

class Driver extends WebBase{

    public function index(){
        return $this->fetch('driver-detail');
     }

    public function detail(){
        $id = input('id');
        $sellerLogic = new SellerLogic();
        $seller =  $sellerLogic->find($id);
        $seller['img_url']=explode("|",$seller['img_url']);
        $this->assign('seller',$seller);
        $CarInfo = new PackCarInfo();
        $seller_car =  $CarInfo->where(["seller_id"=>$id])->select();
        $this->assign('seller_car',$seller_car);
        $lineLogic = new PackLineLogic();
        $line = $lineLogic->find($id);
        $line_detail=$line['line_detail'];
        $de_json=html_json($line_detail);
        $line['line_detail'] =object_to_array($de_json);
        $this->assign('line_array',$line);
        return $this->fetch('detail');
     }
}
?>