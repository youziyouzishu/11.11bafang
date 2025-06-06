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

namespace app\adminapi\controller\channel;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\channel\OpenSettingLogic;
use app\adminapi\validate\channel\OpenSettingValidate;

/**
 * 微信开放平台
 * Class AppSettingController
 * @package app\adminapi\controller\setting\app
 */
class OpenSettingController extends BaseAdminController
{

    /**
     * @notes 获取微信开放平台设置
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/3/29 11:03
     */
    public function getConfig()
    {
        $result = OpenSettingLogic::getConfig();
        return $this->data($result);
    }


    /**
     * @notes 微信开放平台设置
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/3/29 11:03
     */
    public function setConfig()
    {
        $params = (new OpenSettingValidate())->post()->goCheck();
        OpenSettingLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}
