<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

class SuggestionFeedbackLogic extends BaseLogic{
    protected $table = 'ruit_suggestion_feedback';

    //用户类型 1是用户端 2是司导端
    const  TYPE_CLIENT_USER   = 1;
    const  TYPE_CLIENT_SELLER = 2;

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe: 增加反馈
     * @param $params
     * @param $userId
     */
    public function addFeedback($params, $user){
        $data = [
            'name' => $user['nickname'],
            'user_id' => $user['user_id'],
            'type_id' => $params['typeId'],
            'content' => $params['content'],
            'imgurl' => $params['imgs'],
            'enabled' => 1,
            'type' => self::TYPE_CLIENT_USER,
        ];
        if($this->create($data)){
            return resultArray(2000);
        };
        return resultArray(5010, '提交失败请稍后再试');
    }

}