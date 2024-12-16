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


namespace app\adminapi\controller;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\ProjectTasksAuditLists;
use app\adminapi\logic\ProjectTasksAuditLogic;
use app\adminapi\validate\ProjectTasksAuditValidate;
use app\common\model\ProjectTasksAudit;

/**
 * ProjectTasksAudit控制器
 * Class ProjectTasksAuditController
 * @package app\adminapi\controller
 */
class ProjectTasksAuditController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function lists()
    {
        return $this->dataLists(new ProjectTasksAuditLists());
    }

    public function getNamesByType()
    {
        $params['user_id'] = $this->adminId;
        $result = ProjectTasksAuditLogic::getListNameByType($params);
        return $this->data($result);
    }

    public function getMembers()
    {
        $result = ProjectTasksAuditLogic::getListMembers();
        return $this->data($result);
    }


    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function add()
    {
        $params = (new ProjectTasksAuditValidate())->post()->goCheck('add');
        $result = ProjectTasksAuditLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(ProjectTasksAuditLogic::getError());
    }


    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function edit()
    {
        $params = (new ProjectTasksAuditValidate())->post()->goCheck('edit');
        $result = ProjectTasksAuditLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ProjectTasksAuditLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function delete()
    {
        $params = (new ProjectTasksAuditValidate())->post()->goCheck('delete');
        ProjectTasksAuditLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 审核
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function audited()
    {
        $params = (new ProjectTasksAuditValidate())->post()->goCheck('audited');
        $params['user_id'] = $this->adminId;
        $result = ProjectTasksAuditLogic::audited($params);
        if ($result  === true) {
            return $this->success("审核成功", [], 1, 1);
        }
        if ($result) {
            return $this->data($result);
        }
        return $this->fail(ProjectTasksAuditLogic::getError());
    }


    /**
     * @notes 工作状态变更
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function workAudited()
    {
        $params = (new ProjectTasksAuditValidate())->post()->goCheck('workAudited');

        $result = ProjectTasksAuditLogic::work_audited($params);
        if ($result  === true) {
            return $this->success("变更成功", [], 1, 1);
        }
        if ($result) {
            return $this->data($result);
        }
        return $this->fail(ProjectTasksAuditLogic::getError());
    }


    /**
     * @notes 工作状态变更
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function changeOnsiteUser()
    {
        $params = (new ProjectTasksAuditValidate())->post()->goCheck('changeOnsite');

        $result = ProjectTasksAuditLogic::change_onsite($params);
        if ($result  === true) {
            return $this->success("变更成功", [], 1, 1);
        }
        if ($result) {
            return $this->data($result);
        }
        return $this->fail(ProjectTasksAuditLogic::getError());
    }

    /**
     * @notes 工作状态变更
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function getClients()
    {
        $params = (new ProjectTasksAuditValidate())->post()->goCheck('workAudited');
        $result = ProjectTasksAuditLogic::get_client($params);
        if ($result) {
            return $this->data($result);
        }
        return $this->fail(ProjectTasksAuditLogic::getError());
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function detail()
    {
        $params = (new ProjectTasksAuditValidate())->goCheck('detail');
        $result = ProjectTasksAuditLogic::detail($params);
        return $this->data($result);
    }
}
