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


use app\api\controller\BaseApiController;
use app\adminapi\lists\ProjectTasksAuditShareLists;
use app\adminapi\logic\ProjectTasksAuditShareLogic;
use app\adminapi\validate\ProjectTasksAuditShareValidate;


/**
 * 项目管理
 * TasksManageController控制器
 * Class ProjectTasksAuditShareController
 * @package app\adminapi\controller
 */
class TasksManageController extends BaseApiController
{
    /**
     * @notes 获取分享链接
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function getOrAddShare()
    {
        $templae = "复制的口令，到八方人力平台，即可完成入职申请： \${kouling}￥ 快速加入我们，共创辉煌未来！";
        $params = (new ProjectTasksAuditShareValidate())->post()->goCheck('getOrAdd');
        $params['user_id'] = $this->userId;
        $params['type'] = 'ZHAOPING';
        $result = ProjectTasksAuditShareLogic::getOrAdd($params);
        if (false === $result) {
            return $this->fail(ProjectTasksAuditShareLogic::getError());
        }
        return $this->data(['copy' => str_replace("{kouling}", $result, $templae)]);
    }

    /**
     * @notes 检查UNIKEY 是否有效
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function checkUnikey()
    {

        $params = (new ProjectTasksAuditShareValidate())->post()->goCheck('checkUnikey');
        $params['user_id'] = $this->userId;
        $params['type'] = 'ZHAOPING';
        $result = ProjectTasksAuditShareLogic::check($params);
        if (false === $result) {
            return $this->fail(ProjectTasksAuditShareLogic::getError());
        }
        if (true === $result) {
            return $this->success("申请成功，等待公司邀请合同签署");
        }
        return $this->data($result);
    }
}
