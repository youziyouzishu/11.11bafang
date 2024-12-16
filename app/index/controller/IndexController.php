<?php

namespace app\index\controller;

use app\BaseController;
use app\common\service\JsonService;
use think\facade\Request;

class IndexController extends BaseController
{

    /**
     * @notes 主页
     * @param string $name
     * @return \think\response\Json|\think\response\View
     * @author 张晓科
     * @date 2023/10/27 18:12
     */
    public function index($name = '你好,ideaadmin')
    {
        //$template = app()->getRootPath() . 'public/download/index.html';
        // if (Request::isMobile()) {
        //     $template = app()->getRootPath() . 'public/download/index.html';
        // }
        // if (file_exists($template)) {
        //     return view($template);
        // }

        //重定向指定地址
        return redirect('/download/index.html');


        return JsonService::success($name);
    }
}
