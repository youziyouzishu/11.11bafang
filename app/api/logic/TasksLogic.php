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

namespace app\api\logic;

use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\ProjectTasks;
use app\common\model\article\ArticleCate;
use app\common\model\article\ArticleCollect;
use app\common\model\auth\Admin;
use app\common\model\EContract;
use app\common\model\ProjectTasksAudit;
use Exception;
use think\facade\Db;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

/**
 * 文章逻辑
 * Class ArticleLogic
 * @package app\api\logic
 */
class TasksLogic extends BaseLogic
{

    /**
     * @notes 文章详情
     * @param $id
     * @param $userId
     * @return array
     * @author 张晓科
     * @date 2023/9/20 17:09
     */
    public static function detail($id, $userid)
    {
        // 文章详情
        $tasks = ProjectTasks::where(['id' => $id])
            ->findOrEmpty();

        if ($tasks->isEmpty()) {
            return [];
        }

        // 增加点击量
        $tasks->click_actual += 1;
        $tasks->save();
        $tasks->click = $tasks->click_virtual + $tasks->click_actual;
        $user = Admin::with(['enterpriseVerification'])
            ->field([
                'id',
                'name',
                'company_info',
                'company_img',
                'ld_licence_img',
                'hr_licence_img',
                'multipoint_login',
                'avatar',
                'sn'
            ])
            ->findOrEmpty($tasks->creator);
        $tasks->audits = ProjectTasksAudit::where(['project_id' => $id, 'user_id' => $userid])->find();
        if ($tasks->audits != null) {
            $tasks->contract = EContract::where(['project_audit_id' => $tasks->audits->id, 'to_user_id' => $userid])->find();
        }

        $tasks->imid = "C2Chr_" . $user->sn;
        $tasks->company_info = $user;
        $tasks->attr_welfare = explode(',', $tasks->attr_welfare);
        $tasks->work_type = explode(',', $tasks->work_type);
        $tasks->work_time = explode(',', $tasks->work_time);


        return $tasks->append(['click', 'imid', 'company_name'])
            ->hidden(['click_virtual', 'click_actual'])
            ->toArray();
        // // 关注状态
        // $article['collect'] = ArticleCollect::isCollectArticle($userId, $articleId);

        // return $tasks;
    }


    /**
     * @notes 加入收藏
     * @param $userId
     * @param $articleId
     * @author 张晓科
     * @date 2023/9/20 16:52
     */
    public static function addCollect($articleId, $userId)
    {
        $where = ['user_id' => $userId, 'article_id' => $articleId];
        $collect = ArticleCollect::where($where)->findOrEmpty();
        if ($collect->isEmpty()) {
            ArticleCollect::create([
                'user_id' => $userId,
                'article_id' => $articleId,
                'status' => YesNoEnum::YES
            ]);
        } else {
            ArticleCollect::update([
                'id' => $collect['id'],
                'status' => YesNoEnum::YES
            ]);
        }
    }


    /**
     * @notes 取消收藏
     * @param $articleId
     * @param $userId
     * @author 张晓科
     * @date 2023/9/20 16:59
     */
    public static function cancelCollect($articleId, $userId)
    {
        ArticleCollect::update(['status' => YesNoEnum::NO], [
            'user_id' => $userId,
            'article_id' => $articleId,
            'status' => YesNoEnum::YES
        ]);
    }


    /**
     * @notes 文章分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 张晓科
     * @date 2023/9/23 14:11
     */
    public static function cate()
    {
        return ArticleCate::field('id,name')
            ->where('is_show', '=', 1)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();
    }


    public static function audit($params)
    {
        Db::startTrans();
        try {

            $project = ProjectTasks::where(['id' =>  $params['project_id']])->find();
            $audit = ProjectTasksAudit::where(['user_id' => $params['user_id']])->where(['project_id' =>  $params['project_id']])->find();
            if ($audit) {
                throw new Exception("已申请项目，等待人力公司发起合同签署");
            }

            $auditStatus  =  ProjectTasksAudit::create([
                'creator' => $project->creator,
                'project_id' => $params['project_id'],
                'user_id' => $params['user_id'],
                'type' => $params['user_info']['role'],
                'status' =>  0,
                'remarks' => '',
            ]);
            if (!$auditStatus) {
                throw new Exception("申请失败，稍后在试");
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function staffSendAudit($params)
    {
        Db::startTrans();
        try {

            $project = ProjectTasks::where(['id' =>  $params['project_id']])->find();
            $audit = ProjectTasksAudit::where(['user_id' => $params['user_id'], "type" => $params['role']])->where(['project_id' =>  $params['project_id']])->find();
            if ($audit) {
                throw new Exception("已申请加入公司，等待公司发起合同签署");
            }

            $auditStatus  =  ProjectTasksAudit::create([
                'creator' => $project->creator,
                'project_id' => $params['project_id'],
                'user_id' => $params['user_id'],
                'recruit_user_id' => $params['recruit_user_id'],
                'type' => $params['role'],
                'status' =>  0,
                'remarks' => '',
            ]);
            if (!$auditStatus) {
                throw new Exception("入职失败，稍后在试");
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }
}
