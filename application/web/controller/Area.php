<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class Area extends WebBase{
    public function index(){
        return $this->fetch('area');
     }
     public function areaDetail(){
         return $this->fetch('area-detail');
      }
}
?>