<?php
/**
 * User: Plator <aicodes.cn>
 * Date: 2017/9/15
 * Time: 9:43
 * Desc: 环信类
 */
namespace emchat;
class EasemobUse
{
	private $userName;
	private $password;
	private $easeData;

    public function __construct ()
    {
        $this -> options['client_id']='YXA6Kv9EIJNtEeewdnMQJ_FKMA';
        $this -> options['client_secret']='YXA65H9MlMZ4OTvc51WwajxViTlHAz0';
        $this -> options['org_name']='1102170901115301';
        $this -> options['app_name']='shahaizi';
        $this -> easeData = new Easemob($this -> options);
    }

    /**
     * 重置密码
     */
    public function resetPassword ()
	{
        return $this -> easeData ->resetPassword($this->getUserName(),$this->getPassword());
	}

	/*
	 * 创建单个用户
	 */
    public function createSingleUser ()
    {
        return $this -> easeData ->createUser($this->getUserName(),$this->getPassword());
    }

    /*
	 * 修改昵称
	 */
    public function updateNickname ($nickname)
    {
        return $this -> easeData -> editNickname($this->getUserName(),$nickname);
    }

    /**
     * 获取单个用户信息
     */
    public function getUserInfo ($user_name)
	{
       return  $this -> easeData -> getUser($user_name);
	}

    /**
	 * 设置单个用户名
     * @param $name
     */
    public function setUserName ($name)
	{
		$this -> userName = $name;
	}

	/**
	 * 设置单个用户密码
     * @param $password
     */
    public function setPassword ($password)
	{
        $this -> password = $password;
	}

    /**
     * 获取单个用户密码
     * @param $password
     */
    public function getPassword ()
    {
        return $this -> password;
    }

    /**
     * 获取单个用户密码
     * @param $password
     */
    public function getUserName ()
    {
        return $this -> userName;
    }

}
?>
