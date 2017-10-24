<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

class SuggestionFeedbackTypeLogic extends BaseLogic{
    protected $table = 'ruit_suggestion_feedback_type';

    public function getList(){
        $ret = $this->where('enabled', 1)->field('enabled', true)->select();
        if(empty($ret)){
            return resultArray(4004);
        }
        return resultArray(2000, '', ['list' => $ret]);
    }

}