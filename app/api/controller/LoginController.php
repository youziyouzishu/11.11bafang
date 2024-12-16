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

use app\api\validate\{LoginAccountValidate, RegisterValidate, WebScanLoginValidate, WechatLoginValidate};
use app\api\logic\LoginLogic;
use app\common\model\user\User;
use app\common\cache\UserTokenCache;
use app\common\model\PersonalVerification;
use app\common\model\decorate\DecorateTabbar;


/**
 * 登录注册
 * Class LoginController
 * @package app\api\controller
 */
class LoginController extends BaseApiController
{

    public array $notNeedLogin = ['register', 'account', 'logout', 'codeUrl', 'oaLogin',  'mnpLogin', 'getScanCode', 'scanLogin'];


    /**
     * @notes 注册账号
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/7 15:38
     */
    public function register()
    {
        $params = (new RegisterValidate())->post()->goCheck('register');

        $result = LoginLogic::register($params);
        if (true === $result) {
            return $this->success('注册成功', [], 1, 1);
        }
        return $this->fail(LoginLogic::getError());
    }


    /**
     * @notes 账号密码/手机号密码/手机号验证码登录
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/16 10:42
     */
    public function account()
    {
        $params = (new LoginAccountValidate())->post()->goCheck();
        $result = LoginLogic::loginOrRegister($params);

        if (false === $result) {
            return $this->fail(LoginLogic::getError());
        }



        $token = $result['token'];
        $user = (new UserTokenCache())->setUserInfo($token);
        // $clientInfo = $this->request->post('clientInfo');
        // $clientInfo['devicePixelRatio'] = 3;
        // $clientInfo['windowWidth'] = 0;
        // $clientInfo['windowHeight'] = 0;
        // $clientInfo['screenWidth'] = 375;
        // $clientInfo['screenHeight'] = 667;
        //file_put_contents("./1.log", json_encode($clientInfo));
        // exit;

        // if (empty($result['uniid'])) {
        //     $params = [
        //         'clientInfo' => $clientInfo,
        //         'uniIdToken' => '',
        //         'params' => [
        //             "externalUid" => strval($result['sn']), "nickname" => $result['nickname'] ?? '', "avatar" => $this->userInfo['avatar'] ?? '', "gender" => intval($result['sex']) ?? 0
        //         ]
        //     ];
        //     $uni_result = $sign->sendData("/externalRegister", $params);
        //     if (!empty($uni_result['uid'])) {
        //         user::update(
        //             [
        //                 'id' => $result['id'],
        //                 'uniid' => $uni_result['uid'],
        //             ]
        //         );
        //         (new UserTokenCache())->setUserInfo($token);
        //     }
        // } else {
        //     $params = [
        //         'clientInfo' => $clientInfo,
        //         'uniIdToken' => '',
        //         'params' => ["externalUid" => strval($result['sn'])]
        //     ];
        //     $uni_result = $sign->sendData("/externalLogin", $params);
        // }
        //$result['unilogin'] = $uni_result;
        $result['rd'] = $user['role'] != 4 ? "/pages/tasks/index" : "/pages/contract/index";
        $result['tabbar'] = DecorateTabbar::getTabbarLists($user['role']);
        return $this->data($result);
    }


    /**
     * @notes 退出登录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 张晓科
     * @date 2023/9/16 10:42
     */
    public function logout()
    {
        LoginLogic::logout($this->userInfo);
        return $this->success();
    }


    /**
     * @notes 获取微信请求code的链接
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/15 18:27
     */
    public function codeUrl()
    {
        $url = $this->request->get('url');
        $result = ['url' => LoginLogic::codeUrl($url)];
        return $this->success('获取成功', $result);
    }


    /**
     * @notes 公众号登录
     * @return \think\response\Json
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 张晓科
     * @date 2023/9/20 19:48
     */
    public function oaLogin()
    {
        $params = (new WechatLoginValidate())->post()->goCheck('oa');
        $res = LoginLogic::oaLogin($params);
        if (false === $res) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $res);
    }


    /**
     * @notes 小程序-登录接口
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 19:48
     */
    public function mnpLogin()
    {
        $params = (new WechatLoginValidate())->post()->goCheck('mnpLogin');
        $res = LoginLogic::mnpLogin($params);
        if (false === $res) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $res);
    }


    /**
     * @notes 小程序绑定微信
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/20 19:48
     */
    public function mnpAuthBind()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("wechatAuth");
        $params['user_id'] = $this->userId;
        $result = LoginLogic::mnpAuthLogin($params);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }



    /**
     * @notes 公众号绑定微信
     * @return \think\response\Json
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 张晓科
     * @date 2023/9/20 19:48
     */
    public function oaAuthBind()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("wechatAuth");
        $params['user_id'] = $this->userId;
        $result = LoginLogic::oaAuthLogin($params);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }


    /**
     * @notes 获取扫码地址
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/10/20 18:25
     */
    public function getScanCode()
    {
        $redirectUri = $this->request->get('url/s');
        $result = LoginLogic::getScanCode($redirectUri);
        if (false === $result) {
            return $this->fail(LoginLogic::getError() ?? '未知错误');
        }
        return $this->success('', $result);
    }


    /**
     * @notes 网站扫码登录
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/10/21 10:28
     */
    public function scanLogin()
    {
        $params = (new WebScanLoginValidate())->post()->goCheck();
        $result = LoginLogic::scanLogin($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError() ?? '登录失败');
        }
        return $this->success('', $result);
    }


    /**
     * @notes 更新用户头像昵称
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/2/22 11:15
     */
    public function updateUser()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("updateUser");
        LoginLogic::updateUser($params, $this->userId);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 更新用户头像昵称
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/2/22 11:15
     */
    public function logOff()
    {
        User::destroy($this->userId);
        LoginLogic::logout($this->userInfo);
        return $this->success('操作成功', [], 1, 1);
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
