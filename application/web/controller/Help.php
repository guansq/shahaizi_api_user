<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class Help extends WebBase{
    public function index(){
        $where = [
            'cat_id' => 49,
            'is_open' => 1,
        ];
        $list = M('article')->where($where)->select();
        foreach($list as &$val){
            $val['content'] = htmlspecialchars_decode($val['content']);
        }
        $this->assign('list',$list);
        //print_r($list);
        return $this->fetch('help-center');
     }
}
?>