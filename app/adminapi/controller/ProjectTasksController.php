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
use app\adminapi\lists\ProjectTasksLists;
use app\adminapi\logic\ProjectTasksLogic;
use app\adminapi\validate\ProjectTasksValidate;


/**
 * 项目控制器
 * Class ProjectTasksController
 * @package app\adminapi\controller
 */
class ProjectTasksController extends BaseAdminController
{


    /**
     * @notes 获取项目列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function lists()
    {
        return $this->dataLists(new ProjectTasksLists());
    }


    /**
     * @notes 添加项目
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function add()
    {
        $params = (new ProjectTasksValidate())->post()->goCheck('add');
        $params['creator'] = $this->adminId;
        $result = ProjectTasksLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(ProjectTasksLogic::getError());
    }


    /**
     * @notes 编辑项目
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function edit()
    {
        $params = (new ProjectTasksValidate())->post()->goCheck('edit');
        $result = ProjectTasksLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ProjectTasksLogic::getError());
    }


    /**
     * @notes 删除项目
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function delete()
    {
        $params = (new ProjectTasksValidate())->post()->goCheck('delete');
        ProjectTasksLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 关闭项目
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function close()
    {
        $params = (new ProjectTasksValidate())->post()->goCheck('delete');
        ProjectTasksLogic::close($params);
        return $this->success('关闭成功', [], 1, 1);
    }

    /**
     * @notes 审核项目
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function audit()
    {
        $params = (new ProjectTasksValidate())->post()->goCheck('audit');
        $result = ProjectTasksLogic::audit($params);
        if (true === $result) {
            return $this->success('审核成功', [], 1, 1);
        }
        return $this->fail(ProjectTasksLogic::getError());
    }


    /**
     * @notes 获取项目详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function detail()
    {
        $params = (new ProjectTasksValidate())->goCheck('detail');
        $result = ProjectTasksLogic::detail($params);
        return $this->data($result);
    }
}
