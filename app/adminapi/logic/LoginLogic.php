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

namespace app\adminapi\logic;

use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use app\adminapi\service\AdminTokenService;
use app\common\http\tim\IMAccountImporter;
use app\common\http\tim\TencentUserSign;
use app\common\model\auth\AdminRole;
use app\common\model\EnterpriseVerification;
use app\common\service\FileService;
use think\facade\Config;

/**
 * 登录逻辑
 * Class LoginLogic
 * @package app\adminapi\logic
 */
class LoginLogic extends BaseLogic
{
    /**
     * @notes 管理员账号登录
     * @param $params
     * @return false|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 令狐冲
     * @date 2021/6/30 17:00
     */
    public function login($params)
    {
        $time = time();
        $admin = Admin::where('account', '=', $params['account'])->find();

        if (empty($admin->sn)) {
            $role = AdminRole::where(['admin_id' => $admin->id])->find();
            $admin->sn = Admin::createAdminSn("{$role->role_id}_", 24);
            $orginfo = EnterpriseVerification::where(['user_id' => $admin->id])->find();
            $nickname = "";
            if (!empty($orginfo->org_name)) {
                $nickname = $orginfo->org_name;
            }
            IMAccountImporter::AddOne("hr_{$admin->sn}", $nickname);
        }
        $orginfo = EnterpriseVerification::where(['user_id' => $admin->id])->find();
        $nickname = "";
        if (!empty($orginfo->org_name)) {
            $nickname = $orginfo->org_name;
        }
        IMAccountImporter::AddOne("hr_{$admin->sn}", $nickname);
        $admin->im_user_sign =  TencentUserSign::Computer("hr_{$admin->sn}");
        //生成IM登陆签名
        if ($admin->im_user_sign == "") {
            $admin->im_user_sign =  TencentUserSign::Computer("hr_{$admin->sn}");
        }
        //用户表登录信息更新
        $admin->login_time = $time;
        $admin->login_ip = request()->ip();
        $admin->save();

        //设置token
        $adminInfo = AdminTokenService::setToken($admin->id, $params['terminal'], $admin->multipoint_login);

        //返回登录信息
        $avatar = $admin->avatar ? $admin->avatar : Config::get('project.default_image.admin_avatar');
        $avatar = FileService::getFileUrl($avatar);

        return [
            'name' => $adminInfo['name'],
            'avatar' => $avatar,
            'role_name' => $adminInfo['role_name'],
            'token' => $adminInfo['token'],
        ];
    }


    /**
     * @notes 退出登录
     * @param $adminInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 令狐冲
     * @date 2021/7/5 14:34
     */
    public function logout($adminInfo)
    {
        //token不存在，不注销
        if (!isset($adminInfo['token'])) {
            return false;
        }
        //设置token过期
        return AdminTokenService::expireToken($adminInfo['token']);
    }
}
