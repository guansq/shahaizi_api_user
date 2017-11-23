<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class Route extends WebBase{

    public function detail(){
        //$air_id = I('');
        return $this->fetch();
     }
}
?>