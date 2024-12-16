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

namespace app\api\logic;


use app\common\logic\BaseLogic;
use app\common\model\HotSearch;
use app\common\service\ConfigService;

/**
 * 搜索逻辑
 * Class SearchLogic
 * @package app\api\logic
 */
class SearchLogic extends BaseLogic
{

    /**
     * @notes 热搜列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 张晓科
     * @date 2023/9/23 14:34
     */
    public static function hotLists()
    {
        $data = HotSearch::field(['name', 'sort'])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();

        return [
            // 功能状态 0-关闭 1-开启
            'status' => ConfigService::get('hot_search', 'status', 0),
            // 热门搜索数据
            'data' => $data,
        ];
    }
}
