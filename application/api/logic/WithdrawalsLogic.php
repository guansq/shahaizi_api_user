<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\api\logic;


/**
 *  提现逻辑
 * Class CatsLogic
 * @package common\Logic
 */
class WithdrawalsLogic extends BaseLogic{

    protected $table = 'ruit_withdrawals';

    // 状态：-2删除作废-1审核失败0申请中1审核通过2付款成功3付款失败
    const STATUS_DELETE           = -2;           // 删除作废
    const STATUS_AUDIT_FAILURE    = -1;    // 审核失败
    const STATUS_AUDITTING        = 0;        // 审核中
    const STATUS_AUDIT_PASS       = 1;       // 审核通过
    const STATUS_TRANSFER_PASS    = 2;    // 转账成功
    const STATUS_TRANSFER_FAILURE = 3;// 转账失败

    const STATUS_ARR = [
        self::STATUS_DELETE => '作废',
        self::STATUS_AUDIT_FAILURE => '审核失败',
        self::STATUS_AUDITTING => '审核中',
        self::STATUS_AUDIT_PASS => '审核通过',
        self::STATUS_TRANSFER_PASS => '转账成功',
        self::STATUS_TRANSFER_FAILURE => '转账失败',
    ];

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 申请提现
     * @param $reqParams
     * @param $user
     */
    public function applyWithdrawals($reqParams, $user){

        $count = $this->where('user_id', $user['user_id'])->where('status', self::STATUS_AUDITTING)->count();
        if($count > 0){
            return resultArray(4010, '您有提现申请在处理中。如有疑问请联系客服。');
        }
        switch($reqParams['withdrawalsWay']){
            case 'wx':
                $bankName = '微信';
                break;
            case 'zfb':
                $bankName = '支付宝';
                break;
            case 'bank':
            default:
                $bankName = $reqParams['bankName'];
        }
        $data = [
            'user_id' => $user['user_id'],
            'money' => $reqParams['amount'],
            'bank_name' => $bankName,
            'bank_card' => $reqParams['account'],
            'bank_of_deposit' => $reqParams['bankOfDeposit'],
            'user_phone' => $reqParams['phone'],
            'realname' => $reqParams['person'],
            'status' => self::STATUS_AUDITTING,
            'create_time' => time(),
        ];
        if($this->create($data)){
            return resultArray(2000, '提交成功。');
        }else{
            return resultArray(5010, '提交失败,联系客服!。');
        }
    }

}