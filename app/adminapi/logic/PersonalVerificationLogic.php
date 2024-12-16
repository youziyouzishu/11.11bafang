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


use app\common\model\PersonalVerification;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * PersonalVerification逻辑
 * Class PersonalVerificationLogic
 * @package app\adminapi\logic
 */
class PersonalVerificationLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            PersonalVerification::create([
                'user_id' => $params['user_id'],
                'authorize_user_info' => $params['authorize_user_info'] ?? 0,
                'realname_status' => $params['realname_status'] ?? 0,
                'psn_id' => $params['psn_id'] ?? '',
                'account_mobile' => $params['account_mobile'] ?? '',
                'account_email' => $params['account_email'] ?? '',
                'psn_name' => $params['psn_name'],
                'psn_nationality' => $params['psn_nationality'] ?? '',
                'psn_id_card_num' => $params['psn_id_card_num'],
                'psn_id_card_type' => $params['psn_id_card_type'] ?? 'CRED_PSN_CH_IDCARD',
                'bank_card_num' => $params['bank_card_num'] ?? '',
                'psn_mobile' => $params['psn_mobile'] ?? '',
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 编辑
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            PersonalVerification::where('id', $params['id'])->update([
                'user_id' => $params['user_id'],
                'authorize_user_info' => $params['authorize_user_info'],
                'realname_status' => $params['realname_status'],
                'psn_id' => $params['psn_id'],
                'account_mobile' => $params['account_mobile'],
                'account_email' => $params['account_email'],
                'psn_name' => $params['psn_name'],
                'psn_nationality' => $params['psn_nationality'],
                'psn_id_card_num' => $params['psn_id_card_num'],
                'psn_id_card_type' => $params['psn_id_card_type'],
                'bank_card_num' => $params['bank_card_num'],
                'psn_mobile' => $params['psn_mobile'],
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function addOrEdit(array $params): bool
    {
        // 查找是否已经存在记录，假设以 user_id 为唯一标识符
        $record = PersonalVerification::where('user_id', $params['user_id'])->find();
        if ($record) {
            return PersonalVerification::where('user_id', $params['user_id'])->update([
                'user_id' => $params['user_id'],
                'psn_name' => $params['psn_name'],
                'psn_id_card_num' => $params['psn_id_card_num'],
            ]);
        }
        return self::add($params);
    }
    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public static function delete(array $params): bool
    {
        return PersonalVerification::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public static function detail($params): array
    {
        return PersonalVerification::findOrEmpty($params['id'])->toArray();
    }
}
