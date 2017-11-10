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
    // 时间字段取出后的默认时间格式
    protected $dateFormat;
    // 1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游7快捷订单

    //1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游
    const TYPE_MEET_AIRPORT = 1;
    const TYPE_SEND_AIRPORT = 2;
    const TYPE_LINE         = 3;
    const TYPE_SINGLE       = 4;
    const TYPE_CUSTOM       = 5;
    const TYPE_CHARTERED    = 6;
    const TYPE_QUICK        = 7;

    const TYPE_ARR = [
        self::TYPE_MEET_AIRPORT => '接机订单',
        self::TYPE_SEND_AIRPORT => '送机订单',
        self::TYPE_LINE => '线路订单',
        self::TYPE_SINGLE => '单次接送',
        self::TYPE_CUSTOM => '私人订制',
        self::TYPE_CHARTERED => '按天包车游',
        self::TYPE_QUICK => '快捷订单',
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
    const UN_SHOW_STATUS_ARR = [self::STATUS_UNCONFIRM];//, self::STATUS_CANCEL

    // ALL,UN_PAY,DOING,UN_COMMENT,FINISH
    const STATUS_WHERE_ARR = [
        'ALL' => ['NOT IN', PackOrderLogic::UN_SHOW_STATUS_ARR],
        'UN_PAY' => self::STATUS_UNPAY,
        'DOING' => ['IN', [self::STATUS_UNALLOT, self::STATUS_UNJXDJ, self::STATUS_UNSTART, self::STATUS_DOING]],
        'UN_COMMENT' => self::STATUS_UNCOMMENT,
        'FINISH' => ['IN', [self::STATUS_UNCOMMENT, self::STATUS_FINISH]],
    ];

    /*
       * 得到 我的包车订单
       */
    public function get_pack_order($statusCode, $user_id){
        $where = [
            'status' => self::STATUS_WHERE_ARR[$statusCode],
            'is_del' => 0,
            'user_id' => $user_id
        ];

        if($statusCode == 'FINISH'){
            $where['user_order_status'] = 1;
        }
        if($statusCode == 'UN_COMMENT'){
            $where['user_order_status'] = 0;
        }

        $field = [
            'ord.air_id',
            'ord.order_sn',
            'ord.seller_id',
            'sel.hx_user_name',
            'sel.nickname',
            'sel.head_pic' => 'avatar',
            'sel.nickname' => 'drv_name',
            'sel.mobile' => 'drv_phone',
            'ord.status',
            'ord.title',
            'ord.type',
            'ord.customer_name',
            'ord.create_at',
            'ord.total_price',
            'ord.seller_order_status',
            'ord.user_order_status',
            'ord.line_id',
            'ord.user_confirm',
            'ord.seller_confirm',
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
            // $val['create_at'] = shzDate($val['create_at']);
            $val['title'] = empty($val['title']) ? self::TYPE_ARR[$val['type']] : $val['title'];
            //把自动转化的create_at转化时间戳
            if($val['status'] == 0 && (time() - strtotime($val['create_at'])) > 1800){//未支付的订单 且大于1800秒的时候 自动取消
                $result = $this->where(['air_id' => $val['air_id']])->update(['status' => 10]);//取消操作
                if($result !== fasle){
                    $val['status'] = 10;
                }
            }
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
     * 得到我的订单数据 {String=ALL,UN_PAY,DOING,UN_COMMENT,FINISH}
     */
    public function get_order_around($user_id){
        $field = ['ALL','UN_PAY','DOING','UN_COMMENT','FINISH'];
        $returnArr = [];
        foreach($field as $val){
            $returnArr[$val] = $this->get_my_order_count($val,$user_id);
        }
        return resultArray(1,'成功',$returnArr);
    }

    public function get_my_order_count($statusCode, $user_id){
        $where = [
            'status' => self::STATUS_WHERE_ARR[$statusCode],
            'is_del' => 0,
            'user_id' => $user_id
        ];
        if($statusCode == 'FINISH'){
            $where['user_order_status'] = 1;
        }
        if($statusCode == 'UN_COMMENT'){
            $where['user_order_status'] = 0;
        }
        return $count = M('pack_order')->where($where)->count();
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
        $info['costStatement'] = '';//费用说明
        $info['costCompensation'] = '';//补偿改退
        $info['costCompensationLevel'] = '';//补偿改退的等级
        $map = [
            'cover_img_k' => '宽松',
            'cover_img_z' => '中等',
            'cover_img_y' => '严格',
            'cover_img_n' => '不退订',
        ];
        if(in_array($info['type'],[1,2,6,7]) ){//1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游7快捷订单
            if($info['car_product_id']){//车产品ID
                $car_product = M('pack_car_product')->where("id={$info['car_product_id']}")->find();
                $info['costStatement'] = $car_product['cost_statement'];
                $info['costCompensationLevel'] = $map[explode('###',$car_product['costCompensation'])[0]];
                $info['costCompensation'] = explode('###',$car_product['costCompensation'])[1];
            }
        }
        if($info['type'] == 3){//线路单独进行取出退订政策和费用说明
            if($info['line_id']){//线路ID
                $car_line = M('pack_line')->where("line_id={$info['line_id']}")->find();
                $info['costStatement'] = $car_line['cost_statement'];
                $info['costCompensationLevel'] = $map[explode('###',$car_line['costCompensation'])[0]];
                $info['costCompensation'] = explode('###',$car_line['costCompensation'])[1];
            }
        }
        $carBar = $carBarLogic->find($info['req_car_type']);
        $info['req_car_type'] = empty($carBar['car_info']) ? '' : $carBar['car_info'];
        $info['hx_user_name'] = empty($seller['hx_user_name']) ? '' : $seller['hx_user_name'];
        $info['nickname'] = empty($seller['nickname']) ? '' : $seller['nickname'];
        $info['avatar'] = empty($seller['head_pic']) ? '' : $seller['head_pic'];
        $info['user_money_fmt'] = moneyFormat($seller['user_money']);
        if($info['type'] == 6){
            $base_day = M('pack_base_by_day')->where(['base_id'=>$air_id])->find();
            $info['pack_start_time'] = [];
            if(!empty($base_day)){
                $info['pack_start_time'] = explode('|',$base_day['pack_time']);
                foreach($info['pack_start_time'] as &$val){
                    $val = shzDate($val);
                }
            }
        }
        $drv_info = M('seller')->where(['seller_id' => $info['seller_id']])->find();
        $info['drv_phone'] = '';
        if($drv_info){
            $info['drv_phone'] = $drv_info['mobile'];
        }
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
        //$data['line_id'] = 2;
        $line = PackLineLogic::find($data['line_id']);
        if(empty($line)){
            return ['status' => -1, 'msg' => '当前线路不存在'];
        }
        $lineDetail = json_decode(htmlspecialchars_decode($line['line_detail']), true);
        $firstSite = $lineDetail[0]['port_detail'][0]['site_name'];
        $lastPort = $lineDetail[count($lineDetail) - 1]['port_detail'];
        $lastSite = $lastPort[count($lastPort) - 1]['site_name'];
        if(empty($firstSite)){
            return ['status' => -1, 'msg' => '无法获取起始地'];
        }
        if(empty($lastSite)){
            return ['status' => -1, 'msg' => '无法获取目的地'];
        }
        $discountPrice = 0; // FIXME 获取优惠券金额
        $brokerage = 0;

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
            'start_time' => strtotime($data['work_at']),
            'work_address' => $firstSite,
            'dest_address' => $lastSite,
            'discount_id' => $data['discount_id'],
            'total_price' => $line['line_price'],
            'real_price' => $line['line_price'] - $discountPrice,
            'remark' => $data['remark'],
            'title' => $line['line_title'],
            'status' => PackOrderLogic::STATUS_UNPAY,
            'type' => 3,//1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游
            'user_message' => $data['user_message'],
            //'commission_money' => //佣金金额
            //'seller_money' => //订单金额 扣除佣金的金额
            'create_at' => time(),
            'update_at' => time(),
        ];
        if(!$line['is_admin']){
            $order_data['commission_money'] = $commission_money = floatval($line['line_price'])*intval(ConfigLogic::getSysconf('name_line'))/100 ; // 佣金金额
            $order_data['seller_money'] = $line['line_price'] -  $commission_money; // 佣金金额
            $seller = SellerLogic::findByDrvId($line['seller_id']);
            if(!empty($seller)){
                pushMessage('线路预订未支付', '您的线路已被客人预订，请保持通话畅通，随时与客人联系', $seller['device_no'], $seller['seller_id'], 1);
            }
        }
        $result = M('pack_order')->save($order_data);

        if($result){
            $air_id = $this->getLastInsID();
            return [
                'status' => 1,
                'msg' => '成功',
                'result' => [
                    'real_price' => $order_data['real_price'],
                    'real_price_fmt' => moneyFormat($order_data['real_price']),
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
        $order->user_confirm = 1;//更改用户确认为1
        $this->addUserRecharge($order->air_id);//如果商家确认

        $seller = SellerLogic::findByDrvId($order['seller_id']);
        if(!empty($seller)){
            pushMessage('订单确认结束', '您有一条订单，客人已确认结束', $seller['device_no'], $seller['seller_id'], 1);
        }
        if($order["seller_confirm"])
        {
            $order->status = PackOrderLogic::STATUS_UNCOMMENT;//两边都确认才进行待评价
        }
        if($order->save()){
            return resultArray(2000);
        };
    }

    /**
     * 根据订单号增加用户余额
     */
    public function addUserRecharge ($air_id)
    {
        $pack_order = M("pack_order") -> where("air_id = $air_id") -> find();
        if($pack_order["seller_confirm"])
        {
            if($pack_order["seller_id"])
            {
                $employee = getPlatformCharge(1);
                $real_price = floatval($pack_order["real_price"]);
                $user_money = $real_price + floatval($pack_order["add_recharge"]) - ($real_price * $employee/100);
                M("seller") -> where("seller_id = {$pack_order['seller_id']}") -> setInc('user_money',$user_money);//["user_money" => $user_money]
            }
        }
    }



    public function delPackOrder($air_id){
        $result = M('pack_order')->where('air_id',$air_id)->update(['is_del'=>1]);
        if($result !== false){
            return ['status' => 1, 'msg' => '成功'];
        }
        return ['status' => -1, 'msg' => '失败'];
    }
}