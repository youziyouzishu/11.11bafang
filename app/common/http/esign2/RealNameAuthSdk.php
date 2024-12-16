<?php

namespace app\common\http\esign;

class RealNameAuthSdk
{
	private $host;
	private $appId;
	private $appSecret;

	public function __construct($host, $appId, $appSecret)
	{
		$this->host = $host;
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}

	private $errorCodes = [
		// 通用错误码
		1450001 => "缺少参数：%s",
		1450002 => "参数错误：%s",
		1450003 => "参数格式错误：%s",
		1435203 => "账号不存在或已注销 :%s",

		// 个人认证和授权相关错误码
		'personal' => [
			'psn_auth_url' => [
				1450004 => "用户id：%s 由于接口版本问题不可用，请使用用户信息发起授权",
				1450005 => "个人用户已实名",
				1450901 => "创建个人账号失败，请重试或联系e签宝服务人员",
				1450903 => "发起意愿认证失败:%s",
				1450904 => "发起实名认证失败:%s",
				1450905 => "发起实名或意愿认证失败，请重试或联系e签宝服务人员"
			],
			'identity_info' => [
				1450008 => "未找到用户信息，请检查查询数据的正确性 :%s"
			],
			'authorized_info' => [
				1450910 => "个人未实名 :%s"
			]
		],

		// 机构认证和授权相关错误码
		'organization' => [
			'org_auth_url' => [
				1450004 => "用户id：%s 由于接口版本问题不可用，请使用用户信息发起授权",
				1450006 => "企业用户已实名",
				1450902 => "创建个人账号失败，请重试或联系e签宝服务人员",
				1450903 => "发起意愿认证失败:%s",
				1450904 => "发起实名认证失败:%s",
				1450905 => "发起实名或意愿认证失败，请重试或联系e签宝服务人员"
			],
			'authorized_info' => [
				1450909 => "企业未实名 :%s"
			],
			'identity_info' => [
				1450008 => "未找到用户信息，请检查查询数据的正确性 :%s"
			]
		],

		// 认证授权流程详情相关错误码
		'auth_flow' => [
			1450007 => "授权流程id不存在"
		]
	];

	private function makeRequest($endpoint, $params = [], $method = 'GET')
	{
		$url = $this->host . $endpoint;
		var_dump($url, $params);
		$curl = curl_init();

		// Define headers
		$headers = [
			'X-Tsign-Open-App-Id: ' . $this->appId,
			'X-Tsign-Open-Auth-Mode: Signature',
			'Accept: */*',
			'Content-Type: application/json; charset=UTF-8',
		];
		ksort($params);
		// Generate timestamp
		$timestamp = (string) round(microtime(true) * 1000);
		$headers[] = 'X-Tsign-Open-Ca-Timestamp: ' . $timestamp;

		// Calculate the signature for the request
		$contentMd5 = $this->calculateContentMd5($params);
		if ($contentMd5 !== '') {
			$headers[] = 'Content-MD5: ' . $contentMd5;
		}

		$signature = $this->generateSignature($method, $endpoint, $contentMd5, $timestamp, $params);
		$headers[] = 'X-Tsign-Open-Ca-Signature: ' . $signature;

		var_dump(json_encode($params));
		//headers ASCII码 升序排序
		ksort($headers);

		// Set cURL options
		if ($method === 'POST') {
			curl_setopt_array($curl, [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode($params),
				CURLOPT_HTTPHEADER => $headers,
			]);
		} else {
			$url .= '?' . http_build_query($params);
			curl_setopt_array($curl, [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER => $headers,
			]);
		}

		$response = curl_exec($curl);
		if (curl_errno($curl)) {
			throw new \Exception('Request Error: ' . curl_error($curl));
		}
		curl_close($curl);

		$decodedResponse = json_decode($response, true);
		var_dump($decodedResponse);
		return $this->handleApiResponse($decodedResponse, $endpoint);
	}

	private function calculateContentMd5($params)
	{
		if (empty($params)) {
			return '';
		}

		return base64_encode(md5(json_encode($params), true));
	}

