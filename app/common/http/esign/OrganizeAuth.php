<?php

namespace app\common\http\esign;

use app\common\http\esign\emun\HttpEmun;
use app\common\http\esign\comm\EsignHttpHelper;
use app\common\http\esign\comm\EsignLogHelper;
use app\common\http\esign\Config;
use app\common\model\EnterpriseVerification;
use app\common\model\PersonalVerification;
use app\common\http\esign\comm\EsignUtilHelper;

/**
 * 认证和授权服务-企业API
 * @author  陌上
 * @date  2022/09/02 9:51
 */


//include("./EsignOpenAPI.php");

class OrganizeAuth
{

    //场景：1代表认证；2代表授权
    //$scence = 1;     //场景编号
    //$orgName = "xxxx";
    // status = 0 - 待实名, 1 - 已实名 , 2 - 未实名待授权 , 3 - 重新授权 4 - 授权有效 
    function auth($adminId, $scence, $orgName, $orgCardId)
    {
        $status = 0;
        $responseAuth = [];
        switch ($scence) {
            case "1":
                //获取企业实名url
                $realNameStatus = $this->organizationsIdentityInfo($orgName, $orgCardId, $scence);

                if ($realNameStatus == 1) {
                    $status = 1;
                } else {
                    $status = 0;
                    $responseAuth = $this->orgAuthUrl($orgName);;
                }
                break;
            case "2":
                //获取企业授权url
                //$orgId查询个人实名状态获取
                $orgId = $this->organizationsIdentityInfo($orgName, $orgCardId, $scence);
                //var_dump($authorizeUserInfo);
                if ($orgId) {
                    // $response = $this->organizationsAuthorizedInfo($orgId);
                    // //获取过期时间
                    // $expireTime = json_decode($response->getBody())->data->authorizedInfo[0]->expireTime;
                    // //计算是否授权过期
                    // $num = $this->expireTime($expireTime);
                    // if ($num > 0) {
                    //     $status = 4;

                    //     $company_info = OrganizeAuth::organizationsIdentityInfo($orgName, "", 3);

                    //     if ($company_info['code'] == "0") {

                    //         EnterpriseVerification::where('user_id', $adminId)->update([
                    //             "realname_status" =>  $status,
                    //             "org_name" => $company_info['data']['orgName'] ?? '',
                    //             "org_id" => $company_info['data']['orgId'] ?? '',
                    //             'org_id_card_num' =>  $company_info['data']['orgInfo']['orgIDCardNum'] ?? '',
                    //             'org_id_card_type' => $company_info['data']['orgInfo']['orgIDCardType'] ?? '',
                    //             'legal_rep_name' =>  $company_info['data']['orgInfo']['legalRepName'] ?? '',
                    //             'legal_rep_id_card_num' =>  $company_info['data']['orgInfo']['legalRepIDCardNum'] ?? '',
                    //             'legal_rep_id_card_type' => $company_info['data']['orgInfo']['legalRepIDCardType'] ?? '',
                    //             'admin_name' => $company_info['data']['orgInfo']['adminName'] ?? '',
                    //             'admin_account' => $company_info['data']['orgInfo']['adminAccount'] ?? '',
                    //         ]);
                    //     }
                    // } else {
                    //     $status = 3;
                    //     $responseAuth = $this->orgAuthorizehUrl($orgName, $orgCardId);
                    // }
                    $status = 3;
                    $responseAuth = $this->orgAuthorizehUrl($orgName, $orgCardId);
                } else {
                    $status = 2;
                    $responseAuth =  $this->orgAuthorizehUrl($orgName, $orgCardId);
                }
                break;
            default:
                EsignLogHelper::writeLog("场景选择错误");
        }
        EnterpriseVerification::where('user_id', $adminId)->update([
            "realname_status" => $status,
        ]);
        return ['status' => $status, 'response' => $responseAuth];
    }

