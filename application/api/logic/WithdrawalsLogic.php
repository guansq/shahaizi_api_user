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

use think\Log;


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
     * Describe: 申请提现 只能提一次 ，申请成功->冻结申请金额，余额减掉-》后台审核
     * @param $reqParams
     * @param $user
     */
    public function applyWithdrawals($reqParams, $user){
        $userLogic = new UserLogic();
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

        $userObj = $userLogic->where('user_id', $user['user_id'])->find();
        $userObj->user_money -= $reqParams['amount'];
        $userObj->frozen_money += $reqParams['amount'];
        if(!$userObj->save()){
            trace("用户userid=$user[user_id]申请提现,冻结余额失败", Log::ERROR);
            return resultArray(5010, '提交失败,联系客服!。');
        };

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

        if(!$this->create($data)){
            // 创建记录 失败要把余额加回去
            $userObj->user_money += $reqParams['amount'];
            $userObj->frozen_money -= $reqParams['amount'];
            $userObj->save();
            return resultArray(5010, '提交失败,联系客服!。');
        }
        return resultArray(2000, '提交成功。', [
            'amount' => number_format($reqParams['amount'], 2),
            'balance' => number_format($userObj->user_money, 2)
        ]);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 申请提现  不限制申请次数,申请中总金额不能大于可提现金额,
     * @param $reqParams
     * {number}  amount   提现金额 应当小于等于用户余额.
     * {string=zfb,wx,bank}  withdrawalsWay 提现方式 zfb=支付宝,wx=微信,bank=银行汇款.
     * {string}  account 提现账户.
     * {string}  person  提现人姓名.
     * {string}  bankName   提现银行  当提现方式为银行汇款时此项必填.
     * {string}  bankOfDeposit 提现人开户行 当提现方式为银行汇款时此项必填.
     * {string}  [phone] 提现人联系电话.
     *
     * @param $user
     */
    public function applyWithdrawalsInShop($reqParams, $user){
        $userLogic = new UserLogic();
        $userObj = $userLogic->find($user['user_id']);

        //C('TOKEN_ON',true);
        if($userObj->is_lock == 1){
            return resultArray(4010, '账号异常已被锁定');
        }

        $distributMin = tpCache('basic.min');      //最少提现额度 100
        $distributNeed = tpCache('basic.need');    //满多少才能提 100
        //$this->verifyHandle('withdrawals');

        if($reqParams['amount'] < $distributMin){
            return resultArray(4010, "每次最少提现额度{$distributMin}元");
        }
        if($reqParams['amount'] > $userObj->user_money){
            return resultArray(4010, "你最多可提现{$userObj->user_money}元.");
        }

        if($userObj->user_money < $distributNeed){
            return resultArray(4010, '账户余额最少达到'.$distributNeed.'元才能提现');
        }
        $withdrawalTotal = $this->where('user_id', $userObj->user_id)->where('status', self::STATUS_AUDITTING)->sum('money');

        if($userObj->user_money < ($withdrawalTotal + $reqParams['amount'])){
            return resultArray(4010, "您有提现申请待处理，本次提现余额不足。");
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

        if(!$this->create($data)){
            return resultArray(5010, '提交失败,联系客服!。');
        }
        return resultArray(2000, '提交成功。', [
            'amount' => moneyFormat($reqParams['amount']),
            'balance' => moneyFormat($userObj->user_money)
        ]);

    }

}