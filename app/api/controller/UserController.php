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

use app\adminapi\logic\PersonalVerificationLogic;
use app\common\model\PersonalVerification;
use app\api\logic\UserLogic;
use app\api\validate\PasswordValidate;
use app\api\validate\SetUserInfoValidate;
use app\api\validate\UserValidate;
use app\common\http\esign\OrganizeAuth;
use app\common\cache\UserTokenCache;

/**
 * 用户控制器
 * Class UserController
 * @package app\api\controller
 */
class UserController extends BaseApiController
{
    public array $notNeedLogin = ['resetPassword'];


    /**
     * @notes 获取个人中心
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 张晓科
     * @date 2023/9/16 18:19
     */
    public function center()
    {
        $data = UserLogic::center($this->userInfo);
        return $this->success('', $data);
    }


    /**
     * @notes 获取个人信息
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 19:46
     */
    public function info()
    {
        $result = UserLogic::info($this->userId);
        return $this->data($result);
    }


    /**
     * @notes 重置密码
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/16 18:06
     */
    public function resetPassword()
    {
        $params = (new PasswordValidate())->post()->goCheck('resetPassword');
        $result = UserLogic::resetPassword($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }


    /**
     * @notes 修改密码
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 19:16
     */
    public function changePassword()
    {
        $params = (new PasswordValidate())->post()->goCheck('changePassword');
        $result = UserLogic::changePassword($params, $this->userId);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }


    /**
     * @notes 获取小程序手机号
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/21 16:46
     */
    public function getMobileByMnp()
    {
        $params = (new UserValidate())->post()->goCheck('getMobileByMnp');
        $params['user_id'] = $this->userId;
        $result = UserLogic::getMobileByMnp($params);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }


    /**
     * @notes 编辑用户信息
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/21 17:01
     */
    public function setInfo()
    {
        $params = (new SetUserInfoValidate())->post()->goCheck(null, ['id' => $this->userId]);
        $result = UserLogic::setInfo($this->userId, $params);
        if (false === $result) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }


    /**
     * @notes 绑定/变更 手机号
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/21 17:29
     */
    public function bindMobile()
    {
        $params = (new UserValidate())->post()->goCheck('bindMobile');
        $params['user_id'] = $this->userId;
        $result = UserLogic::bindMobile($params);
        if ($result) {
            return $this->success('绑定成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }

    /**
     * @notes 绑定/变更 手机号
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/21 17:29
     */
    public function bindRole()
    {
        $params = (new UserValidate())->post()->goCheck('bindRole');
        if ($params['role'] == NULL) {
            return $this->fail("请先选择身份");
        }

        $params['flow_status'] = 1;
        $params['id'] = $this->userId;
        $result = UserLogic::SaveInfo($params);

        if ($result) {
            $token = $this->request->header('token');
            (new UserTokenCache())->setUserInfo($token);
            return $this->success('设置身份成功', [], 1, 1);
        }

        return $this->fail(UserLogic::getError());
    }

    /**
     * @notes 实名认证
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/21 17:29
     */
    public function realnameVerify()
    {
        $params = (new UserValidate())->post()->goCheck('realnameVerify');
        //$flowInfo = OrganizeAuth::queryAuthFlow("OF-306fc2d233080009");

        $params2['user_id'] = $this->userId;
        $params2['psn_name'] = $params['real_name'];
        $params2['psn_id_card_num'] =  $params['id_card'];
        PersonalVerificationLogic::addOrEdit($params2);

        //if (empty($params2['psn_id_card_num']) || (isset($params2['realname_status']) && intval($params2['realname_status']) < 4)) {
        if (1 == 1) {
            $sdk =  new OrganizeAuth();
            $result = $sdk->personsAuth($this->userId, 2,  $params2['psn_name'], $params2['psn_id_card_num']);
            if ($result['status'] == 4) {
                $params['flow_status'] = 2;
                $params['id'] = $this->userId;
                UserLogic::SaveInfo($params);
            }
            if ($result['status'] == 2 || $result['status'] == 3) {

                PersonalVerification::where('user_id', $this->userId)->update([
                    "auth_url" => $result['response']['data']['authShortUrl'] ?? '',
                    "auth_flow_id" => $result['response']['data']['authFlowId'] ?? '',
                    "realname_status" => $result['status']
                ]);
            }
            return $this->data($result);
        }

        return $this->fail(UserLogic::getError());
    }
    /**
     * @notes 绑定/变更 手机号
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/21 17:29
     */
    public function saveDetail()
    {
        $params = (new UserValidate())->post()->goCheck('saveDetail');
        $params['id'] = $this->userId;
        $params['flow_status'] = 3;
        $result = UserLogic::SaveInfo($params);
        if ($result) {
            return $this->success('保存成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }
}
