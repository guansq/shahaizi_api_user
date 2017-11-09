<?php

namespace app\web\controller;

use app\common\logic\OrderCommentLogic;
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
        $map = [
            'cover_img_k' => '宽松',
            'cover_img_z' => '中等',
            'cover_img_y' => '严格',
            'cover_img_n' => '不退订',
        ];
        $pcpRet['result']['costCompensationLevel'] = $map[explode('###',$pcpRet['result']['costCompensation'])[0]];
        $pcpRet['result']['costCompensation'] = explode('###',$pcpRet['result']['costCompensation'])[1];
        $this->assign('packCarProduct',$pcpRet['result']);

        $where =[
            'car_product_id'=>$id,
            'deleted'=>0
        ];
        $commentLogic = new OrderCommentLogic();
        $list = $commentLogic->getListByWere($where);
        $comments =[
            'total'=>count($list),
            'list'=>$list,
        ];
        $this->assign('comments',$comments);
        return $this->fetch();
    }

}

?>