    function personsAuth($userid, $scence, $psnName, $psnIdCard)
    {
        $status = 0;
        $responseAuth = [];
        switch ($scence) {
            case "1":
                //个人实名
                $realnameStatus = $this->personsIdentityInfo($psnIdCard, $scence);
                if ($realnameStatus == 1) {
                    $status = 1;
                } else {
                    $status = 0;
                    $responseAuth =  $this->psnAuthUrl($psnIdCard);
                }
                break;
            case "2":
                //个人授权
                //$psnId查询个人实名状态获取
                $psnId = $this->personsIdentityInfo($psnIdCard, $scence);

                if ($psnId) {
                    // $response = $this->personsAuthorizedInfo($psnId);

                    // $expireTime = json_decode($response->getBody())->data->authorizedInfo[0]->expireTime;
                    // //计算是否授权过期
                    // $num = $this->expireTime($expireTime);
                    // if ($num > 0) {
                    //     $status = 4;
                    //     $psnInfo = OrganizeAuth::personsIdentityInfo($psnIdCard, 3);
                    //     if ($psnInfo['code'] == "0") {
                    //         PersonalVerification::where('user_id', $userid)->update([
                    //             "realname_status" =>  $status,
                    //             "psn_id" => $psnInfo['data']['psnId'] ?? '',
                    //             "psn_id_card_num" => $psnInfo['data']['psnInfo']['psnIDCardNum'] ?? '',
                    //             "psn_id_card_type" => $psnInfo['data']['psnInfo']['psnIDCardType'] ?? '',
                    //             "account_mobile" => $psnInfo['data']['psnAccount']['accountMobile'] ?? '',
                    //             "account_email" => $psnInfo['data']['psnAccount']['accountEmail'] ?? '',
                    //             "psn_mobile" => $psnInfo['data']['psnInfo']['psnMobile'] ?? '',
                    //             "psn_name" => $psnInfo['data']['psnInfo']['psnName'] ?? '',
                    //         ]);
                    //     }
                    // } else {
                    //     $status = 3;
                    //     $responseAuth =   $this->psnAuthorizehUrl($psnName, $psnIdCard);
                    // }
                    $status = 3;
                    $responseAuth =   $this->psnAuthorizehUrl($psnName, $psnIdCard);
                } else {
                    $status = 2;
                    $responseAuth =  $this->psnAuthorizehUrl($psnName, $psnIdCard);
                }

                break;
            default:
                EsignLogHelper::writeLog("场景选择错误");
        }
        PersonalVerification::where('user_id', $userid)->update([
            "realname_status" => $status,
        ]);
        return ['status' => $status, 'response' => $responseAuth];
    }
    /**
     * @param $expireTime
     * @return float
     */
    function expireTime($expireTime)
    {
        list($t1, $t2) = explode(' ', microtime());
        $nowTime = (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
        $num = $expireTime - $nowTime;
        return $num;
    }

    /**
     * 查询企业授权详情
     * @param $orgId
     * @return \esign\comm\EsignResponse
     */
    function organizationsAuthorizedInfo($orgId)
    {
        $config =  Config::$config;

        $apiaddr = "/v3/organizations/" . $orgId . "/authorized-info";
        $requestType = HttpEmun::GET;
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return $response;
    }

    /**
     * 查询企业实名状态
     * @param $orgName
     * @param $scence
     * @return mixed
     */
    static function organizationsIdentityInfo($orgName, $orgCardId, $scence = 3)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/organizations/identity-info?orgName=" . $orgName;
        $requestType = HttpEmun::GET;
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        if (json_decode($response->getBody())->code != 0) {
            return '';
        }
        $realnameStatus = json_decode($response->getBody())->data->realnameStatus;
        $orgId = json_decode($response->getBody())->data->orgId;
        if ($scence == 1) {
            return $realnameStatus;
        } else if ($scence == 3) {
            return json_decode($response->getBody(), true);
        } else {
            return $orgId;
        }
    }

    /**
     * 企业实名
     * @param $orgName
     */
    function orgAuthUrl($orgName)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/org-auth-url";
        $requestType = HttpEmun::POST;
        $data = [
            "orgAuthConfig" => [
                "orgName" => $orgName,
                "orgInfo" => [
                    "orgIDCardNum" => "910000470902480019",
                    "orgIDCardType" => "CRED_ORG_USCC"

                ],
                "transactorInfo" => [
                    "psnAccount" => "",
                    "psnInfo" => [
                        "psnIDCardNum" => "",
                        "psnName" => ""
                    ]
                ],
            ],
            "authorizeConfig" => [],
            "redirectConfig" => [
                "redirectUrl" => "https://www.baidu.com/"
            ],
            "notifyUrl" => "",
            "clientType" => "ALL",
            "appScheme" => ""
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return json_decode($response->getBody(), true);
    }

    /**
     * 企业授权
     * @param $orgName
     */
    function orgAuthorizehUrl($orgName, $orgCardId)
    {

        $config =  Config::$config;
        $apiaddr = "/v3/org-auth-url";
        $requestType = HttpEmun::POST;
        $data = [
            "orgAuthConfig" => [
                "orgName" => $orgName,
                "orgInfo" => [
                    "orgIDCardNum" => $orgCardId,
                    "orgIDCardType" => "CRED_PSN_CH_IDCARD"

                ],
                "transactorInfo" => [
                    "psnAccount" => "",
                    "psnInfo" => [
                        "psnIDCardNum" => "",
                        "psnName" => ""
                    ]
                ],
            ],
            "authorizeConfig" => [
                "authorizedScopes" => [
                    "get_org_identity_info",
                    "get_psn_identity_info",
                    "org_initiate_sign",
                    "psn_initiate_sign",
                    "manage_org_resource",
                    "manage_psn_resource",
                    "psn_sign_file_storage",
                    "org_sign_file_storage",
                    "org_approval_info",
                    "use_org_order"
                ]
            ],
            "redirectConfig" => [
                "redirectUrl" => config::getDomain() . "/business/member/user/verify"
            ],
            "transactorUseSeal" => true,
            "notifyUrl" => "",
            "clientType" => "ALL",
            "appScheme" => ""
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return json_decode($response->getBody(), true);
    }


    //签署回调
    static function  CallbackVerify()
    {
        $config =  Config::$config;
        //    此处可以打印下日志  

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new \Exception("请求方法不对");
        }

        // throw new \Exception(json_encode($_SERVER));
        //    校验签名 如果header里放入的值为X_TSIGN_OPEN_SIGNATURE，到header里会自动加上HTTP_，并且转化为大写，取值时如下
        if (!isset($_SERVER['HTTP_X_TSIGN_OPEN_SIGNATURE'])) {
            throw new \Exception("签名不能为空");
        }
        $sign =  $_SERVER['HTTP_X_TSIGN_OPEN_SIGNATURE'];

        $secret = $config['eSignAppSecret']; //应用对应密钥

        //1.获取时间戳的字节流
        if (!isset($_SERVER['HTTP_X_TSIGN_OPEN_TIMESTAMP'])) {
            throw new \Exception("时间戳不能为空");
        }
        $timeStamp =  $_SERVER['HTTP_X_TSIGN_OPEN_TIMESTAMP'];

        //2.获取query请求的字节流，对 Query 参数按照字典对 Key 进行排序后,按照value1+value2方法拼接
        $params = $_GET;
        if (!empty($params)) {
            ksort($params);
        }

        $requestQuery = '';
        foreach ($params as $val) {
            $requestQuery .= $val;
        }

        //3. 获取body的数据
        $body = file_get_contents("php://input");

        //4.组装数据并计算签名
        $data = $timeStamp . $requestQuery . $body;


        $mySign = hash_hmac("sha256", $data, $secret);

        if ($mySign != $sign) {
            throw new \Exception("签名校验失败");
        }
        $result = json_decode($body, true);
        return $result;
    }

