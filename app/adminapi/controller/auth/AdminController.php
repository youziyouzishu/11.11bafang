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

namespace app\adminapi\controller\auth;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\auth\AdminLists;
use app\adminapi\validate\auth\AdminValidate;
use app\adminapi\logic\auth\AdminLogic;
use app\adminapi\logic\EnterpriseVerificationLogic;
use app\adminapi\validate\auth\editSelfValidate;
use app\adminapi\validate\EnterpriseVerificationValidate;
use app\common\model\EnterpriseVerification;
use app\common\http\esign\OrganizeAuth;
use app\adminapi\logic\EContractLogic;
use app\adminapi\validate\auth\AdjustAdminMoney;

/**
 * 管理员控制器
 * Class AdminController
 * @package app\adminapi\controller\auth
 */
class AdminController extends BaseAdminController
{

    /**
     * @notes 查看管理员列表
     * @return \think\response\Json
     * @author 张晓科
     * @date 2021/12/29 9:55
     */
    public function lists()
    {
        return $this->dataLists(new AdminLists());
    }


    /**
     * @notes 添加管理员
     * @return \think\response\Json
     * @author 张晓科
     * @date 2021/12/29 10:21
     */
    public function add()
    {
        $params = (new AdminValidate())->post()->goCheck('add');
        $result = AdminLogic::add($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }


    /**
     * @notes 编辑管理员
     * @return \think\response\Json
     * @author 张晓科
     * @date 2021/12/29 11:03
     */
    public function edit()
    {
        $params = (new AdminValidate())->post()->goCheck('edit');
        $result = AdminLogic::edit($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }

    /**
     * @notes 签署合同
     * @return \think\response\Json
     * @author 张晓科
     * @date 2021/12/29 11:03
     */
    public function auth()
    {
        $params = $this->request->post()['enterpriseVerification'];

        $params['user_id'] = $this->adminId;
        $result = EnterpriseVerificationLogic::addOrEdit($params);
        if (true === $result) {
            if (empty($params['org_id']) || intval($params['realname_status']) < 4) {
                $sdk =  new OrganizeAuth();
                $result = $sdk->auth($this->adminId, 2, $params['org_name'], $params['org_id_card_num']);
                if ($result['status'] == 2 || $result['status'] == 3) {
                    EnterpriseVerification::where('user_id', $this->adminId)->update([
                        "auth_url" => $result['response']['data']['authShortUrl'] ?? '',
                        "auth_flow_id" => $result['response']['data']['authFlowId'] ?? '',
                        "realname_status" => $result['status']
                    ]);
                }
                return $this->data($result);
            }
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }

    public function signContract()
    {
        $params['user_id'] = $this->adminId;
        $result =  EContractLogic::add($params);
        if ($result !== false) {
            return  $this->data($result);
        }
        return $this->fail(EContractLogic::getError());
    }

    /**
     * @notes 删除管理员
     * @return \think\response\Json
     * @author 张晓科
     * @date 2021/12/29 11:03
     */
    public function delete()
    {
        $params = (new AdminValidate())->post()->goCheck('delete');
        $result = AdminLogic::delete($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }


    /**
     * @notes 查看管理员详情
     * @return \think\response\Json
     * @author 张晓科
     * @date 2021/12/29 11:07
     */
    public function detail()
    {
        $params = (new AdminValidate())->goCheck('detail');
        $result = AdminLogic::detail($params);
        return $this->data($result);
    }


    /**
     * @notes 获取当前管理员信息
     * @return \think\response\Json
     * @author 张晓科
     * @date 2021/12/31 10:53
     */
    public function mySelf()
    {
        $result = AdminLogic::detail(['id' => $this->adminId], 'auth');
        return $this->data($result);
    }

    /**
     * @notes 获取当前管理员信息
     * @return \think\response\Json
     * @author 张晓科
     * @date 2021/12/31 10:53
     */
    public function getBalance()
    {
        $result = AdminLogic::detail(['id' => $this->adminId]);
        return $this->data($result);
    }

    /**
     * @notes 编辑超级管理员信息
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/4/8 17:54
     */
    public function editSelf()
    {
        $params = (new editSelfValidate())->post()->goCheck('', ['admin_id' => $this->adminId]);
        $result = AdminLogic::editSelf($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 调整用户余额
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/2/23 14:33
     */
    public function adjustMoney()
    {
        $params = (new AdjustAdminMoney())->post()->goCheck();
        $res = AdminLogic::adjustUserMoney($params);
        if (true === $res) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail($res);
    }
}
