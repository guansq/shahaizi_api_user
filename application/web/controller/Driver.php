<?php

namespace app\web\controller;

use app\common\logic\OrderCommentLogic;
use app\common\logic\PackCarInfoLogic;
use app\common\logic\PackLineLogic;
use app\common\logic\RegionCountryLogic;
use app\common\logic\RegionLogic;
use app\common\logic\SellerLogic;
use app\common\logic\PackCarInfo;

use think\Request;

class Driver extends WebBase{

    public function index(){
        return $this->fetch('driver-detail');
     }

    public function detail(){
        $id = input('id');
        $sellerLogic = new SellerLogic();
        $seller =  $sellerLogic->find($id);
        $seller['img_url']=explode("|",$seller['img_url']);
        $regionLogic = new RegionLogic();
        $regionCtLogic = new RegionCountryLogic();
        $seller['cityFullName'] = $regionCtLogic->getNameByid($seller['country_id']).'·'.$regionLogic->getNameByid($seller['city']);
        if(empty($seller['cover_img'])){
            $seller['cover_img'] = '/application/web/view/static/image/bg_sidao.png';
        }
        $this->assign('seller',$seller);
        $CarInfo = new PackCarInfoLogic();
        $seller_car = $CarInfo->getMyCar($id);
        //$seller_car['car_level_name'] = PackCarInfoLogic::LEVEL_ARR[$seller_car['car_level']];
        //$seller_car =  $CarInfo->field()->where("seller_id",$id)->where('is_state',PackCarInfoLogic::STATUS_PASS)->select();
        //print_r(collection($seller_car)->toArray());die;
        $this->assign('seller_car',$seller_car);
        $lineLogic = new PackLineLogic();
        $line = $lineLogic->selectShowListBySellerId($id);
        $this->assign('line_array',$line);
        $where =[
            'seller_id'=>$id,
            'deleted'=>0
        ];
        $commentLogic = new OrderCommentLogic();
        $list = $commentLogic->getListByWere($where);
        $comments =[
            'total'=>count($list),
            'list'=>$list,
        ];
        $this->assign('comments',$comments);
        return $this->fetch('detail');
     }
}
?>