	private function generateSignature($method, $endpoint, $contentMd5, $timestamp, $params)
	{
		$pathAndParameters = $this->getPathAndParameters($endpoint, $params);

		$stringToSign = implode("\n", [
			strtoupper($method),
			'*/*', // Accept header
			$contentMd5,
			strtoupper($method) == "POST" ? 'application/json; charset=UTF-8' : '', // Content-Type header
			'', // Date header, not used in this case
			'', // Headers to include in signature, empty for simplicity
			$pathAndParameters,
		]);

		var_dump($stringToSign);

		return base64_encode(hash_hmac('sha256', $stringToSign, $this->appSecret, true));
	}

	private function getPathAndParameters($endpoint, $params)
	{
		if (empty($params)) {
			return $endpoint;
		}

		$queryString = http_build_query($params);
		return $endpoint . '?' . $queryString;
	}

	private function handleApiResponse($response, $endpoint)
	{
		if (isset($response['code']) && $response['code'] !== 0) {
			$errorMessage = isset($response['message']) ? $response['message'] : 'Unknown error';
			$errorCode = $response['code'];
			$formattedErrorMessage = $this->getFormattedErrorMessage($errorCode, $errorMessage, $endpoint);
			var_dump($formattedErrorMessage);
			exit;
			throw new \Exception($formattedErrorMessage);
		}
		return $response;
	}

	private function getFormattedErrorMessage($errorCode, $errorMessage, $endpoint)
	{
		$errorMap = $this->errorCodes;

		if (strpos($endpoint, '/v3/psn-auth-url') !== false) {
			if (isset($errorMap['personal']['psn_auth_url'][$errorCode])) {
				return sprintf($errorMap['personal']['psn_auth_url'][$errorCode], $errorMessage);
			}
		} elseif (strpos($endpoint, '/v3/persons/identity-info') !== false) {
			if (isset($errorMap['personal']['identity_info'][$errorCode])) {
				return sprintf($errorMap['personal']['identity_info'][$errorCode], $errorMessage);
			}
		} elseif (strpos($endpoint, '/v3/persons/') !== false && strpos($endpoint, '/authorized-info') !== false) {
			if (isset($errorMap['personal']['authorized_info'][$errorCode])) {
				return sprintf($errorMap['personal']['authorized_info'][$errorCode], $errorMessage);
			}
		} elseif (strpos($endpoint, '/v3/org-auth-url') !== false) {
			if (isset($errorMap['organization']['org_auth_url'][$errorCode])) {
				return sprintf($errorMap['organization']['org_auth_url'][$errorCode], $errorMessage);
			}
		} elseif (strpos($endpoint, '/v3/organizations/identity-info') !== false) {
			if (isset($errorMap['organization']['identity_info'][$errorCode])) {
				return sprintf($errorMap['organization']['identity_info'][$errorCode], $errorMessage);
			}
		} elseif (strpos($endpoint, '/v3/auth-flow/') !== false) {
			if (isset($errorMap['auth_flow'][$errorCode])) {
				return sprintf($errorMap['auth_flow'][$errorCode], $errorMessage);
			}
		}

		if (isset($errorMap[$errorCode])) {
			return sprintf($errorMap[$errorCode], $errorMessage);
		}

		return "Error Code: $errorCode - $errorMessage";
	}
	//查询企业认证
	public function getOrgIdentityInfo($orgId = null, $orgName = null, $orgIDCardNum = null, $orgIDCardType = null)
	{
		$params = [];

		if ($orgId !== null) {
			$params['orgId'] = $orgId;
		} elseif ($orgName !== null) {
			$params['orgName'] = $orgName;
		} elseif ($orgIDCardNum !== null) {
			$params['orgIDCardNum'] = $orgIDCardNum;
			if ($orgIDCardType === null) {
				throw new \Exception("orgIDCardType is required when orgIDCardNum is provided.");
			}
			$params['orgIDCardType'] = $orgIDCardType;
		} else {
			throw new \Exception("At least one of orgId, orgName, or orgIDCardNum must be provided.");
		}

		return $this->makeRequest('/v3/organizations/identity-info', $params);
	}

