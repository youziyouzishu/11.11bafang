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

use app\adminapi\logic\EContractLogic;
use app\api\lists\ContractLogLists;

/**
 * 账户流水
 * Class AccountLogController
 * @package app\api\controller
 */
class ContractLogController extends BaseApiController
{
    /**
     * @notes 账户流水
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/2/24 14:34
     */
    public function lists()
    {
        return $this->dataLists(new ContractLogLists());
    }

    /**
     * @notes 获取签署URL
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function signContractUrl()
    {
        $params['id'] = $this->request->get('id', '');
        $result = EContractLogic::getAppSignContractUrl($params);
        return $this->data($result);
    }

    /**
     * @notes 下载签署后合同
     * @return \think\response\Json
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function downloadContractUrl()
    {
        $params['id'] = $this->request->get('id', '');
        $result = EContractLogic::getDownloadContractUrl($params);
        return $this->data($result);
    }
}
