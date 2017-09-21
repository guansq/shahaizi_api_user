<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use think\Request;

class PackLine extends WebBase{


    public function detail(Request $request){
        $id = input('id');
        $lineLogic = new PackLineLogic();
        $line = $lineLogic->find($id);
        if(empty($line)){
            return $this->error('你要查看的路线已经不存在');
        }
        $line = $line->toArray();
        $this->assign('line',$line);
        return $this->fetch('detail');
    }


}