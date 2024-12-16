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


use app\common\model\ESeals;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * ESeals逻辑
 * Class ESealsLogic
 * @package app\adminapi\logic
 */
class ESealsLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            ESeals::create([
                'user_id' => $params['user_id'],
                'target_id' => $params['target_id'],
                'type' => $params['type'],
                'seal_name' => $params['seal_name'],
                'seal_template_style' => $params['seal_template_style'],
                'seal_opacity' => $params['seal_opacity'],
                'seal_color' => $params['seal_color'],
                'seal_old_style' => $params['seal_old_style'],
                'seal_size' => $params['seal_size']
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
     * @date 2024/06/09 09:41
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            ESeals::where('id', $params['id'])->update([
                'user_id' => $params['user_id'],
                'target_id' => $params['target_id'],
                'type' => $params['type'],
                'seal_name' => $params['seal_name'],
                'seal_template_style' => $params['seal_template_style'],
                'seal_opacity' => $params['seal_opacity'],
                'seal_color' => $params['seal_color'],
                'seal_old_style' => $params['seal_old_style'],
                'seal_size' => $params['seal_size']
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
     * @notes 删除
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public static function delete(array $params): bool
    {
        return ESeals::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public static function detail($params): array
    {
        return ESeals::findOrEmpty($params['id'])->toArray();
    }
}
