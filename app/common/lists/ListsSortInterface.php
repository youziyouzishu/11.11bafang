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


namespace app\common\lists;


interface ListsSortInterface
{

    /**
     * @notes 设置支持排序字段
     * @return array
     * @author 令狐冲
     * @date 2021/7/7 19:44
     */
    public function setSortFields(): array;

    /**
     * @notes 设置默认排序条件
     * @return array
     * @author 令狐冲
     * @date 2021/7/16 00:01
     */
    public function setDefaultOrder(): array;
}
