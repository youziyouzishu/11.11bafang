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


use app\common\model\EnterpriseVerification;
use app\common\logic\BaseLogic;
use Exception;
use think\facade\Db;


/**
 * EnterpriseVerification逻辑
 * Class EnterpriseVerificationLogic
 * @package app\adminapi\logic
 */
class EnterpriseVerificationLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            EnterpriseVerification::create([
                'user_id' => $params['user_id'],
                'org_id' => $params['org_id'],
                'org_name' => $params['org_name'],
                'org_type' => $params['org_type'],
                'org_id_card_num' => $params['org_id_card_num'],
                'org_id_card_type' => $params['org_id_card_type'],
                'legal_rep_name' => $params['legal_rep_name'],
                'legal_rep_id_card_num' => $params['legal_rep_id_card_num'],
                'legal_rep_id_card_type' => $params['legal_rep_id_card_type'],
                'admin_name' => $params['admin_name'],
                'admin_account' => $params['admin_account'],
                'authorize_user_info' => $params['authorize_user_info'],
                'realname_status' => $params['realname_status']
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
     * @date 2024/06/04 12:01
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            EnterpriseVerification::where('id', $params['id'])->update([
                'user_id' => $params['user_id'],
                'org_id' => $params['org_id'],
                'org_name' => $params['org_name'],
                'org_type' => $params['org_type'],
                'org_id_card_num' => $params['org_id_card_num'],
                'org_id_card_type' => $params['org_id_card_type'],
                'legal_rep_name' => $params['legal_rep_name'],
                'legal_rep_id_card_num' => $params['legal_rep_id_card_num'],
                'legal_rep_id_card_type' => $params['legal_rep_id_card_type'],
                'admin_name' => $params['admin_name'],
                'admin_account' => $params['admin_account'],
                'authorize_user_info' => $params['authorize_user_info'],
                'realname_status' => $params['realname_status']
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
     * @notes 添加或编辑
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    public static function addOrEdit(array $params): bool
    {
        Db::startTrans();
        try {
            // 查找是否已经存在记录，假设以 user_id 为唯一标识符
            $record = EnterpriseVerification::where('user_id', $params['user_id'])->find();


            if ($record) {
                if ($record->realname_status > 0) {
                    throw new \Exception("正在实名中，不可重复创建实名订单");
                }
                EnterpriseVerification::where('user_id', $params['user_id'])->update([
                    'org_id' => $params['org_id'] ?? '',
                    'org_name' => $params['org_name'],
                    'org_id_card_num' => $params['org_id_card_num'],
                    'org_id_card_type' => $params['org_id_card_type'] ?? 'CRED_ORG_USCC',
                    'legal_rep_name' => $params['legal_rep_name'],
                    'legal_rep_id_card_num' => $params['legal_rep_id_card_num'],
                    'legal_rep_id_card_type' => $params['legal_rep_id_card_type'] ?? 'CRED_PSN_CH_IDCARD',
                    'bank_name' => $params['bank_name'] ?? '',
                    'bank_card' => $params['bank_card'] ?? '',
                    'admin_name' => $params['admin_name'] ?? '',
                    'admin_account' => $params['admin_account'] ?? '',
                    'authorize_user_info' => $params['authorize_user_info'] ?? '0',
                    'realname_status' => $params['realname_status'] ?? '0',
                    'email' => $params['email'] ?? '0',
                    'address' => $params['address'] ?? '0',
                ]);
            } else {
                // 创建新记录
                EnterpriseVerification::create([
                    'user_id' => $params['user_id'],
                    'org_id' => $params['org_id'] ?? '',
                    'org_name' => $params['org_name'],
                    'org_id_card_num' => $params['org_id_card_num'],
                    'org_id_card_type' => $params['org_id_card_type'] ?? 'CRED_ORG_USCC',
                    'legal_rep_name' => $params['legal_rep_name'],
                    'legal_rep_id_card_num' => $params['legal_rep_id_card_num'],
                    'legal_rep_id_card_type' => $params['legal_rep_id_card_type'] ?? 'CRED_PSN_CH_IDCARD',
                    'admin_name' => $params['admin_name'] ?? '',
                    'bank_name' => $params['bank_name'] ?? '',
                    'bank_card' => $params['bank_card'] ?? '',
                    'admin_account' => $params['admin_account'] ?? '',
                    'authorize_user_info' => $params['authorize_user_info'] ?? '0',
                    'realname_status' => $params['realname_status'] ?? '0',
                    'email' => $params['email'] ?? '0',
                    'address' => $params['address'] ?? '0',
                ]);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public static function delete(array $params): bool
    {
        return EnterpriseVerification::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public static function detail($params): array
    {
        return EnterpriseVerification::findOrEmpty($params['id'])->toArray();
    }
}
