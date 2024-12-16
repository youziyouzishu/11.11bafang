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


namespace app\common\cache;


class WebScanLoginCache extends BaseCache
{

    private $prefix = 'web_scan_';


    /**
     * @notes 获取扫码登录状态标记
     * @param $state
     * @return false|mixed
     * @author 张晓科
     * @date 2023/10/20 18:39
     */
    public function getScanLoginState($state)
    {
        return $this->get($this->prefix . $state);
    }


    /**
     * @notes 设置扫码登录状态
     * @param $state
     * @return false|mixed
     * @author 张晓科
     * @date 2023/10/20 18:31
     */
    public function setScanLoginState($state)
    {
        $this->set($this->prefix . $state, $state, 600);
        return $this->getScanLoginState($state);
    }


    /**
     * @notes 删除缓存
     * @param $token
     * @return bool
     * @author 张晓科
     * @date 2023/9/16 10:13
     */
    public function deleteLoginState($state)
    {
        return $this->delete($this->prefix . $state);
    }
}
