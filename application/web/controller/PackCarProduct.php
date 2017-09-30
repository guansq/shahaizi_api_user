<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class PackCarProduct extends WebBase{

    public function detail(){
        return $this->fetch();
     }

}
?>