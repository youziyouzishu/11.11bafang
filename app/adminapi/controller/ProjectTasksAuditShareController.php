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
use app\adminapi\lists\ProjectTasksAuditShareLists;
use app\adminapi\logic\ProjectTasksAuditShareLogic;
use app\adminapi\validate\ProjectTasksAuditShareValidate;


/**
 * ProjectTasksAuditShare控制器
 * Class ProjectTasksAuditShareController
 * @package app\adminapi\controller
 */
class ProjectTasksAuditShareController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function lists()
    {
        return $this->dataLists(new ProjectTasksAuditShareLists());
    }


    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function add()
    {
        $params = (new ProjectTasksAuditShareValidate())->post()->goCheck('add');
        $result = ProjectTasksAuditShareLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(ProjectTasksAuditShareLogic::getError());
    }

    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function getOrAdd()
    {
        $params = (new ProjectTasksAuditShareValidate())->post()->goCheck('getOrAdd');
        $params['user_id'] = $this->adminId;
        $result = ProjectTasksAuditShareLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(ProjectTasksAuditShareLogic::getError());
    }

    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function edit()
    {
        $params = (new ProjectTasksAuditShareValidate())->post()->goCheck('edit');
        $result = ProjectTasksAuditShareLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ProjectTasksAuditShareLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function delete()
    {
        $params = (new ProjectTasksAuditShareValidate())->post()->goCheck('delete');
        ProjectTasksAuditShareLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function detail()
    {
        $params = (new ProjectTasksAuditShareValidate())->goCheck('detail');
        $result = ProjectTasksAuditShareLogic::detail($params);
        return $this->data($result);
    }
}
