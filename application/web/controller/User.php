<?php

namespace app\web\controller;

use app\common\logic\CountryMobilePrefixLogic;

class User extends WebBase{

    public function register(){
        $countryMobile = new CountryMobilePrefixLogic();
        $result = $countryMobile->select();
        $this->assign("country_code", $result);
        return $this->fetch();
    }
}

?>