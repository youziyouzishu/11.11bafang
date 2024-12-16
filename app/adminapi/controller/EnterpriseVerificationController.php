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
use app\adminapi\lists\EnterpriseVerificationLists;
use app\adminapi\logic\EnterpriseVerificationLogic;
use app\adminapi\validate\EnterpriseVerificationValidate;


/**
 * EnterpriseVerification控制器
 * Class EnterpriseVerificationController
 * @package app\adminapi\controller
 */
class EnterpriseVerificationController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function lists()
    {
        return $this->dataLists(new EnterpriseVerificationLists());
    }


    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function add()
    {
        $params = (new EnterpriseVerificationValidate())->post()->goCheck('add');
        $result = EnterpriseVerificationLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(EnterpriseVerificationLogic::getError());
    }


    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function edit()
    {
        $params = (new EnterpriseVerificationValidate())->post()->goCheck('edit');
        $result = EnterpriseVerificationLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(EnterpriseVerificationLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function delete()
    {
        $params = (new EnterpriseVerificationValidate())->post()->goCheck('delete');
        EnterpriseVerificationLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function detail()
    {
        $params = (new EnterpriseVerificationValidate())->goCheck('detail');
        $result = EnterpriseVerificationLogic::detail($params);
        return $this->data($result);
    }
}
