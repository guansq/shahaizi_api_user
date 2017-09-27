<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace ruitu;

use think\Page;

class PageVo{

    public $p = 1;      /// 当前页码
    public $pageSize = 20; // 列表每页显示行数
    public $totalRows = 0; // 总行数
    public $totalPages = 0; // 分页总页面数
    public $list = null; //

    /**
     * 架构函数
     * @param array $p          当前页码
     * @param array $pageSize   列表每页显示行数
     * @param array $totalRows  总行数
     * @param array $totalPages 分页总页面数
     * @param array $list       数据
     */
    public function __construct(Page $page, $list){
        $this->p = $page->nowPage;
        $this->pageSize = $page->listRows;
        $this->totalRows = $page->totalRows;
        $this->totalPages = $page->totalPages;
        $this->list = $list;

    }

}
