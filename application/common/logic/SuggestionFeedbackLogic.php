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

}