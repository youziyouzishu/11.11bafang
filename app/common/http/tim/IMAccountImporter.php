<?php

namespace app\common\http\tim;

use app\common\http\esign\comm\EsignLogHelper;
use Exception;

class IMAccountImporter
{
	public static function importAccounts($accounts)
	{
		$url = 'https://console.tim.qq.com' . '/v4/im_open_login_svc/multiaccount_import';
		$data = [
			'sdkappid' => 1600054212,
			'identifier' => "administrator",
			'usersig' => "eJwtjLEOgjAURf*lq4a0hVeVxIGkxqULwcWx2Ja8CNi0jdEY-10ExnvOzfmQi2qypw2kJDyjZDtvNHZM6HDG2gw4YkxBp0dYD9HctfdoSMkEpRQKzvhi7MtjsBMHAD6phSYc-mzHxYEDBVgr2E39jai7mLNCuv2t7l1sOqnq-upbLdP7bCvb5lI5w06uOpLvD0mlNJo_",
			'random' => rand(0, 4294967295),
			'contenttype' => 'json',

		];

		$url .= "?" . http_build_query($data);
		$jsonData = json_encode(['AccountList' => $accounts], true);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json'
		]);

		EsignLogHelper::writeLog('start im register ', "txim");
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($httpCode == 200) {
			$responseData = json_decode($response, true);
			return $responseData;
		} else {
			EsignLogHelper::writeLog('HTTP status code ' . $httpCode, "txim");
			// 处理错误情况
			throw new Exception('HTTP status code ' . $httpCode);
		}
	}

	public static function AddOne($user_id = "", $nickname  = "", $face = "")
	{
		return self::importAccounts([['UserID' => $user_id, 'Nick' => $nickname, 'FaceUrl' =>  $face ?  $face : "https://im.sdk.qcloud.com/download/tuikit-resource/avatar/avatar_3.png"]]);
	}
}
