<?php

namespace app\web\controller;

use app\common\logic\PackCarProductLogic;

class PackCarProduct extends WebBase{

    public function detail(){
        $id = input('id');
        $pcpLogic = new PackCarProductLogic();
        $user = $this->getUserByToken();
        $pcpRet = $pcpLogic->getDetailById($id, $user);
        if(empty($pcpRet) || $pcpRet['status'] != 2000){
            return $this->error($pcpRet['msg'] );
        }
        $this->assign('packCarProduct',$pcpRet['result']);
        return $this->fetch();
    }

}

?>