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
use app\adminapi\lists\EContractLists;
use app\adminapi\logic\EContractLogic;
use app\adminapi\validate\EContractValidate;
use app\common\http\esign\OrganizeAuth;

/**
 * EContract控制器
 * Class EContractController
 * @package app\adminapi\controller
 */
class EContractController extends BaseAdminController
{


    /**
     * @notes 获取列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function lists()
    {
        return $this->dataLists(new EContractLists());
    }


    /**
     * @notes 签署平台合同
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function signContract()
    {
        $params['user_id'] = $this->adminId;
        $params['to_user_id'] = 1;

        $params = (new EContractValidate())->post()->goCheck('add');
        $result = EContractLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(EContractLogic::getError());
    }
    /**
     * @notes 获取签署URL
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function signContractUrl()
    {
        $params = (new EContractValidate())->post()->goCheck('detail');
        $result = EContractLogic::getSignContractUrl($params);
        return $this->data($result);
    }

    /**
     * @notes 下载签署后合同
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function downloadContractUrl()
    {
        $params = (new EContractValidate())->post()->goCheck('detail');
        $result = EContractLogic::getDownloadContractUrl($params);
        return $this->data($result);
    }

    /**
     * @notes 添加
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function add()
    {
        $params = (new EContractValidate())->post()->goCheck('add');
        $result = EContractLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(EContractLogic::getError());
    }


    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function edit()
    {
        $params = (new EContractValidate())->post()->goCheck('edit');
        $result = EContractLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(EContractLogic::getError());
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function delete()
    {
        $params = (new EContractValidate())->post()->goCheck('delete');
        EContractLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function detail()
    {
        $params = (new EContractValidate())->goCheck('detail');
        $result = EContractLogic::detail($params);
        return $this->data($result);
    }
}
