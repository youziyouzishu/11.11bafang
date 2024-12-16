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
use app\adminapi\lists\AdminMsgLists;
use app\adminapi\logic\AdminMsgLogic;
use app\adminapi\validate\AdminMsgValidate;
use app\common\service\JsonService;

/**
 * AdminMsg控制器
 * Class AdminMsgController
 * @package app\adminapi\controller
 */
class AdminMsgController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function lists()
    {
        return $this->dataLists(new AdminMsgLists());
    }

    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function notificationList()
    {
        $lists = AdminMsgLists::notificationList();
        return JsonService::success('', $lists);
    }


    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function add()
    {
        $params = (new AdminMsgValidate())->post()->goCheck('add');
        $result = AdminMsgLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(AdminMsgLogic::getError());
    }


    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function edit()
    {
        $params = (new AdminMsgValidate())->post()->goCheck('edit');
        $result = AdminMsgLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(AdminMsgLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function delete()
    {
        $params = (new AdminMsgValidate())->post()->goCheck('delete');
        AdminMsgLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function review()
    {
        $params = (new AdminMsgValidate())->post()->goCheck('review');
        AdminMsgLogic::review($params);
        return $this->success('阅读成功', [], 1, 1);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function detail()
    {
        $params = (new AdminMsgValidate())->goCheck('detail');
        $result = AdminMsgLogic::detail($params);
        return $this->data($result);
    }
}
