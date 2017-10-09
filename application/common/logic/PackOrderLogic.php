<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
 * Time: 10:05
 */

namespace app\common\logic;

use think\Model;
use think\Page;


class PackOrderLogic extends BaseLogic{
    protected $table = 'ruit_pack_order';

    // 1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游7快捷订单
    const TYPE_ARR = [
        1 => '接机订单',
        2 => '送机订单',
        3 => '线路订单',
        4 => '单次接送',
        5 => '私人订制',
        6 => '按天包车游',
        7 => '快捷订单',
    ];

    const STATUS_UNCONFIRM         = -1; //未确定价格
    const STATUS_UNPAY             = 0; //未支付
    const STATUS_UNALLOT           = 1; //待派单
    const STATUS_UNJXDJ            = 2; //已派单_待接单
    const STATUS_UNSTART           = 3; //即将开始
    const STATUS_DOING             = 4; //进行中
    const STATUS_UNCOMMENT         = 5; //待评价
    const STATUS_FINISH            = 6; //已完成
    const STATUS_CANCEL            = 10; //取消
    const STATUS_AFTER_SALE        = 11; //申请售后中
    const STATUS_AFTER_SALE_PASS   = 12; //售后成功
    const STATUS_AFTER_SALE_REFUSE = 13; //售后拒绝

    const STATUS_ARR = [
        self::STATUS_UNCONFIRM => '未确定价格',
        self::STATUS_UNPAY => '未支付',
        self::STATUS_UNALLOT => '进行中',
        self::STATUS_UNJXDJ => '进行中',
        self::STATUS_UNSTART => '进行中',
        self::STATUS_DOING => '进行中',
        self::STATUS_UNCOMMENT => '待评价',
        self::STATUS_FINISH => '已完成',
        self::STATUS_CANCEL => '取消',
        self::STATUS_AFTER_SALE => '申请售后中',
        self::STATUS_AFTER_SALE_PASS => '售后成功',
        self::STATUS_AFTER_SALE_REFUSE => '售后拒绝',
    ];
    /**
     * 不在用户端显示的状态
     */
    const UN_SHOW_STATUS_ARR = [self::STATUS_UNCONFIRM, self::STATUS_CANCEL];

    // ALL,UN_PAY,DOING,UN_COMMENT,FINISH
    const STATUS_WHERE_ARR = [
        'ALL' => ['NOT IN', PackOrderLogic::UN_SHOW_STATUS_ARR],
        'UN_PAY' => self::STATUS_UNPAY,
        'DOING' => ['IN', [self::STATUS_UNALLOT, self::STATUS_UNJXDJ, self::STATUS_UNSTART, self::STATUS_DOING]],
        'UN_COMMENT' => self::STATUS_UNCOMMENT,
        'FINISH' => self::STATUS_FINISH,
    ];

    /*
       * 得到 我的包车订单
       */
    public function get_pack_order($statusCode, $user_id){
        $where = [
            'status' => self::STATUS_WHERE_ARR[$statusCode],
            'user_id' => $user_id
        ];
//        if($statusCode == 'DOING'){
//            $now = time();
//            $where = [
//                'status' => self::STATUS_UNSTART,
//                'start_time' => ['<=', $now],
//                'end_time' => ['>=', $now],
//                'user_id' => $user_id
//            ];
//        }
        $field = [
            'ord.air_id',
            'ord.order_sn',
            'ord.seller_id',
            'sel.hx_user_name',
            'sel.nickname',
            'sel.head_pic' => 'avatar',
            'ord.status',
            'ord.title',
            'ord.type',
            'ord.customer_name',
            'ord.drv_name',
            'ord.create_at',
            'ord.drv_phone',
            'ord.total_price',
            'ord.real_price'
        ];
        $count = M('pack_order')->where($where)->count();
        $page = new Page($count, 10);//每页10
        $order_list = $this->alias('ord')
            ->join('ruit_seller sel', 'ord.seller_id = sel.seller_id', 'LEFT')
            ->field($field)
            ->where($where)
            ->limit($page->firstRow.','.$page->listRows)
            ->order('air_id DESC')
            ->select();
        foreach($order_list as &$val){
            $val['create_at'] = shzDate($val['create_at']);
            $val['title'] = empty($val['title']) ? self::TYPE_ARR[$val['type']] : $val['title'];

            $val['real_price_fmt'] = moneyFormat($val['real_price']);
            $val['total_price_fmt'] = moneyFormat($val['total_price']);
        }
        $result = [
            'totalPages' => $page->totalPages,
            'list' => $order_list
        ];
        if(empty($order_list)){
            $return = [
                'status' => -1,
                'msg' => '数据为空',
                'result' => null
            ];
        }else{
            $return = [
                'status' => 1,
                'msg' => '成功',
                'result' => $result
            ];
        }
        return $return;
    }

