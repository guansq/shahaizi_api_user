<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class Visa extends WebBase{
    public function index(){
        return $this->fetch('visa');
    }
    public function visaStrategy(){
        return $this->fetch('visa-strategy');
    }
}
?>