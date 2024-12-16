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

namespace app\adminapi\logic\auth;

use app\common\cache\AdminAuthCache;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use app\common\model\auth\AdminDept;
use app\common\model\auth\AdminJobs;
use app\common\model\auth\AdminRole;
use app\common\model\auth\AdminSession;
use app\common\cache\AdminTokenCache;
use app\common\service\FileService;
use think\facade\Config;
use think\facade\Db;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\AccountLogLogic;
use Exception;

/**
 * 管理员逻辑
 * Class AdminLogic
 * @package app\adminapi\logic\auth
 */
class AdminLogic extends BaseLogic
{

    /**
     * @notes 添加管理员
     * @param array $params
     * @author 张晓科
     * @date 2021/12/29 10:23
     */
    public static function add(array $params)
    {
        Db::startTrans();
        try {
            
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);
            $defaultAvatar = config('project.default_image.admin_avatar');
            $avatar = !empty($params['avatar']) ? FileService::setFileUrl($params['avatar']) : $defaultAvatar;

            $admin = Admin::create([
                "sn" => Admin::createAdminSn($params['role_id'][0] . "_", 24),
                'name' => $params['name'],
                'account' => $params['account'],
                'avatar' => $avatar,
                'password' => $password,
                'create_time' => time(),
                'disable' => $params['disable'],
                'multipoint_login' => $params['multipoint_login'],
            ]);
            // 角色
            self::insertRole($admin['id'], $params['role_id'] ?? []);
            // 部门
            self::insertDept($admin['id'], $params['dept_id'] ?? []);
            // 岗位
            self::insertJobs($admin['id'], $params['jobs_id'] ?? []);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 编辑管理员
     * @param array $params
     * @return bool
     * @author 张晓科
     * @date 2021/12/29 10:43
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            // 基础信息
            $data = [
                'id' => $params['id'],
                'name' => $params['name'],
                'account' => $params['account'],
                'disable' => $params['disable'],
                'multipoint_login' => $params['multipoint_login']
            ];

            // 头像
            $data['avatar'] = !empty($params['avatar']) ? FileService::setFileUrl($params['avatar']) : '';

            // 密码
            if (!empty($params['password'])) {
                $passwordSalt = Config::get('project.unique_identification');
                $data['password'] = create_password($params['password'], $passwordSalt);
            }

            // 禁用或更换角色后.设置token过期
            $roleId = AdminRole::where('admin_id', $params['id'])->column('role_id');
            $editRole = false;
            if (!empty(array_diff_assoc($roleId, $params['role_id']))) {
                $editRole = true;
            }

            if ($params['disable'] == 1 || $editRole) {
                $tokenArr = AdminSession::where('admin_id', $params['id'])->select()->toArray();
                foreach ($tokenArr as $token) {
                    self::expireToken($token['token']);
                }
            }

            Admin::update($data);
            (new AdminAuthCache($params['id']))->clearAuthCache();

            // 删除旧的关联信息
            AdminRole::delByUserId($params['id']);
            AdminDept::delByUserId($params['id']);
            AdminJobs::delByUserId($params['id']);
            // 角色
            self::insertRole($params['id'], $params['role_id']);
            // 部门
            self::insertDept($params['id'], $params['dept_id'] ?? []);
            // 岗位
            self::insertJobs($params['id'], $params['jobs_id'] ?? []);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 删除管理员
     * @param array $params
     * @return bool
     * @author 张晓科
     * @date 2021/12/29 10:45
     */
    public static function delete(array $params): bool
    {
        Db::startTrans();
        try {
            $admin = Admin::findOrEmpty($params['id']);
            if ($admin->root == YesNoEnum::YES) {
                throw new \Exception("超级管理员不允许被删除");
            }
            Admin::destroy($params['id']);

            //设置token过期
            $tokenArr = AdminSession::where('admin_id', $params['id'])->select()->toArray();
            foreach ($tokenArr as $token) {
                self::expireToken($token['token']);
            }
            (new AdminAuthCache($params['id']))->clearAuthCache();

            // 删除旧的关联信息
            AdminRole::delByUserId($params['id']);
            AdminDept::delByUserId($params['id']);
            AdminJobs::delByUserId($params['id']);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 过期token
     * @param $token
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 张晓科
     * @date 2021/12/29 10:46
     */
    public static function expireToken($token): bool
    {
        $adminSession = AdminSession::where('token', '=', $token)
            ->with('admin')
            ->find();

        if (empty($adminSession)) {
            return false;
        }

        $time = time();
        $adminSession->expire_time = $time;
        $adminSession->update_time = $time;
        $adminSession->save();

        return (new AdminTokenCache())->deleteAdminInfo($token);
    }


    /**
     * @notes 查看管理员详情
     * @param $params
     * @return array
     * @author 张晓科
     * @date 2021/12/29 11:07
     */
    public static function detail($params, $action = 'detail'): array
    {
        // 使用 with 方法预加载关联数据
        $admin = Admin::with(['enterpriseVerification'])
            ->field([
                'id',
                'account',
                'name',
                'disable',
                'root',
                'company_info',
                'company_img',
                'multipoint_login',
                'avatar',
                'ld_licence_img',
                'hr_licence_img',
                'sn',
                'im_user_sign',
                'user_money'
            ])
            ->findOrEmpty($params['id'])
            ->toArray();

        if ($action == 'detail') {
            return $admin;
        }



        // 当前管理员角色拥有的菜单
        $result['menu'] = MenuLogic::getMenuByAdminId($params['id']);

        // 当前管理员角色拥有的按钮权限
        $result['permissions'] = AuthLogic::getBtnAuthByRoleId($admin);

        // // 添加企业实名认证信息
        // if (empty($admin['enterprise_verification'])) {
        //     $$admin['enterprise_verification'] = null;
        // }
        $result['user'] = $admin;
        return $result;
    }


    /**
     * @notes 编辑超级管理员
     * @param $params
     * @return Admin
     * @author 张晓科
     * @date 2023/4/8 17:54
     */
    public static function editSelf($params)
    {
        $data = [
            'id' => $params['admin_id'],
            'name' => $params['name'],
            'avatar' => FileService::setFileUrl($params['avatar']),
            'company_name' => $params['company_name'] ?? '',
            'company_card' => $params['company_card'] ?? '',
            'company_info' => $params['company_info'] ?? '',
            'company_img' => FileService::setFileUrl($params['company_img']) ?? '',
            'ld_licence_img' => FileService::setFileUrl($params['ld_licence_img']) ?? '',
            'hr_licence_img' => FileService::setFileUrl($params['hr_licence_img']) ?? '',

            'bank_name' => $params['bank_name'] ?? '',
            'bank_card' => $params['bank_card'] ?? '',
        ];
        if (!empty($params['password'])) {
            $passwordSalt = Config::get('project.unique_identification');
            $data['password'] = create_password($params['password'], $passwordSalt);
        }

        return Admin::update($data);
    }


    /**
     * @notes 新增角色
     * @param $adminId
     * @param $roleIds
     * @throws \Exception
     * @author 张晓科
     * @date 2023/11/25 14:23
     */
    public static function insertRole($adminId, $roleIds)
    {
        if (!empty($roleIds)) {
            // 角色
            $roleData = [];
            foreach ($roleIds as $roleId) {
                $roleData[] = [
                    'admin_id' => $adminId,
                    'role_id' => $roleId,
                ];
            }
            (new AdminRole())->saveAll($roleData);
        }
    }


    /**
     * @notes 新增部门
     * @param $adminId
     * @param $deptIds
     * @throws \Exception
     * @author 张晓科
     * @date 2023/11/25 14:22
     */
    public static function insertDept($adminId, $deptIds)
    {
        // 部门
        if (!empty($deptIds)) {
            $deptData = [];
            foreach ($deptIds as $deptId) {
                $deptData[] = [
                    'admin_id' => $adminId,
                    'dept_id' => $deptId
                ];
            }
            (new AdminDept())->saveAll($deptData);
        }
    }


    /**
     * @notes 新增岗位
     * @param $adminId
     * @param $jobsIds
     * @throws \Exception
     * @author 张晓科
     * @date 2023/11/25 14:22
     */
    public static function insertJobs($adminId, $jobsIds)
    {
        // 岗位
        if (!empty($jobsIds)) {
            $jobsData = [];
            foreach ($jobsIds as $jobsId) {
                $jobsData[] = [
                    'admin_id' => $adminId,
                    'jobs_id' => $jobsId
                ];
            }
            (new AdminJobs())->saveAll($jobsData);
        }
    }


    /**
     * @notes 调整用户余额
     * @param array $params
     * @return bool|string
     * @author 张晓科
     * @date 2023/2/23 14:25
     */
    public static function adjustUserMoney(array $params)
    {
        Db::startTrans();
        try {
            $user = Admin::find($params['user_id']);
            if (AccountLogEnum::INC == $params['action']) {
                //调整可用余额
                $user->user_money += $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_INC_ADMIN,
                    AccountLogEnum::INC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            } else {
                $user->user_money -= $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_DEC_ADMIN,
                    AccountLogEnum::DEC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }


    /**
     * @notes 项目计费
     * @param array $params
     * @return bool|string
     * @author 张晓科
     * @date 2023/2/23 14:25
     */
    public static function ProjectUserMoney(array $params)
    {
        Db::startTrans();
        try {
            $user = Admin::find($params['user_id']);
            if (AccountLogEnum::INC == $params['action']) {
                //调整可用余额
                $user->user_money += $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_INC_PROJECT,
                    AccountLogEnum::INC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            } else {
                $user->user_money -= $params['num'];
                if ($user->user_money < 0) {
                    throw new Exception("余额不足，请充值");
                }
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_DEC_PROJECT,
                    AccountLogEnum::DEC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 合同计费
     * @param array $params
     * @return bool|string
     * @author 张晓科
     * @date 2023/2/23 14:25
     */
    public static function ContractUserMoney(array $params)
    {
        Db::startTrans();
        try {
            $user = Admin::find($params['user_id']);
            if (AccountLogEnum::INC == $params['action']) {
                //调整可用余额
                $user->user_money += $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_INC_CONTRACT,
                    AccountLogEnum::INC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            } else {
                $user->user_money -= $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_DEC_CONTRACT,
                    AccountLogEnum::DEC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}