	//查询个人认证信息
	public function getPsnIdentityInfo($psnId = null, $psnAccount = null, $psnIDCardNum = null, $psnIDCardType = null)
	{
		$params = [];

		if ($psnId !== null) {
			$params['psnId'] = $psnId;
		} elseif ($psnAccount !== null) {
			$params['psnAccount'] = $psnAccount;
		} elseif ($psnIDCardNum !== null) {
			$params['psnIDCardNum'] = $psnIDCardNum;
			if ($psnIDCardType === null) {
				throw new \Exception("psnIDCardType is required when psnIDCardNum is provided.");
			}
			$params['psnIDCardType'] = $psnIDCardType;
		} else {
			throw new \Exception("At least one of psnId, psnAccount, or psnIDCardNum must be provided.");
		}

		return $this->makeRequest('/v3/persons/identity-info', $params);
	}

	//获取个人认证&授权页面链接
	public function getPsnAuthUrl(
		$psnAuthConfig,
		$redirectConfig = null,
		$notifyUrl = null,
		$clientType = 'ALL',
		$appScheme = null,
		$authorizeConfig = ["get_psn_identity_info", "psn_initiate_sign", "manage_psn_resource", "psn_sign_file_storage"]
	) {
		$params = [
			"psnAuthConfig" => $psnAuthConfig,
		];

		if ($authorizeConfig !== null) {
			$params["authorizeConfig"] = $authorizeConfig;
		}

		if ($redirectConfig !== null) {
			$params["redirectConfig"] = $redirectConfig;
		}

		if ($notifyUrl !== null) {
			$params["notifyUrl"] = $notifyUrl;
		}

		if ($clientType !== null) {
			$params["clientType"] = $clientType;
		}

		if ($appScheme !== null) {
			$params["appScheme"] = $appScheme;
		}

		return $this->makeRequest('/v3/psn-auth-url', $params, 'POST');
	}

	//获取组织认证&授权页面链接
	public function getOrgAuthUrl($orgAuthConfig = [
		"orgName" => "", //组织名称
		"orgInfo" => [
			"orgIDCardNum" => "",
			"orgIDCardType" => "",
			"legalRepName" => "",
			"legalRepIDCardNum" => "",
			"legalRepIDCardType" => "CRED_PSN_CH_IDCARD",
		],
	], $authorizeConfig = null, $redirectUrl = null, $notifyUrl = null, $transactorUseSeal = false, $clientType = 'ALL', $appScheme = null)
	{
		$params = [
			"orgAuthConfig" => $orgAuthConfig,
			"transactorUseSeal" => $transactorUseSeal
		];

		if ($authorizeConfig !== null) {
			$params["authorizeConfig"] = ["authorizedScopes" => $authorizeConfig];
		} else {
			// $params["authorizeConfig"] = [
			// 	"authorizedScopes" => ["get_org_identity_info", "get_psn_identity_info", "org_initiate_sign", "psn_initiate_sign", "manage_org_resource", "manage_psn_resource", "psn_sign_file_storage", "org_sign_file_storage", "org_approval_info", "use_org_order"]
			// ];
		}

		if ($redirectUrl !== null) {
			$params["redirectConfig"] = ["redirectUrl" => $redirectUrl];
		}

		if ($notifyUrl !== null) {
			$params["notifyUrl"] = $notifyUrl;
		}

		if ($clientType !== null) {
			$params["clientType"] = $clientType;
		}

		if ($appScheme !== null) {
			$params["appScheme"] = $appScheme;
		}

		return $this->makeRequest('/v3/org-auth-url', $params, 'POST');
	}

	// 查询机构授权信息
	public function getOrganizationAuthorizedInfo($orgId)
	{
		if ($orgId === null) {
			throw new \Exception("orgId is required.");
		}

		$url = "/v3/organizations/{$orgId}/authorized-info";

		return $this->makeRequest($url, [], 'GET');
	}
}
