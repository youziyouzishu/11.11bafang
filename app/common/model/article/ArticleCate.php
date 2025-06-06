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

namespace app\common\model\article;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 资讯分类管理模型
 * Class ArticleCate
 * @package app\common\model\article;
 */
class ArticleCate extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';


    /**
     * @notes 关联文章
     * @return \think\model\relation\HasMany
     * @author 张晓科
     * @date 2023/10/19 16:59
     */
    public function article()
    {
        return $this->hasMany(Article::class, 'cid', 'id');
    }


    /**
     * @notes 状态描述
     * @param $value
     * @param $data
     * @return string
     * @author 张晓科
     * @date 2023/9/15 11:25
     */
    public function getIsShowDescAttr($value, $data)
    {
        return $data['is_show'] ? '启用' : '停用';
    }


    /**
     * @notes 文章数量
     * @param $value
     * @param $data
     * @return int
     * @author 张晓科
     * @date 2023/9/15 11:32
     */
    public function getArticleCountAttr($value, $data)
    {
        return Article::where(['cid' => $data['id']])->count('id');
    }
}
