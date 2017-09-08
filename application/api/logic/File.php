<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/22
 * Time: 9:45
 */

namespace app\api\logic;

use service\HttpService;

class File{

    public $url = 'http://oss.ruitukeji.com/index/uploadFiles';

    // 上传文件 php 5.5
    function uploadFile(\think\File $file){

        //$info = $file->move('/tmp','wztx_tmp_avatar.png');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
        //$filePath = ROOT_PATH . 'public' . DS . 'upload'. DS .$info->getSaveName();
        $data = [
            'rt_appkey' => '2017ShaHaiZi_uQbJFDUPPUGc6MiN_j99YeHXpb3fsAT0V',
            'file' => '@'.$info->getPathname()
        ];

        $return_data = HttpService::post($this->url, $data, 60);
        //trace($return_data);
//        dump($data);
        if(empty($return_data)){
            return resultArray(-1);
        }
        $ossRet = json_decode($return_data,true);
        if(empty($ossRet)){
            return resultArray(-1,'上传图片错误');
        }
        if($ossRet['code'] !=2000){
            return resultArray(-1,$ossRet['msg']);
        }
        return resultArray(1,$ossRet['msg'],$ossRet['result']);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 上传图片
     * @param \think\File $file
     * @param array       $user
     * @return array
     */
    public function uploadImg(\think\File $file){
        $fileLogic = model('File', 'logic');
        if(empty($file)){
            return resultArray(4001);
        }

        $ossRet = $fileLogic->uploadFile($file);
        if(empty($ossRet) || $ossRet['code'] != 2000){
            return resultArray($ossRet);
        }

        return resultArray($ossRet);
    }
}