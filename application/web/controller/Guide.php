<?php

namespace app\web\controller;

use app\api\logic\StrategyLogic;
use app\common\logic\GuideLogic;

class Guide extends WebBase{


    public function detail(){
        $id = input('id');
        $guideLogic = new GuideLogic();
        $user = $this->getUserByToken();
        $guide = $guideLogic->getDetail($id,$user);
        if(empty($guide)){
            return $this->error('您要查看的攻略不存在');
        }
        $this->assign('guide',$guide);
        return $this->fetch();
     }
}
?>