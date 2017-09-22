<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class Help extends WebBase{
    public function index(){
        return $this->fetch('help-center');
     }
}
?>