<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/17
 * Time: 13:51
 */
namespace app\api\controller;
require VENDOR_PATH . 'jpush/jpush/autoload.php';
use think\Controller;
use JPush\Client as JPush;


class Push extends Base{

    public $app_key = 'ae18520eca229fc7e23c5f86'; // = 'ae18520eca229fc7e23c5f86';
    public $master_secret = '7e5df030845bb7bcdfce058d'; // = '7e5df030845bb7bcdfce058d ';
    public $title = '傻孩子App';
    public $content = '默认内容';
    public $pushId = '140fe1da9e932ec8628';
    public static $msg_info = [
        1000 => '系统内部错误',
        1001 => '只支持 HTTP Post 方法',
        1002 => '缺少了必须的参数',
        1003 => '参数值不合法',
        1004 => '验证失败',
        1005 => '消息体太大',
        1008 => 'app_key参数非法',
        1009 => '推送对象中有不支持的key',
        1011 => '没有满足条件的推送目标',
        1020 => '只支持 HTTPS 请求',
        1030 => '内部服务超时',
        2002 => 'API调用频率超出该应用的限制',
        2003 => '该应用appkey已被限制调用 API',
        2004 => '无权限执行当前操作',
    ];

    public function index(){
        $client = new JPush($this->app_key, $this->master_secret);
        $msg_info = self::$msg_info;
        $message = [
            'title' => $this->title,
            //'content_type' => '测试标题',
        ];
        $content = $this->content;
        //echo $content;die;
        $regId = $this->pushId;
        $push = $client->push()->setPlatform('all');
        if(!empty($regId)){
            $push = $push->addRegistrationId($regId);
        }else{
            $push = $push->addAllAudience();//为空就推送全部
        }
        //$push = $push->message($content, $message);
        //$push = $push->setNotificationAlert($content);//单独推送标题
        $push = $push->androidNotification($content, $message)->iosNotification($content, $message);//推送安卓
        try {
            $response = $push->send();
            if($response['http_code'] == 200){
                return resultArray(1,'推送成功',$response['body']);
            }else{
                return resultArray(-1,'推送失败',$response['body']);
            }
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            // try something here
            $code_num = $e->getCode();
            return resultArray(-1,'推送失败',$msg_info[$code_num]);
        } catch (\JPush\Exceptions\APIRequestException $e) {
            // try something here
            $code_num = $e->getCode();
            return resultArray(-1,'推送失败',$msg_info[$code_num]);
        }
    }

    public function test(){
        $result = pushMessage('推送的标题', '推送的内容', '1114a8979297c0e17ee', 7, 0);
        //print_r($result);
//        $mobile = input('mobile');
//        $content = input('content');
//        sendSMSbyApi($mobile,$content);
//        echo '发送成功！';
    }
}