<?php

namespace app\web\controller;

use app\api\logic\StrategyLogic;

class Guide extends WebBase{


    public function detail(){
        $id = input('id');
        $guideLogic = new StrategyLogic();
        return $this->fetch();
     }
}
?>