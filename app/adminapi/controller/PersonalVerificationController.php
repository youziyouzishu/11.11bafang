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
use app\adminapi\lists\PersonalVerificationLists;
use app\adminapi\logic\PersonalVerificationLogic;
use app\adminapi\validate\PersonalVerificationValidate;


/**
 * PersonalVerification控制器
 * Class PersonalVerificationController
 * @package app\adminapi\controller
 */
class PersonalVerificationController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function lists()
    {
        return $this->dataLists(new PersonalVerificationLists());
    }


    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function add()
    {
        $params = (new PersonalVerificationValidate())->post()->goCheck('add');
        $result = PersonalVerificationLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(PersonalVerificationLogic::getError());
    }


    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function edit()
    {
        $params = (new PersonalVerificationValidate())->post()->goCheck('edit');
        $result = PersonalVerificationLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(PersonalVerificationLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function delete()
    {
        $params = (new PersonalVerificationValidate())->post()->goCheck('delete');
        PersonalVerificationLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function detail()
    {
        $params = (new PersonalVerificationValidate())->goCheck('detail');
        $result = PersonalVerificationLogic::detail($params);
        return $this->data($result);
    }
}
