<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/13
 * Time: 10:55
 */
namespace app\api\controller;
use think\Controller;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
class File extends Controller{
    /**
     * @api      {POST} /index.php?m=Api&c=File&a=uploadImg  上传图片done
     * @apiName  uploadImg
     * @apiGroup File
     * @apiHeader {String} authorization-token           token.
     * @apiParam {Image} file    上传的文件 最大5M 支持'jpg', 'gif', 'png', 'jpeg'
     * @apiSuccess {String} url  下载链接(绝对路径)
     */
    public function uploadImg(){
        $file = $this->request->file('file');

        if(empty($file)){
            returnJson(-1,'文件不能为空');
        }
        $rule = ['size' => 1024*1024*20, 'ext' => 'jpg,gif,png,jpeg'];
        validateFile($file, $rule);
        $logic = model('File', 'logic');

        returnJson($logic->uploadImg($file));
    }

    /**
     * 图片上传
     * @return String 图片的完整URL
     */
    public function upload()
    {
        if(request()->isPost()){
            $file = request()->file('file');
            // 要上传图片的本地路径
            $filePath = $file->getRealPath();
            $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀
            //获取当前控制器名称
            $controllerName=I('c');

            // 上传到七牛后保存的文件名
            $key =substr(md5($file->getRealPath()) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;
            require_once APP_PATH . '/../vendor/qiniu/php-sdk/autoload.php';
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey = config('ACCESSKEY');
            $secretKey = config('SECRETKEY');
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 要上传的空间
            $bucket = config('BUCKET');
            $domain = config('DOMAINImage');
            $token = $auth->uploadToken($bucket);
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                return ["status"=>-1,"msg"=>$err];
            } else {
                //返回图片的完整URL
                $url = $domain.$ret['key'];
                return json(["status"=>'1',"msg"=>"上传完成","result"=>['url'=>$url]]);
            }
        }
    }
}