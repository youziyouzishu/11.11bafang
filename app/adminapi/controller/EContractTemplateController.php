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
use app\adminapi\lists\EContractTemplateLists;
use app\adminapi\logic\EContractTemplateLogic;
use app\adminapi\validate\EContractTemplateValidate;


/**
 * EContractTemplate控制器
 * Class EContractTemplateController
 * @package app\adminapi\controller
 */
class EContractTemplateController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public function lists()
    {
        return $this->dataLists(new EContractTemplateLists());
    }


    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public function add()
    {
        $params = (new EContractTemplateValidate())->post()->goCheck('add');
        $result = EContractTemplateLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(EContractTemplateLogic::getError());
    }


    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public function edit()
    {
        $params = (new EContractTemplateValidate())->post()->goCheck('edit');
        $result = EContractTemplateLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(EContractTemplateLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public function delete()
    {
        $params = (new EContractTemplateValidate())->post()->goCheck('delete');
        EContractTemplateLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public function detail()
    {
        $params = (new EContractTemplateValidate())->goCheck('detail');
        $result = EContractTemplateLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public function templateEditUrl()
    {
        $params = (new EContractTemplateValidate())->goCheck('detail');
        $result = EContractTemplateLogic::getTemplateEditUrl($params);
        return $this->data($result);
    }
}
