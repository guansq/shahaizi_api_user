<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 9:30
 */
namespace app\common\logic;

use think\Model;

class ConfigLogic extends BaseLogic{

    protected $table = 'ruit_config';

    /*
     * 得到城市区号
     */
    public function get_country_number(){
        return M('country_mobile_prefix')->select();
    }


    /**
     * 获取设备或配置系统参数
     * @param string $name  参数名称
     * @param bool   $value 默认值
     * @return string|bool
     */
   public static function getSysconf($name, $defaultValue = ''){
        $data = self::where(['name' => $name])->find();
        if(empty($data)){
            return $defaultValue;
        }
        return $data['value'];

    }

    /**
     * 设置设备或配置系统参数
     * @param string $name 参数名称
     */
    public static function setSysconf($name, $value = '', $type = 'app', $remark = ''){
        $data = ['name' => $name, 'value' => $value, 'inc_type' => $type, 'desc' => $remark];
        $oldData = self::where('name', $name)->find();
        if(empty($oldData)){
            return self::create($data);
        }else{
            return self::update($data, ['name' => $name]);
        }
    }


    /**
     * 获取客服电话
     */
    public function getServiceTel(){
        $list = M('vip_telephone')->where('is_show',1)->select();
        if(empty($list)){
            return resultArray(-1,'数据为空',[]);
        }
        return resultArray(1,'成功',$list[0]);
    }
}