    /**
     * 发起个人实名
     * @param $psnAccount
     */
    function psnAuthUrl($psnAccount)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/psn-auth-url";
        $requestType = HttpEmun::POST;
        $data = [
            "psnAuthConfig" => [
                "psnAccount" => $psnAccount,
                "psnInfo" => [
                    "psnName" => ""
                ]
            ],
            "redirectConfig" => [
                "redirectUrl" => "https://www.baidu.com/"
            ],
            "notifyUrl" => "",
            "clientType" => "ALL",
            "appScheme" => ""
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header


        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
    }

    /**
     * 发起个人授权
     * @param $psnAccount
     */
    function psnAuthorizehUrl($psnName, $psnIdCard)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/psn-auth-url";
        $requestType = HttpEmun::POST;
        $data = [
            "psnAuthConfig" => [
                "psnInfo" => [
                    "psnName" => $psnName,
                    "psnIDCardType" => "CRED_PSN_CH_IDCARD",
                    "psnIDCardNum" => $psnIdCard,
                    //"psnIdentityVerify" => true
                ]
            ],
            "authorizeConfig" => [
                "authorizedScopes" => [
                    "get_psn_identity_info",
                    "psn_initiate_sign",
                    "manage_psn_resource",
                    "psn_sign_file_storage"
                ]
            ],
            "redirectConfig" => [
                "redirectUrl" => "bafang://pages/bind/user_detail"
            ],
            "notifyUrl" => "",
            "clientType" => "ALL",
            "appScheme" => "bafang://pages/bind/user_detail"
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header


        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return json_decode($response->getBody(), true);
    }

    /**
     * 查询个人认证信息
     * @param $psnId
     * @param $scence
     * @return mixed
     */
    static function personsIdentityInfo($psnIDCardNum, $scence)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/persons/identity-info?psnIDCardNum=" . $psnIDCardNum . "&psnIDCardType=CRED_PSN_CH_IDCARD";
        $requestType = HttpEmun::GET;
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        if (json_decode($response->getBody())->code != 0) {
            return '';
        }

        $realnameStatus = json_decode($response->getBody())->data->realnameStatus;

        $psnId = json_decode($response->getBody())->data->psnId;

