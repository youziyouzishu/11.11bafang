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
use app\adminapi\lists\ReviewTableLists;
use app\adminapi\logic\ReviewTableLogic;
use app\adminapi\validate\ReviewTableValidate;


/**
 * 1控制器
 * Class ReviewTableController
 * @package app\adminapi\controller
 */
class ReviewTableController extends BaseAdminController
{


    /**
     * @notes 获取1列表
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function lists()
    {
        return $this->dataLists(new ReviewTableLists());
    }


    /**
     * @notes 添加1
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function add()
    {
        $params = (new ReviewTableValidate())->post()->goCheck('add');
        $result = ReviewTableLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(ReviewTableLogic::getError());
    }


    /**
     * @notes 编辑1
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function edit()
    {
        $params = (new ReviewTableValidate())->post()->goCheck('edit');
        $result = ReviewTableLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ReviewTableLogic::getError());
    }


    /**
     * @notes 删除1
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function delete()
    {
        $params = (new ReviewTableValidate())->post()->goCheck('delete');
        ReviewTableLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取1详情
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function detail()
    {
        $params = (new ReviewTableValidate())->goCheck('detail');
        $result = ReviewTableLogic::detail($params);
        return $this->data($result);
    }
}
