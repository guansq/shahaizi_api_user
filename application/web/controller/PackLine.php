<?php

namespace app\web\controller;

use app\common\logic\ConfigLogic;
use app\common\logic\OrderCommentLogic;
use app\common\logic\PackCommentLogic;
use app\common\logic\PackLineLogic;
use app\common\logic\SellerLogic;
use app\common\logic\PackCarInfo;
use app\common\logic\PackCarInfoLogic;
use think\Request;

class PackLine extends WebBase{

    const LEVEL_ARR = [
        1 => '经济型',//1=>jinji,2=>shushi         2=>'shushixing'
        2 => '舒适型',
        3 => '豪华型',
    ];

    public function detail(Request $request){
        $id = input('id');
        $lineLogic = new PackLineLogic();
        $sellerLogic = new SellerLogic();
        $orderCommentLogic = new OrderCommentLogic();
        $line = $lineLogic->find($id);
        if(empty($line)){
            return $this->error('你要查看的路线已经不存在');
        }
        $line = $line->toArray();
        $map = [
            'cover_img_k' => '宽松',
            'cover_img_z' => '中等',
            'cover_img_y' => '严格',
            'cover_img_n' => '不退订',
        ];
        if($line['is_admin']){
            $line['car_level_name'] = PackCarInfoLogic::LEVEL_ARR[$line['car_level']];
        }

        $line['costStatement'] = $line['cost_statement'];
        $line['costCompensationLevel'] = $map[explode('###',$line['cost_compensation'])[0]];
        $line['costCompensation'] = explode('###',$line['cost_compensation'])[1];
        $line_detail=$line['line_detail'];
        $de_json=html_json($line_detail);
        $line['line_detail'] =object_to_array($de_json);
        $where =[
            'line_id'=>$id,
            'deleted'=>0
        ];
        $line['score'] = intval($orderCommentLogic->where($where)->avg('pack_order_score'));
        //if(!$line['is_admin']){
        //    $line['line_price'] += floatval($line['line_price'])*intval(ConfigLogic::getSysconf('name_line'))/100 ; // 平台收取的佣金
        //}

        $line['line_price_fmt'] = moneyFormat($line['line_price']);
        $driverInfo = $sellerLogic->getInfoById($line['seller_id']);
        //dd($line['line_detail'][0]);  //todo 添加数据校验
        $this->assign('line', $line);
        $this->assign('driver', $driverInfo);

        $commentLogic = new OrderCommentLogic();
        $list = $commentLogic->getListByWere($where);
        //print_r(collection($list)->toArray());die;
        $comments =[
            'total'=>count($list),
            'list'=>$list,
        ];
        //print_r($comments);die;
        //得到车辆信息
        $carInfo = $sellerLogic->getCarInfo($line['car_id']);
        if(!empty($carInfo)){
            $carType = $sellerLogic->getCarTypeName($carInfo['car_type_id']);
            $carBrand = $sellerLogic->getCarTypeName($carType['pid']);
            $car = [
                'car_type' => $carType['car_info'],
                'car_brand' => $carBrand['car_info'],
                'car_seat_num' => $carType['seat_num'],
                'car_level_name' => self::LEVEL_ARR[$carType['car_level']],
            ];
        }
        $this->assign('car',$car);
        $this->assign('comments',$comments);
        return $this->fetch();
    }


}