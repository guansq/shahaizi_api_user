<?php

namespace app\web\controller;

use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use think\Request;

class About extends WebBase{

    public function index(){
        $where = [
            'article_id' => 44,
            //'is_open' => 1,
        ];
        $info = M('article')->where($where)->find();
        $info['content'] = htmlspecialchars_decode($info['content']);
        $this->assign('info',$info);
        return $this->fetch('about-us');
     }
}
?>