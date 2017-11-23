<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use app\common\logic\PackOrderLogic;
use think\Request;

class Route extends WebBase{

    public function detail(){
        $air_id = I('air_id');
        $pack_order = new PackOrderLogic();
        $result = $pack_order->get_private_detail($air_id);
        if($result['status'] != -1){
            $this->error("抱歉数据为空");
        }
        //print_r($result['result']);
        $this->assign('info',$result['result']);
        return $this->fetch();
     }
}
?>