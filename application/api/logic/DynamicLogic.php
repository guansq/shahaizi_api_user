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
 * 个人动态定义
 * Class CatsLogic
 * @package common\Logic
 */
class DynamicLogic extends BaseLogic{

    protected $table ='ruit_article_new_action';

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 发布动态
     * @param $reqParams
     * @Param  img    封面图片 多张用“|” 分割 ，第一张为默认封面.
     * @Param  title  标题.
     * @Param  content 内容.
     * @param $user
     */
    public function createDynamic($reqParams, $user){
        $data = [
        'cover_img' => $reqParams['img'],     // 封面图片 多张用“|” 分割 ，第一张为默认封面.
        'title' => $reqParams['title'],     // 标题.
        'content' =>$reqParams['content'],    // 内容.
        'user_id' =>$user['user_id'],
        'user_name' =>$user['nickname'],
        ];
        if(!$this->create($data)){
            return resultArray(5020);
        };
        return resultArray(2000);
    }
}