<?php
// +----------------------------------------------------------------------
// | 快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：
// | github下载：

// | imadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: imadmin
// +----------------------------------------------------------------------

namespace app\api\controller;


use app\api\logic\SearchLogic;

/**
 * 搜索
 * Class HotSearchController
 * @package app\api\controller
 */
class SearchController extends BaseApiController
{

    public array $notNeedLogin = ['hotLists'];

    /**
     * @notes 热门搜素
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/22 10:14
     */
    public function hotLists()
    {
        return $this->data(SearchLogic::hotLists());
    }
}