    /*
     * 得到我的订单详情
     */
    public function get_pack_order_info($air_id, $user_id){
        $carBarLogic = new PackCarBarLogic();
        $sellerLogic = new SellerLogic();
        $info = M('pack_order')->where(['air_id' => $air_id, 'user_id' => $user_id])->find();
        if(empty($info)){
            $return = [
                'status' => -1,
                'msg' => '数据为空'
            ];
        }

        $seller = $sellerLogic->find($info['seller_id']);
        $carBar = $carBarLogic->find($info['con_car_type']);
        $info['con_car_type_name'] = empty($carBar['car_info']) ? '' : $carBar['car_info'];
        $info['real_price_fmt'] = moneyFormat($info['real_price']);
        $info['total_price_fmt'] = moneyFormat($info['total_price']);


        $carBar = $carBarLogic->find($info['req_car_type']);
        $info['req_car_type'] = empty($carBar['car_info']) ? '' : $carBar['car_info'];
        $info['hx_user_name'] = empty($seller['hx_user_name']) ? '' : $seller['hx_user_name'];
        $info['nickname'] = empty($seller['nickname']) ? '' : $seller['nickname'];
        $info['avatar'] = empty($seller['head_pic']) ? '' : $seller['head_pic'];
        $info['user_money_fmt'] = moneyFormat($seller['user_money']);
        $return = [
            'status' => 1,
            'msg' => '成功',
            'result' => $info
        ];

        return $return;
    }

    /**
     * 获取订单 order_sn
     * @return string
     */
    public function get_order_sn(){
        $order_sn = null;
        // 保证不会有重复订单号存在
        while(true){
            $order_sn = date('YmdHis').rand(1000, 9999); // 订单编号
            $order_sn_count = M('pack_order')->where("order_sn = '$order_sn'")->count();
            if($order_sn_count == 0){
                break;
            }
        }
        return $order_sn;
    }

    /*
     * 生成路线订单
     */
    public function create_pack_order($data, $user){
        $line = PackLineLogic::find($data['line_id']);
        if(empty($line)){
            return ['status' => -1, 'msg' => '当前线路不存在'];
        }
        $order_data = [
            'order_sn' => $this->get_order_sn(),
            'user_id' => $user['user_id'],
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'line_id' => $data['line_id'],
            'seller_id' => $line['seller_id'],
            'use_car_adult' => $data['use_car_adult'],
            'use_car_children' => $data['use_car_children'],
            'user_passport' => $data['user_passport'],
            'user_identity' => $data['user_identity'],
            'twenty_four' => $data['twenty_four'],
            'twenty_six' => $data['twenty_six'],
            'twenty_eight' => $data['twenty_eight'],
            'thirty' => $data['thirty'],
            'work_at' => $data['work_at'],
            'work_address' => $data['work_address'],
            'dest_address' => $data['dest_address'],
            'discount_id' => $data['discount_id'],
            'total_price' => $data['total_price'],
            'real_price' => $data['real_price'],
            'remark' => $data['remark'],
            'title' => $line['line_title'],
            'status' => PackOrderLogic::STATUS_UNPAY,
            'type' => 3,//1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游
            'user_message' => $data['user_message'],
            'create_at' => time(),
            'update_at' => time(),
        ];
        $result = M('pack_order')->save($order_data);

        if($result){
            $air_id = $this->getLastInsID();
            return [
                'status' => 1,
                'msg' => '成功',
                'result' => [
                    'real_price' => $data['total_price'],
                    'discount_id' => $data['discount_id'],
                    'air_id' => $air_id
                ]
            ];
        }else{
            return ['status' => -1, 'msg' => '失败'];
        }
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe: 用户 确认订单完成
     * @param $order
     * @param $user
     */
    public function confirmFinish(Model $order, $user){
        $order->status = PackOrderLogic::STATUS_UNCOMMENT;
        if($order->save()){
            return resultArray(2000);
        };
        return resultArray(5020);
    }
}