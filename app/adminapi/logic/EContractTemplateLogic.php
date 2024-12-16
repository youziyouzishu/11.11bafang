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


use app\common\model\EContractTemplate;
use app\common\logic\BaseLogic;
use think\facade\Db;
use app\common\http\esign\OrganizeAuth;

/**
 * EContractTemplate逻辑
 * Class EContractTemplateLogic
 * @package app\adminapi\logic
 */
class EContractTemplateLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            $sdk =  new OrganizeAuth();

            $file_id = $sdk->fileUploadUrl($params['file_path']);


            while ($sdk->fileStatues($file_id) != 5) {
                sleep(1);
            }
            $tempateInfo = $sdk->docTemplateCreateUrl($file_id, explode(".", $params['file_name'])[0], 0);
            // exit;
            // $sdk->signAgreement($params['org_id'], $params['psn_id']);
            EContractTemplate::create([
                'file_name' => explode(".", $params['file_name'])[0],
                'file_path' => $params['file_path'],
                'file_id' => $file_id,
                'doc_template_id' => $tempateInfo->docTemplateId,
                'edit_url' => $tempateInfo->docTemplateCreateUrl,
                'show_status' => $params['show_status']
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
     * @date 2024/07/14 16:40
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {

            $update = [];
            $find = self::detail(['id' => $params['id']]);
            if (!empty($params['file_path']) && $find['file_path'] != $params['file_path']) {
                $sdk =  new OrganizeAuth();

                $file_id = $sdk->fileUploadUrl($params['file_path']);
                while ($sdk->fileStatues($file_id) != 5) {
                    sleep(1);
                }
                $tempateInfo = $sdk->docTemplateCreateUrl($file_id,  explode(".", $params['file_name'])[0], 0);

                $update = [
                    'file_id' => $file_id,
                    'doc_template_id' => $tempateInfo->docTemplateId,
                    'edit_url' => $tempateInfo->docTemplateCreateUrl,
                ];
            }

            EContractTemplate::where('id', $params['id'])->update(array_merge(
                [
                    'file_name' => $params['file_name'],
                    'doc_template_id' => $params['doc_template_id'],
                    'show_status' => $params['show_status'],
                ],
                $update
            ));

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
     * @date 2024/07/14 16:40
     */
    public static function delete(array $params): bool
    {
        return EContractTemplate::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public static function detail($params): array
    {
        return EContractTemplate::findOrEmpty($params['id'])->toArray();
    }


    /**
     * @notes 获取模版编辑链接
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/07/14 16:40
     */
    public static function getTemplateEditUrl($params): array
    {
        $result = EContractTemplate::findOrEmpty($params['id'])->toArray();

        $sdk =  new OrganizeAuth();
        $tempateInfo = $sdk->getDocTemplateCreateUrl($result['doc_template_id']);
        return  $tempateInfo;
    }
}
