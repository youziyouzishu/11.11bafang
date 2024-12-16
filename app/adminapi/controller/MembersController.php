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
use app\adminapi\lists\MemberAllLists;
use app\common\model\auth\Admin;
use app\common\model\user\User;
use app\common\http\esign\Config;

/**
 * ProjectTasksAudit控制器
 * Class ProjectTasksAuditController
 * @package app\adminapi\controller
 */
class MembersController extends BaseAdminController
{
    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function lists()
    {
        return $this->dataLists(new MemberAllLists());
    }

    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function toim()
    {
        $tmp = request()->post();
        $id = intval($tmp['id']);
        $user = User::findOrEmpty($id);

        // 获取管理员记录
        $admin = Admin::findOrEmpty($this->adminId); // 假设是ID为1的管理员

        if (!$admin) {
            return $this->fail('管理员记录不存在');
        }


        // 检查im_list中是否已经存在当前用户的sn
        if ($admin->im_list) {
            $imList = explode(',', $admin->im_list);
            if (!in_array($user['sn'], $imList)) {
                $imList[] = $user['sn'];
                if (count($imList) > 30) {
                    return $this->fail('每日可主动发起沟通超过30次，已达到上限');
                }
                $admin->im_list = trim(implode(',', $imList), ",");
                $admin->save();
            }
        } else {
            $admin->im_list = $user['sn'];
            $admin->save();
        }


        $protocol = request()->scheme();
        $host = $_SERVER['HTTP_HOST'];
        $url = $protocol . '://' . $host . '/business/im?conversationId=C2Ccl_' . $user['sn'];
        $data = ['url' => $url];
        return $this->data($data);
    }
}
