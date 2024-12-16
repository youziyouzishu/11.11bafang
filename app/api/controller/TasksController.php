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

use app\adminapi\lists\ProjectDailyReportLists;
use app\adminapi\logic\ProjectDailyReportLogic;
use app\api\lists\project\TasksCollectLists;
use app\api\lists\project\MyTasksCollectLists;
use app\api\lists\project\MyTasksWorkpCollectLists;
use app\api\logic\TasksLogic;
use app\adminapi\logic\ProjectTasksAuditLogic;
use app\adminapi\logic\ProjectTasksLogic;
use app\adminapi\validate\ProjectDailyReportValidate;
use app\adminapi\validate\ProjectTasksAuditValidate;
use app\common\model\ProjectDailyReport;

/**
 * 文章管理
 * Class ArticleController
 * @package app\api\controller
 */
class TasksController extends BaseApiController
{

    public array $notNeedLogin = ['cate'];


    /**
     * @notes 招聘项目
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 15:30
     */
    public function lists()
    {
        return $this->dataLists(new TasksCollectLists());
    }

    /**
     * @notes 招聘项目
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 15:30
     */
    public function mylists()
    {
        return $this->dataLists(new MyTasksCollectLists());
    }

    /**
     * @notes 项目下的员工
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 15:30
     */
    public function myWorkplists()
    {
        return $this->dataLists(new MyTasksWorkpCollectLists());
    }
    //变更工作状态
    public function updateWorkStatus()
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

    //dailyreport 日报批量写入|更新
    public function dailyReportSave()
    {
        $params = (new ProjectDailyReportValidate())->post()->goCheck('dailyReportSave');

        $result = ProjectDailyReportLogic::adds($params);
        if ($result  === true) {
            return $this->success("保存成功", [], 1, 1);
        }
        if ($result) {
            return $this->data($result);
        }
        return $this->fail(ProjectDailyReportLogic::getError());
    }


    //dailyreport 日报批量写入|更新
    public function dailyReportLogs()
    {
        return $this->dataLists(new ProjectDailyReportLists());
    }
    /**
     * @notes 文章分类列表
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 15:30
     */
    public function cate()
    {
        return $this->data(TasksLogic::cate());
    }


    /**
     * @notes 收藏列表
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 16:31
     */
    public function collect()
    {
        return $this->dataLists(new TasksCollectLists());
    }


    /**
     * @notes 文章详情
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 17:09
     */
    public function detail()
    {
        $id = $this->request->get('id/d');
        $result = TasksLogic::detail($id, $this->userId);
        return $this->data($result);
    }


    /**
     * @notes 加入收藏
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 17:01
     */
    public function addCollect()
    {
        $articleId = $this->request->post('id/d');
        TasksLogic::addCollect($articleId, $this->userId);
        return $this->success('操作成功');
    }


    /**
     * @notes 取消收藏
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 17:01
     */
    public function cancelCollect()
    {
        $articleId = $this->request->post('id/d');
        TasksLogic::cancelCollect($articleId, $this->userId);
        return $this->success('操作成功');
    }

    /**
     * @notes 取消收藏
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 17:01
     */
    public function apply()
    {
        $pamars['project_id'] = $this->request->get('id');
        $pamars['user_id'] = $this->userId;
        $pamars['user_info'] = $this->userInfo;
        $result = TasksLogic::audit($pamars);

        if ($result === true) {
            return  $this->success("申请成功");
        }
        return $this->fail(TasksLogic::getError());
    }
}
