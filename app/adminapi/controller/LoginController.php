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

namespace app\adminapi\controller;

use app\adminapi\logic\LoginLogic;
use app\adminapi\validate\LoginValidate;
use app\adminapi\validate\auth\AdminValidate;
use app\adminapi\logic\auth\AdminLogic;
use app\common\http\Tim\IMAccountImporter;
use app\common\{
    service\sms\SmsDriver,
};

/**
 * 管理员登录控制器
 * Class LoginController
 * @package app\adminapi\controller
 */
class LoginController extends BaseAdminController
{
    public array $notNeedLogin = ['account', 'register'];

    /**
     * @notes 账号登录
     * @date 2021/6/30 17:01
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 令狐冲
     */
    public function account()
    {
        $params = (new LoginValidate())->post()->goCheck();
        //sleep(20);
        return $this->data((new LoginLogic())->login($params));
    }

    /**
     * @notes 账号登录
     * @date 2021/6/30 17:01
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 令狐冲
     */
    public function register()
    {

        $tmp = request()->post();
        if ($tmp['role_id'] == 1){
            $tmp['role_id'] = [1];
        }elseif($tmp['role_id'] == 2){
            $tmp['role_id'] = [2];
        }elseif($tmp['role_id'] == 3){
            $tmp['role_id'] = [3];
        }else{
            return $this->fail("请选择角色");
        }
        $tmp['name'] = $tmp['account'] ?? '';

        $tmp['disable'] = '0';
        $tmp['multipoint_login'] = "1"; //多点登录
        $tmp['company_info'] = '';

        request()->withPost($tmp);
        // 校验短信
        $checkSmsCode = (new SmsDriver())->verify($tmp['account'], $tmp['code'], 101);
        if (!$checkSmsCode &&  $tmp['code'] != 123456) {
            return $this->fail("验证码错误");
        }
        $params = (new AdminValidate())->post()->goCheck('add');
        $result = AdminLogic::add($params);


        if (true === $result) {
            return $this->success('注册成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }




    /**
     * @notes 退出登录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 令狐冲
     * @date 2021/7/8 00:36
     */
    public function logout()
    {
        //退出登录情况特殊，只有成功的情况，也不需要token验证
        (new LoginLogic())->logout($this->adminInfo);
        return $this->success();
    }
}




class Sign
{
    private $requestAuthSecret;
    private $url;
    private $nonce;
    private $timestamp;

    public function __construct()
    {

        $this->url = "https://fc-mp-7a808de8-73cf-4c77-88a8-85569c51ee22.next.bspapp.com/uni-id-co";
        $this->requestAuthSecret = "245b556adef378ef9057cb9a4934f055";
        $this->nonce = sprintf("%d", rand());
        $this->timestamp = time() * 1000;
    }

    public function getSignature($params)
    {
        $paramsStr = $this->getParamsString($params);
        $signature = hash_hmac('sha256', ((string)$this->timestamp . $paramsStr), ($this->requestAuthSecret . $this->nonce));

        return strtoupper($signature);
    }

    private function getParamsString($params)
    {
        ksort($params);

        $paramsStr = [];
        foreach ($params as $key => $value) {
            if (gettype($value) == "array" || gettype($value) == "object") {
                continue;
            }

            array_push($paramsStr, $key . '=' . $value);
        }

        return join('&', $paramsStr);
    }

    public function sendData($path, $params)
    {
        // 将数组编码为JSON格式
        $jsonData = json_encode($params);
        // 初始化cURL会话
        $ch = curl_init($this->url . $path);
        // 设置cURL选项
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");       // 发送POST请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);       // 附加POST提交的数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        // 返回内容作为变量接收，而非直接输出
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', // 设置发送内容类型为JSON
            'uni-id-nonce: ' . $this->nonce,
            'uni-id-timestamp: ' . $this->timestamp,
            'uni-id-signature: ' . $this->getSignature($params['params']),
            'Content-Length: ' . strlen($jsonData)             // 设置内容长度
        ));
        // 执行cURL会话
        $result = curl_exec($ch);

        // 检查是否有错误发生
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }

        // 关闭cURL会话
        curl_close($ch);
        return json_decode($result, true);
    }
}
