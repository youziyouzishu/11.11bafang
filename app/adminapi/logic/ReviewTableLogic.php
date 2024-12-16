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


use app\common\model\ReviewTable;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * 1逻辑
 * Class ReviewTableLogic
 * @package app\adminapi\logic
 */
class ReviewTableLogic extends BaseLogic
{


    /**
     * @notes 添加1
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            ReviewTable::create([
                'project_id' => $params['project_id'],
                'reviewer_id' => $params['reviewer_id'],
                'reviewer_type' => $params['reviewer_type'],
                'target_type' => $params['target_type'],
                'target_id' => $params['target_id'],
                'score' => $params['score'],
                'review_content' => $params['review_content']
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
     * @notes 编辑1
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            ReviewTable::where('id', $params['id'])->update([
                'project_id' => $params['project_id'],
                'reviewer_id' => $params['reviewer_id'],
                'reviewer_type' => $params['reviewer_type'],
                'target_type' => $params['target_type'],
                'target_id' => $params['target_id'],
                'score' => $params['score'],
                'review_content' => $params['review_content']
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
     * @notes 删除1
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function delete(array $params): bool
    {
        return ReviewTable::destroy($params['id']);
    }


    /**
     * @notes 获取1详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function detail($params): array
    {
        return ReviewTable::findOrEmpty($params['id'])->toArray();
    }
}