        if ($scence == 1) {
            return $realnameStatus;
        } else if ($scence == 3) {
            return json_decode($response->getBody(), true);
        } else {
            return $psnId;
        }
    }

    /**
     * 查询个人认证信息
     * @param $psnId
     * @param $scence
     * @return mixed
     */
    static function queryAuthFlow($flowid)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/auth-flow/" . $flowid;
        $requestType = HttpEmun::GET;
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        if (json_decode($response->getBody())->code != 0) {
            return '';
        }
        return json_decode($response->getBody(), true);
    }
    /**
     * 查询个人授权信息
     * @param $psnId
     * @return \esign\comm\EsignResponse
     */
    function personsAuthorizedInfo($psnId)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/persons/" . $psnId . "/authorized-info";
        $requestType = HttpEmun::GET;
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return $response;
    }


    /**
     * 签署合作协议
     */
    function signAgreement($orgId, $psnId)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/sign-flow/create-by-sign-template";
        $requestType = HttpEmun::POST;
        $data = [
            "signTemplateId" => 'a707df7a6d1b426aabecd0b4911b8bec',
            "signFlowInitiator" => [
                "orgId" => $orgId,
                "transactor" => [
                    "psnId" => $psnId,
                ]
            ],
            "signFlowConfig" => [
                "signFlowTitle" => "合作协议",
            ]
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return json_decode($response->getBody(), true);
    }

    ///v3/sign-templates/detail


    /**
     * 签署合作协议
     */
    function getSignTempateDetail($orgId)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/sign-templates/detail?signTemplateId=a707df7a6d1b426aabecd0b4911b8bec&queryComponents=true&orgId=" . $orgId;
        $requestType = HttpEmun::GET;
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());

        return json_decode($response->getBody(), true);
    }

    /**
     * 查询合同模板中控件详情
     * @param $docTemplateId
     *
     */
    function templatesTComponents($docTemplateId)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/doc-templates/" . $docTemplateId;
        $requestType = HttpEmun::GET;
        //生成签名验签+json体的header

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::printMsg($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::printMsg($response->getStatus());
        EsignLogHelper::printMsg($response->getBody());
    }
    /**
     * @param $filePath
     * @param $contentMd5
     * @return mixed
     */
    function fileUploadUrl($filePath)
    {
        $filePath = ROOT_DIR . "/" . $filePath;
        if (!file_exists($filePath)) {
            EsignLogHelper::printMsg($filePath . "文件不存在");
            exit;
        }
        $contentMd5 = EsignUtilHelper::getContentBase64Md5($filePath);
        $config =  Config::$config;
        $apiaddr = "/v3/files/file-upload-url";
        $requestType = HttpEmun::POST;
        $fileInfo = pathinfo($filePath);
        $contentType = "application/" . $fileInfo['extension'];

        $fileName = $fileInfo['basename'];

        $data = [
            "contentMd5" => $contentMd5,
            "contentType" =>  $contentType,
            "convertToPDF" => true,
            "convertToHTML" => false,
            "fileName" => $fileName,
            "fileSize" => filesize($filePath)
        ];

        $paramStr = json_encode($data);
        //生成签名验签+json体的header

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //获取文件上传地址
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        $fileUploadUrl = json_decode($response->getBody())->data->fileUploadUrl;
        $fileId = json_decode($response->getBody())->data->fileId;
        //文件流put上传
        $response = EsignHttpHelper::upLoadFileHttp($fileUploadUrl, $filePath, $contentType);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return $fileId;
    }


    /**
     * 获取制作合同模板页面
     * @param $fileId
     * @param $docTemplateType
     * @return mixed
     */
    function docTemplateCreateUrl($fileId, $fileName , $docTemplateType)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/doc-templates/doc-template-create-url";
        $requestType = HttpEmun::POST;
        $data = [
            "docTemplateName" => $fileName ,
            "docTemplateType" => $docTemplateType,
            "fileId" => $fileId,
            "redirectUrl" => ""
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header


        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());

        return json_decode($response->getBody())->data;
    }


    /**
     * 获取制作合同模板页面
     * @param $fileId
     * @param $docTemplateType
     * @return mixed
     */
    function getDocTemplateCreateUrl($templateId)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/doc-templates/{$templateId}/doc-template-edit-url";
        $requestType = HttpEmun::POST;
        $data = [
            "docTemplateId" => $templateId,
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());

        return json_decode($response->getBody(), true);
    }


    /**
     * 获取制作合同模板页面
     * @param $fileId
     * @param $docTemplateType
     * @return mixed
     */
    function getSignContractUrl($signFlowId, $psnId, $redirectUrl)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/sign-flow/{$signFlowId}/sign-url";
        $requestType = HttpEmun::POST;
        $data = [
            "signFlowId" => $signFlowId,
            "operator" => [
                "psnId" => $psnId
            ],
            "clientType" => "ALL",
            "urlType" => 2,
            "redirectConfig" => [
                "redirectUrl" => $redirectUrl
            ],
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取合同下载地址
     * @param $fileId
     * @param $docTemplateType
     * @return mixed
     */
    function getDownloadContractUrl($signFlowId)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/sign-flow/{$signFlowId}/file-download-url";
        $requestType = HttpEmun::GET;
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        return json_decode($response->getBody(), true);
    }

    /**
     * 查询文件状态
     * @param $fileId
     * @return mixed
     */
    function fileStatues($fileId)
    {
        $config =  Config::$config;

        $apiaddr = "/v3/files/" . $fileId;
        $requestType = HttpEmun::GET;
        //生成签名验签+json体的header


        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], "", $requestType, $apiaddr);
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        //发起接口请求
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, "");
        EsignLogHelper::writeLog($response->getBody());
        EsignLogHelper::writeLog($response->getStatus());
        $fileStatus  = json_decode($response->getBody())->data->fileStatus;
        return  $fileStatus;
    }
    /**
     * 基于模板创建文件
     * @param $docTemplateId 模版id
     * @param $contractName 合同名称
     * @param $clientName   客户名称
     * @param $clientRealname   客户真实姓名
     * @param $clientIdCard  客户身份证号
     * @param $phone    手机号
     * @param $email   邮箱
     * @param $address 地址
     */
    function createByDocTemplate($docTemplateId, $contractName, $clientName, $clientRealname, $clientIdCard, $phone, $email, $address)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/files/create-by-doc-template";
        $requestType = HttpEmun::POST;
        $data = [
            "docTemplateId" => $docTemplateId,
            "fileName" => $contractName,
            "components" => [
                [
                    "componentKey" => "client_name",
                    "componentValue" => $clientName,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "client_realname",
                    "componentValue" => $clientRealname,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "client_id_card",
                    "componentValue" => $clientIdCard,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "phone",
                    "componentValue" => $phone,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "email",
                    "componentValue" => $email,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "address",
                    "componentValue" => $address,
                    "requiredCheck" => false
                ],
            ]
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());

        $fileId = json_decode($response->getBody())->data->fileId;

        return $fileId;
    }

    /**
     * 基于模版创建招聘合同
     * @param $docTemplateId 模版id
     * @param $contractName 合同名称
     * @param $clientName   客户名称
     * @param $clientRealname   客户真实姓名
     * @param $clientIdCard  客户身份证号
     * @param $phone    手机号
     * @param $email   邮箱
     * @param $address 地址
     */
    function createRecruitByDocTemplate($docTemplateId, $contractName, $clientName, $clientRealname, $clientIdCard, $phone, $email, $address, $psn_name, $psn_id_card, $psn_mobile, $psn_account_fees, $psn_default_cost, $psn_pay_type, $psn_wages_day)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/files/create-by-doc-template";
        $requestType = HttpEmun::POST;
        $data = [
            "docTemplateId" => $docTemplateId,
            "fileName" => $contractName,
            "components" => [
                [
                    "componentKey" => "client_name",
                    "componentValue" => $clientName,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "client_realname",
                    "componentValue" => $clientRealname,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "client_id_card",
                    "componentValue" => $clientIdCard,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "phone",
                    "componentValue" => $phone,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "email",
                    "componentValue" => $email,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "address",
                    "componentValue" => $address,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_name",
                    "componentValue" => $psn_name,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_id_card",
                    "componentValue" => $psn_id_card,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_mobile",
                    "componentValue" => $psn_mobile,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_pay_type",
                    "componentValue" => $psn_pay_type,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_account_fees",
                    "componentValue" => $psn_account_fees,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_default_cost",
                    "componentValue" => intval($psn_default_cost),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_wages_day",
                    "componentValue" => $psn_wages_day,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "year",
                    "componentValue" => date("Y"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_year",
                    "componentValue" => date("Y"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "month",
                    "componentValue" => date("m"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_month",
                    "componentValue" => date("m"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "day",
                    "componentValue" => date("d"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_day",
                    "componentValue" => date("d"),
                    "requiredCheck" => false
                ],
            ]
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        $body = json_decode($response->getBody(), true);
        return $body;
    }

    /**
     * 基于模版创建招聘合同
     * @param $docTemplateId 模版id
     * @param $contractName 合同名称
     * @param $clientName   客户名称
     * @param $clientRealname   客户真实姓名
     * @param $clientIdCard  客户身份证号
     * @param $phone    手机号
     * @param $email   邮箱
     * @param $address 地址
     */
    function createZhuchangByDocTemplate(
        $docTemplateId,
        $contractName,
        $clientName,
        $clientRealname,
        $clientIdCard,
        $phone,
        $email,
        $address,
        $psn_name,
        $psn_id_card,
        $psn_mobile,
        $psn_pay_type,
        $psn_wages_day,
        $server_address,
        $server_sdate,
        $server_edate,
        $ratio,
        $cost3,
        $cost4,
        $cost5,
        $cost6,
        $cost7,
        $cost8,
        $cost13,
        $cost14
    ) {
        $config =  Config::$config;
        $apiaddr = "/v3/files/create-by-doc-template";
        $requestType = HttpEmun::POST;
        $data = [
            "docTemplateId" => $docTemplateId,
            "fileName" => $contractName,
            "components" => [
                [
                    "componentKey" => "client_name",
                    "componentValue" => $clientName,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "client_realname",
                    "componentValue" => $clientRealname,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "client_id_card",
                    "componentValue" => $clientIdCard,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "phone",
                    "componentValue" => $phone,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "email",
                    "componentValue" => $email,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "address",
                    "componentValue" => $address,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_name",
                    "componentValue" => $psn_name,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_id_card",
                    "componentValue" => $psn_id_card,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_mobile",
                    "componentValue" => $psn_mobile,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_pay_type",
                    "componentValue" => $psn_pay_type,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "server_address",
                    "componentValue" => $server_address,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "server_sdate",
                    "componentValue" => $server_sdate,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "server_edate",
                    "componentValue" => $server_edate,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_wages_day",
                    "componentValue" => intval($psn_wages_day),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "ratio",
                    "componentValue" => intval($ratio),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "cost3",
                    "componentValue" => intval($cost3),
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "cost4",
                    "componentValue" => intval($cost4),
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "cost5",
                    "componentValue" => intval($cost5),
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "cost6",
                    "componentValue" => intval($cost6),
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "cost7",
                    "componentValue" => intval($cost7),
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "cost8",
                    "componentValue" => intval($cost8),
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "cost13",
                    "componentValue" => intval($cost13),
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "cost14",
                    "componentValue" => intval($cost14),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "year",
                    "componentValue" => date("Y"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_year",
                    "componentValue" => date("Y"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "month",
                    "componentValue" => date("m"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_month",
                    "componentValue" => date("m"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "day",
                    "componentValue" => date("d"),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_day",
                    "componentValue" => date("d"),
                    "requiredCheck" => false
                ],
            ]
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        $body = json_decode($response->getBody(), true);
        return $body;
    }

    /**
     * 基于模版创建招聘合同
     * @param $docTemplateId 模版id
     * @param $contractName 合同名称
     * @param $clientName   客户名称
     * @param $clientRealname   客户真实姓名
     * @param $clientIdCard  客户身份证号
     * @param $phone    手机号
     * @param $email   邮箱
     * @param $address 地址
     */
    function createWorkByDocTemplate(
        $docTemplateId,
        $contractName,
        $clientName,
        $clientRealname,
        $phone,
        $address,
        $psn_name,
        $psn_id_card,
        $psn_mobile,
        $psn_wages_day,
        $work_sdate,
        $work_edate,
        $work_month,
        $work_position,
        $pay_cost,
        $sign_date
    ) {
        $config =  Config::$config;
        $apiaddr = "/v3/files/create-by-doc-template";
        $requestType = HttpEmun::POST;
        $data = [
            "docTemplateId" => $docTemplateId,
            "fileName" => $contractName,
            "components" => [
                [
                    "componentKey" => "client_name",
                    "componentValue" => $clientName,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "client_realname",
                    "componentValue" => $clientRealname,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "phone",
                    "componentValue" => $phone,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "address",
                    "componentValue" => $address,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_name",
                    "componentValue" => $psn_name,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_id_card",
                    "componentValue" => $psn_id_card,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_mobile",
                    "componentValue" => $psn_mobile,
                    "requiredCheck" => false
                ],
                // [
                //     "componentKey" => "psn_address",
                //     "componentValue" => $psn_pay_type,
                //     "requiredCheck" => false
                // ],

                [
                    "componentKey" => "work_sdate",
                    "componentValue" => $work_sdate,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "work_edate",
                    "componentValue" => $work_edate,
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "psn_wages_day",
                    "componentValue" => intval($psn_wages_day),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "work_month",
                    "componentValue" => intval($work_month),
                    "requiredCheck" => false
                ],
                [
                    "componentKey" => "work_position",
                    "componentValue" => $work_position,
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "pay_cost",
                    "componentValue" => $pay_cost . ".00",
                    "requiredCheck" => false
                ],

                [
                    "componentKey" => "sign_date",
                    "componentValue" => $sign_date,
                    "requiredCheck" => false
                ],
            ]
        ];
        $paramStr = json_encode($data);
        //生成签名验签+json体的header

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());
        $body = json_decode($response->getBody(), true);
        return $body;
    }
    /**
     * 基于文件发起签署
     * @param $fileId
     * @param $contractName
     * @param $sendOrgId
     * @param $targetOrgId
     * @param $sealId
     * @return bool
     */
    function createByFile($fileId, $contractName, $sendOrgId, $psnId, $targetOrgId = "5e7ec709965a47c6bb1fc26a7411f35e")
    {
        $config =  Config::$config;
        $apiaddr = "/v3/sign-flow/create-by-file";
        $requestType = HttpEmun::POST;

        $data = [
            "docs" => [
                [
                    "fileId" => $fileId,
                    "fileName" => $contractName . ".pdf"
                ]
            ],
            "signFlowConfig" => [
                "signFlowTitle" => $contractName,
                "autoFinish" => true,
                "noticeConfig" => [
                    "noticeTypes" => 1 //签署链接短信通知
                ],
                "redirectConfig" => [
                    "redirectUrl" => config::getDomain() . "/business/operate/contracts/e_contract?type=plant_contract"
                ]
            ],
            "signers" => [
                [
                    "orgSignerInfo" => [
                        "orgId" => $sendOrgId,   //其他企业自动签署，设置orgId平台方id
                        "transactorInfo" => [
                            "psnId" => $psnId
                        ],
                    ],
                    "signFields" => [
                        [
                            "mustSign" => true,
                            "fileId" => $fileId,
                            "normalSignFieldConfig" => [
                                "autoSign" => false,
                                "signFieldStyle" => 1,
                                //"assignedSealId" => $sealId,    // 在signFields中设置 assignedSealId为授权企业印章id
                                "signFieldPosition" => [
                                    "positionPage" => 6,
                                    "positionX" => 440.32,
                                    "positionY" => 137.09
                                ]
                            ]
                        ]
                    ],
                    "signerType" => 1
                ],
                [
                    "orgSignerInfo" => [
                        "orgId" => $targetOrgId    //平台方自动签署
                    ],
                    "signFields" => [
                        [
                            "fileId" => $fileId,
                            "normalSignFieldConfig" => [
                                "autoSign" => true,
                                "signFieldStyle" => 1,
                                "signFieldPosition" => [
                                    "positionPage" => 6,
                                    "positionX" => 189.64,
                                    "positionY" => 137.09
                                ]
                            ]
                        ]
                    ],
                    "signerType" => 1
                ]
            ]
        ];
        $paramStr = json_encode($data);

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);

        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);

        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());

        if ($response->getStatus() == 200) {
            $result = json_decode($response->getBody(), true);
            if ($result['code'] == 0) {
                $result = $result['data'];
            } else {
                throw new \Exception("基于文件发起签署接口调用失败，错误信息: " . $result->message);
            }
        } else {
            throw new \Exception("基于文件发起签署接口调用失败，HTTP错误码" . $response->getStatus());
        }
        return $result;
    }

    /**
     * 基于文件发起签署
     * @param $fileId
     * @param $contractName
     * @param $sendOrgId
     * @param $targetOrgId
     * @param $sealId
     * @return bool
     */
    function createRecruitByFile($fileId, $contractName, $sendOrgId, $psnId, $psn_name, $targetOrgId = "5e7ec709965a47c6bb1fc26a7411f35e")
    {
        $config =  Config::$config;
        $apiaddr = "/v3/sign-flow/create-by-file";
        $requestType = HttpEmun::POST;

        $data = [
            "docs" => [
                [
                    "fileId" => $fileId,
                    "fileName" => $contractName . ".pdf"
                ]
            ],
            "signFlowConfig" => [
                "signFlowTitle" => $contractName,
                "autoFinish" => true,
                "noticeConfig" => [
                    "noticeTypes" => 1 //签署链接短信通知
                ],
                "redirectConfig" => [
                    "redirectUrl" => "bafang://pages/contract/index?a=1"
                ]
            ],
            "signers" => [
                [
                    "orgSignerInfo" => [
                        "orgId" => $sendOrgId,   //其他企业自动签署，设置orgId平台方id
                        "transactorInfo" => [
                            "psnId" => $psnId
                        ],
                    ],
                    "signFields" => [
                        [
                            "mustSign" => true,
                            "fileId" => $fileId,
                            "normalSignFieldConfig" => [
                                "autoSign" => false,
                                "signFieldStyle" => 1,
                                //"assignedSealId" => $sealId,    // 在signFields中设置 assignedSealId为授权企业印章id
                                "signFieldPosition" => [
                                    "positionPage" => 3,
                                    "positionX" => 229.24,
                                    "positionY" => 156.05
                                ]
                            ]
                        ]
                    ],
                    "signerType" => 1
                ],
                [
                    "psnSignerInfo" => [
                        "psnId" => $targetOrgId    //平台方自动签署
                    ],
                    "signFields" => [
                        [
                            "fileId" => $fileId,
                            "signFieldType" => 1,
                            "remarkSignFieldConfig" => [
                                "inputType" => 1,
                                "aiCheck" => 1,
                                "remarkContent" => $psn_name,
                                "signFieldPosition" => [
                                    "positionPage" => 3,
                                    "positionX" => 228.78,
                                    "positionY" => 87.08
                                ]
                            ]
                        ]
                    ],
                    "signerType" => 0
                ]
            ]
        ];
        $paramStr = json_encode($data);

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);

        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());

        if ($response->getStatus() == 200) {
            $result = json_decode($response->getBody(), true);
            if ($result['code'] == 0) {
                $result = $result['data'];
            } else {
                throw new \Exception("基于文件发起签署接口调用失败，错误信息: " . $result['message']);
            }
        } else {
            throw new \Exception("基于文件发起签署接口调用失败，HTTP错误码" . $response->getStatus());
        }
        return $result;
    }

    /**
     * 基于文件发起签署
     * @param $fileId
     * @param $contractName
     * @param $sendOrgId
     * @param $targetOrgId
     * @param $sealId
     * @return bool
     */
    function createZhuchangByFile($fileId, $contractName, $sendOrgId, $psnId, $psn_name, $targetOrgId = "5e7ec709965a47c6bb1fc26a7411f35e")
    {
        $config =  Config::$config;
        $apiaddr = "/v3/sign-flow/create-by-file";
        $requestType = HttpEmun::POST;

        $data = [
            "docs" => [
                [
                    "fileId" => $fileId,
                    "fileName" => $contractName . ".pdf"
                ]
            ],
            "signFlowConfig" => [
                "signFlowTitle" => $contractName,
                "autoFinish" => true,
                "noticeConfig" => [
                    "noticeTypes" => 1 //签署链接短信通知
                ],
                "redirectConfig" => [
                    "redirectUrl" => "bafang://pages/contract/index?a=1"
                ]
            ],
            "signers" => [
                [
                    "orgSignerInfo" => [
                        "orgId" => $sendOrgId,   //其他企业自动签署，设置orgId平台方id
                        "transactorInfo" => [
                            "psnId" => $psnId
                        ],
                    ],
                    "signFields" => [
                        [
                            "mustSign" => true,
                            "fileId" => $fileId,
                            "normalSignFieldConfig" => [
                                "autoSign" => false,
                                "signFieldStyle" => 1,
                                //"assignedSealId" => $sealId,    // 在signFields中设置 assignedSealId为授权企业印章id
                                "signFieldPosition" => [
                                    "positionPage" => 4,
                                    "positionX" => 230.54,
                                    "positionY" => 315
                                ]
                            ]
                        ]
                    ],
                    "signerType" => 1
                ],
                [
                    "psnSignerInfo" => [
                        "psnId" => $targetOrgId    //平台方自动签署
                    ],
                    "signFields" => [
                        [
                            "fileId" => $fileId,
                            "signFieldType" => 1,
                            "remarkSignFieldConfig" => [
                                "inputType" => 1,
                                "aiCheck" => 1,
                                "remarkContent" => $psn_name,
                                "signFieldPosition" => [
                                    "positionPage" => 4,
                                    "positionX" => 227.48,
                                    "positionY" => 229.74
                                ]
                            ]
                        ]
                    ],
                    "signerType" => 0
                ]
            ]
        ];
        $paramStr = json_encode($data);

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);

        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());

        if ($response->getStatus() == 200) {
            $result = json_decode($response->getBody(), true);
            if ($result['code'] == 0) {
                $result = $result['data'];
            } else {
                throw new \Exception("基于文件发起签署接口调用失败，错误信息: " . $result['message']);
            }
        } else {
            throw new \Exception("基于文件发起签署接口调用失败，HTTP错误码" . $response->getStatus());
        }
        return $result;
    }

    /**
     * 基于文件发起签署
     * @param $fileId
     * @param $contractName
     * @param $sendOrgId
     * @param $targetOrgId
     * @param $sealId
     * @return bool
     */
    function createWorkByFile($fileId, $contractName, $sendOrgId, $psnId, $psn_name, $targetOrgId = "5e7ec709965a47c6bb1fc26a7411f35e")
    {
        $config =  Config::$config;
        $apiaddr = "/v3/sign-flow/create-by-file";
        $requestType = HttpEmun::POST;

        $data = [
            "docs" => [
                [
                    "fileId" => $fileId,
                    "fileName" => $contractName . ".pdf"
                ]
            ],
            "signFlowConfig" => [
                "signFlowTitle" => $contractName,
                "autoFinish" => true,
                "noticeConfig" => [
                    "noticeTypes" => 1 //签署链接短信通知
                ],
                "redirectConfig" => [
                    "redirectUrl" => "bafang://pages/contract/index?a=1"
                ]
            ],
            "signers" => [
                [
                    "orgSignerInfo" => [
                        "orgId" => $sendOrgId,   //其他企业自动签署，设置orgId平台方id
                        "transactorInfo" => [
                            "psnId" => $psnId
                        ],
                    ],
                    "signFields" => [
                        [
                            "mustSign" => true,
                            "fileId" => $fileId,
                            "normalSignFieldConfig" => [
                                "autoSign" => false,
                                "signFieldStyle" => 1,
                                //"assignedSealId" => $sealId,    // 在signFields中设置 assignedSealId为授权企业印章id
                                "signFieldPosition" => [
                                    "positionPage" => 4,
                                    "positionX" => 173.54,
                                    "positionY" => 206.66
                                ]
                            ]
                        ]
                    ],
                    "signerType" => 1
                ],
                [
                    "psnSignerInfo" => [
                        "psnId" => $targetOrgId    //平台方自动签署
                    ],
                    "signFields" => [
                        [
                            "fileId" => $fileId,
                            "signFieldType" => 1,
                            "remarkSignFieldConfig" => [
                                "inputType" => 1,
                                "aiCheck" => 1,
                                "remarkContent" => $psn_name,
                                "signFieldPosition" => [
                                    "positionPage" => 4,
                                    "positionX" => 375.43,
                                    "positionY" => 196.91
                                ]
                            ]
                        ]
                    ],
                    "signerType" => 0
                ]
            ]
        ];
        $paramStr = json_encode($data);

        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);

        EsignLogHelper::writeLog($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::writeLog($response->getStatus());
        EsignLogHelper::writeLog($response->getBody());

        if ($response->getStatus() == 200) {
            $result = json_decode($response->getBody(), true);
            if ($result['code'] == 0) {
                $result = $result['data'];
            } else {
                throw new \Exception("基于文件发起签署接口调用失败，错误信息: " . $result['message']);
            }
        } else {
            throw new \Exception("基于文件发起签署接口调用失败，HTTP错误码" . $response->getStatus());
        }
        return $result;
    }
    //创建机构模板印章接口
    function createOrgsealsbyTemplate($orgId)
    {
        $config =  Config::$config;
        $apiaddr = "/v3/seals/org-seals/create-by-template";
        $requestType = HttpEmun::POST;
        //生成签名验签+json体的header
        $data = [
            "orgId" => $orgId,
            "sealName" => "合同印章", //机构印章名称（用户自定义，且名称不可重复）
            "sealTemplateStyle" => "CONTRACT_ROUND_NO_STAR", //机构模板印章样式
            "sealSize" => "38_38",
            "sealColor" => "RED", //机构印章颜色，默认是红色
            "sealHorizontalText" => "合同专用章", //自定义印章横向文，示例值：XX专用章
            "sealBottomText" => "9111113331" //印章下弦文（实体印章防伪码），示例值：9133********1
        ];
        $paramStr = json_encode($data);
        //请求参数
        // EsignLogHelper::printMsg($paramStr);
        $signAndBuildSignAndJsonHeader = EsignHttpHelper::signAndBuildSignAndJsonHeader($config['eSignAppId'], $config['eSignAppSecret'], $paramStr, $requestType, $apiaddr);
        //发起接口请求
        //发起接口请求头
        EsignLogHelper::printMsg($signAndBuildSignAndJsonHeader);
        $response = EsignHttpHelper::doCommHttp($config['eSignHost'], $apiaddr, $requestType, $signAndBuildSignAndJsonHeader, $paramStr);
        EsignLogHelper::printMsg($response->getStatus());
        $code = json_decode($response->getBody())->code;
        if ($code === 0) {
            EsignLogHelper::printMsg("机构模板印章接口调用成功");
            EsignLogHelper::printMsg($response->getBody());
        } else {
            EsignLogHelper::printMsg($response->getBody());
        }
    }
}


//OF-2203140677080009
