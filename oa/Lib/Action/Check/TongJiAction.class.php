<?php
/**
 * Created by PhpStorm.
 * User: yqing
 * Date: 2018/8/25
 * Time: 16:14
 */

class TongJiAction extends loginAction{


    function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->display();
    }
}