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
use app\adminapi\lists\ESealsLists;
use app\adminapi\logic\ESealsLogic;
use app\adminapi\validate\ESealsValidate;


/**
 * ESeals控制器
 * Class ESealsController
 * @package app\adminapi\controller
 */
class ESealsController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function lists()
    {
        return $this->dataLists(new ESealsLists());
    }


    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function add()
    {
        $params = (new ESealsValidate())->post()->goCheck('add');
        $result = ESealsLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(ESealsLogic::getError());
    }


    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function edit()
    {
        $params = (new ESealsValidate())->post()->goCheck('edit');
        $result = ESealsLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ESealsLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function delete()
    {
        $params = (new ESealsValidate())->post()->goCheck('delete');
        ESealsLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function detail()
    {
        $params = (new ESealsValidate())->goCheck('detail');
        $result = ESealsLogic::detail($params);
        return $this->data($result);
    }
}
