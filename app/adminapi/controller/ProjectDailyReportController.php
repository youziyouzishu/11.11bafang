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
use app\adminapi\lists\ProjectDailyReportLists;
use app\adminapi\logic\ProjectDailyReportLogic;
use app\adminapi\validate\ProjectDailyReportValidate;


/**
 * ProjectDailyReport控制器
 * Class ProjectDailyReportController
 * @package app\adminapi\controller
 */
class ProjectDailyReportController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function lists()
    {
        return $this->dataLists(new ProjectDailyReportLists());
    }


    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function add()
    {
        $params = (new ProjectDailyReportValidate())->post()->goCheck('add');
        $result = ProjectDailyReportLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(ProjectDailyReportLogic::getError());
    }


    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function edit()
    {
        $params = (new ProjectDailyReportValidate())->post()->goCheck('edit');
        $result = ProjectDailyReportLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ProjectDailyReportLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function delete()
    {
        $params = (new ProjectDailyReportValidate())->post()->goCheck('delete');
        ProjectDailyReportLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function detail()
    {
        $params = (new ProjectDailyReportValidate())->goCheck('detail');
        $result = ProjectDailyReportLogic::detail($params);
        return $this->data($result);
    }
}
