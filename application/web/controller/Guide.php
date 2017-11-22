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
        if($guide['is_admin'] == 0){
            $de_json=html_json($guide['content']);
            $guide['content'] =object_to_array($de_json);
        }

        //dd($guide);
        $this->assign('guide',$guide);
        return $this->fetch();
     }
}
?>