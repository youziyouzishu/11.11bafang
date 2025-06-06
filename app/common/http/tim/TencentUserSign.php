<?php

namespace app\common\http\tim;

if (version_compare(PHP_VERSION, '5.1.2') < 0) {
	trigger_error('need php 5.1.2 or newer', E_USER_ERROR);
}

class TLSSigAPIv2
{

	private $key = false;
	private $sdkappid = 1600054212;

	/**
	 *【功能说明】用于签发 TRTC 和 IM 服务中必须要使用的 UserSig 鉴权票据
	 *
	 *【参数说明】
	 * @param string userid - 用户id，限制长度为32字节，只允许包含大小写英文字母（a-zA-Z）、数字（0-9）及下划线和连词符。
	 * @param string expire - UserSig 票据的过期时间，单位是秒，比如 86400 代表生成的 UserSig 票据在一天后就无法再使用了。
	 * @return string 签名字符串
	 * @throws \Exception
	 */

	/**
	 * Function: Used to issue UserSig that is required by the TRTC and IM services.
	 *
	 * Parameter description:
	 * @param userid - User ID. The value can be up to 32 bytes in length and contain letters (a-z and A-Z), digits (0-9), underscores (_), and hyphens (-).
	 * @param expire - UserSig expiration time, in seconds. For example, 86400 indicates that the generated UserSig will expire one day after being generated.
	 * @return string signature string
	 * @throws \Exception
	 */

	public function genUserSig($userid, $expire = 86400 * 180)
	{
		return $this->__genSig($userid, $expire, '', false);
	}

	/**
	 *【功能说明】
	 * 用于签发 TRTC 进房参数中可选的 PrivateMapKey 权限票据。
	 * PrivateMapKey 需要跟 UserSig 一起使用，但 PrivateMapKey 比 UserSig 有更强的权限控制能力：
	 *  - UserSig 只能控制某个 UserID 有无使用 TRTC 服务的权限，只要 UserSig 正确，其对应的 UserID 可以进出任意房间。
	 *  - PrivateMapKey 则是将 UserID 的权限控制的更加严格，包括能不能进入某个房间，能不能在该房间里上行音视频等等。
	 * 如果要开启 PrivateMapKey 严格权限位校验，需要在【实时音视频控制台】=>【应用管理】=>【应用信息】中打开“启动权限密钥”开关。
	 *
	 *【参数说明】
	 * @param userid - 用户id，限制长度为32字节，只允许包含大小写英文字母（a-zA-Z）、数字（0-9）及下划线和连词符。
	 * @param expire - PrivateMapKey 票据的过期时间，单位是秒，比如 86400 生成的 PrivateMapKey 票据在一天后就无法再使用了。
	 * @param roomid - 房间号，用于指定该 userid 可以进入的房间号
	 * @param privilegeMap - 权限位，使用了一个字节中的 8 个比特位，分别代表八个具体的功能权限开关：
	 *  - 第 1 位：0000 0001 = 1，创建房间的权限
	 *  - 第 2 位：0000 0010 = 2，加入房间的权限
	 *  - 第 3 位：0000 0100 = 4，发送语音的权限
	 *  - 第 4 位：0000 1000 = 8，接收语音的权限
	 *  - 第 5 位：0001 0000 = 16，发送视频的权限
	 *  - 第 6 位：0010 0000 = 32，接收视频的权限
	 *  - 第 7 位：0100 0000 = 64，发送辅路（也就是屏幕分享）视频的权限
	 *  - 第 8 位：1000 0000 = 200，接收辅路（也就是屏幕分享）视频的权限
	 *  - privilegeMap == 1111 1111 == 255 代表该 userid 在该 roomid 房间内的所有功能权限。
	 *  - privilegeMap == 0010 1010 == 42  代表该 userid 拥有加入房间和接收音视频数据的权限，但不具备其他权限。
	 */

	/**
	 * Function:
	 * Used to issue PrivateMapKey that is optional for room entry.
	 * PrivateMapKey must be used together with UserSig but with more powerful permission control capabilities.
	 *  - UserSig can only control whether a UserID has permission to use the TRTC service. As long as the UserSig is correct, the user with the corresponding UserID can enter or leave any room.
	 *  - PrivateMapKey specifies more stringent permissions for a UserID, including whether the UserID can be used to enter a specific room and perform audio/video upstreaming in the room.
	 * To enable stringent PrivateMapKey permission bit verification, you need to enable permission key in TRTC console > Application Management > Application Info.
	 *
	 * Parameter description:
	 * userid - User ID. The value can be up to 32 bytes in length and contain letters (a-z and A-Z), digits (0-9), underscores (_), and hyphens (-).
	 * roomid - ID of the room to which the specified UserID can enter.
	 * expire - PrivateMapKey expiration time, in seconds. For example, 86400 indicates that the generated PrivateMapKey will expire one day after being generated.
	 * privilegeMap - Permission bits. Eight bits in the same byte are used as the permission switches of eight specific features:
	 *  - Bit 1: 0000 0001 = 1, permission for room creation
	 *  - Bit 2: 0000 0010 = 2, permission for room entry
	 *  - Bit 3: 0000 0100 = 4, permission for audio sending
	 *  - Bit 4: 0000 1000 = 8, permission for audio receiving
	 *  - Bit 5: 0001 0000 = 16, permission for video sending
	 *  - Bit 6: 0010 0000 = 32, permission for video receiving
	 *  - Bit 7: 0100 0000 = 64, permission for substream video sending (screen sharing)
	 *  - Bit 8: 1000 0000 = 200, permission for substream video receiving (screen sharing)
	 *  - privilegeMap == 1111 1111 == 255: Indicates that the UserID has all feature permissions of the room specified by roomid.
	 *  - privilegeMap == 0010 1010 == 42: Indicates that the UserID has only the permissions to enter the room and receive audio/video data.
	 */

	public function genPrivateMapKey($userid, $expire, $roomid, $privilegeMap)
	{
		$userbuf = $this->__genUserBuf($userid, $roomid, $expire, $privilegeMap, 0, '');
		return $this->__genSig($userid, $expire, $userbuf, true);
	}
	/**
	 *【功能说明】
	 * 用于签发 TRTC 进房参数中可选的 PrivateMapKey 权限票据。
	 * PrivateMapKey 需要跟 UserSig 一起使用，但 PrivateMapKey 比 UserSig 有更强的权限控制能力：
	 *  - UserSig 只能控制某个 UserID 有无使用 TRTC 服务的权限，只要 UserSig 正确，其对应的 UserID 可以进出任意房间。
	 *  - PrivateMapKey 则是将 UserID 的权限控制的更加严格，包括能不能进入某个房间，能不能在该房间里上行音视频等等。
	 * 如果要开启 PrivateMapKey 严格权限位校验，需要在【实时音视频控制台】=>【应用管理】=>【应用信息】中打开“启动权限密钥”开关。
	 *
	 *【参数说明】
	 * @param userid - 用户id，限制长度为32字节，只允许包含大小写英文字母（a-zA-Z）、数字（0-9）及下划线和连词符。
	 * @param expire - PrivateMapKey 票据的过期时间，单位是秒，比如 86400 生成的 PrivateMapKey 票据在一天后就无法再使用了。
	 * @param roomstr - 房间号，用于指定该 userid 可以进入的房间号
	 * @param privilegeMap - 权限位，使用了一个字节中的 8 个比特位，分别代表八个具体的功能权限开关：
	 *  - 第 1 位：0000 0001 = 1，创建房间的权限
	 *  - 第 2 位：0000 0010 = 2，加入房间的权限
	 *  - 第 3 位：0000 0100 = 4，发送语音的权限
	 *  - 第 4 位：0000 1000 = 8，接收语音的权限
	 *  - 第 5 位：0001 0000 = 16，发送视频的权限
	 *  - 第 6 位：0010 0000 = 32，接收视频的权限
	 *  - 第 7 位：0100 0000 = 64，发送辅路（也就是屏幕分享）视频的权限
	 *  - 第 8 位：1000 0000 = 200，接收辅路（也就是屏幕分享）视频的权限
	 *  - privilegeMap == 1111 1111 == 255 代表该 userid 在该 roomid 房间内的所有功能权限。
	 *  - privilegeMap == 0010 1010 == 42  代表该 userid 拥有加入房间和接收音视频数据的权限，但不具备其他权限。
	 */

	/**
	 * Function:
	 * Used to issue PrivateMapKey that is optional for room entry.
	 * PrivateMapKey must be used together with UserSig but with more powerful permission control capabilities.
	 *  - UserSig can only control whether a UserID has permission to use the TRTC service. As long as the UserSig is correct, the user with the corresponding UserID can enter or leave any room.
	 *  - PrivateMapKey specifies more stringent permissions for a UserID, including whether the UserID can be used to enter a specific room and perform audio/video upstreaming in the room.
	 * To enable stringent PrivateMapKey permission bit verification, you need to enable permission key in TRTC console > Application Management > Application Info.
	 *
	 * Parameter description:
	 * @param userid - User ID. The value can be up to 32 bytes in length and contain letters (a-z and A-Z), digits (0-9), underscores (_), and hyphens (-).
	 * @param roomstr - ID of the room to which the specified UserID can enter.
	 * @param expire - PrivateMapKey expiration time, in seconds. For example, 86400 indicates that the generated PrivateMapKey will expire one day after being generated.
	 * @param privilegeMap - Permission bits. Eight bits in the same byte are used as the permission switches of eight specific features:
	 *  - Bit 1: 0000 0001 = 1, permission for room creation
	 *  - Bit 2: 0000 0010 = 2, permission for room entry
	 *  - Bit 3: 0000 0100 = 4, permission for audio sending
	 *  - Bit 4: 0000 1000 = 8, permission for audio receiving
	 *  - Bit 5: 0001 0000 = 16, permission for video sending
	 *  - Bit 6: 0010 0000 = 32, permission for video receiving
	 *  - Bit 7: 0100 0000 = 64, permission for substream video sending (screen sharing)
	 *  - Bit 8: 1000 0000 = 200, permission for substream video receiving (screen sharing)
	 *  - privilegeMap == 1111 1111 == 255: Indicates that the UserID has all feature permissions of the room specified by roomid.
	 *  - privilegeMap == 0010 1010 == 42: Indicates that the UserID has only the permissions to enter the room and receive audio/video data.
	 */

	public function genPrivateMapKeyWithStringRoomID($userid, $expire, $roomstr, $privilegeMap)
	{
		$userbuf = $this->__genUserBuf($userid, 0, $expire, $privilegeMap, 0, $roomstr);
		return $this->__genSig($userid, $expire, $userbuf, true);
	}

	public function __construct($sdkappid, $key)
	{
		$this->sdkappid = $sdkappid;
		$this->key = $key;
	}

	/**
	 * 用于 url 的 base64 encode
	 * '+' => '*', '/' => '-', '=' => '_'
	 * @param string $string 需要编码的数据
	 * @return string 编码后的base64串，失败返回false
	 * @throws \Exception
	 */

	/**
	 * base64 encode for url
	 * '+' => '*', '/' => '-', '=' => '_'
	 * @param string $string data to be encoded
	 * @return string The encoded base64 string, returns false on failure
	 * @throws \Exception
	 */
	private function base64_url_encode($string)
	{
		static $replace = array('+' => '*', '/' => '-', '=' => '_');
		$base64 = base64_encode($string);
		if ($base64 === false) {
			throw new \Exception('base64_encode error');
		}
		return str_replace(array_keys($replace), array_values($replace), $base64);
	}

	/**
	 * 用于 url 的 base64 decode
	 * '+' => '*', '/' => '-', '=' => '_'
	 * @param string $base64 需要解码的base64串
	 * @return string 解码后的数据，失败返回false
	 * @throws \Exception
	 */

	/**
	 * base64 decode for url
	 * '+' => '*', '/' => '-', '=' => '_'
	 * @param string $base64 base64 string to be decoded
	 * @return string Decoded data, return false on failure
	 * @throws \Exception
	 */
	private function base64_url_decode($base64)
	{
		static $replace = array('+' => '*', '/' => '-', '=' => '_');
		$string = str_replace(array_values($replace), array_keys($replace), $base64);
		$result = base64_decode($string);
		if ($result == false) {
			throw new \Exception('base64_url_decode error');
		}
		return $result;
	}
	/**
	 * TRTC业务进房权限加密串使用用户定义的userbuf
	 * @brief 生成 userbuf
	 * @param account 用户名
	 * @param dwSdkappid sdkappid
	 * @param dwAuthID  数字房间号
	 * @param dwExpTime 过期时间：该权限加密串的过期时间. 过期时间 = now+dwExpTime
	 * @param dwPrivilegeMap 用户权限，255表示所有权限
	 * @param dwAccountType 用户类型, 默认为0
	 * @param roomStr 字符串房间号
	 * @return userbuf string  返回的userbuf
	 */

	/**
	 * User-defined userbuf is used for the encrypted string of TRTC service entry permission
	 * @brief generate userbuf
	 * @param account username
	 * @param dwSdkappid sdkappid
	 * @param dwAuthID  digital room number
	 * @param dwExpTime Expiration time: The expiration time of the encrypted string of this permission. Expiration time = now+dwExpTime
	 * @param dwPrivilegeMap User permissions, 255 means all permissions
	 * @param dwAccountType User type, default is 0
	 * @param roomStr String room number
	 * @return userbuf string  returned userbuf
	 */

	private function __genUserBuf($account, $dwAuthID, $dwExpTime, $dwPrivilegeMap, $dwAccountType, $roomStr)
	{

		//cVer  unsigned char/1 版本号，填0
		if ($roomStr == '')
			$userbuf = pack('C1', '0');
		else
			$userbuf = pack('C1', '1');

		$userbuf .= pack('n', strlen($account));
		//wAccountLen   unsigned short /2   第三方自己的帐号长度
		$userbuf .= pack('a' . strlen($account), $account);
		//buffAccount   wAccountLen 第三方自己的帐号字符
		$userbuf .= pack('N', $this->sdkappid);
		//dwSdkAppid    unsigned int/4  sdkappid
		$userbuf .= pack('N', $dwAuthID);
		//dwAuthId  unsigned int/4  群组号码/音视频房间号
		$expire = $dwExpTime + time();
		$userbuf .= pack('N', $expire);
		//dwExpTime unsigned int/4  过期时间 （当前时间 + 有效期（单位：秒，建议300秒））
		$userbuf .= pack('N', $dwPrivilegeMap);
		//dwPrivilegeMap unsigned int/4  权限位
		$userbuf .= pack('N', $dwAccountType);
		//dwAccountType  unsigned int/4
		if ($roomStr != '') {
			$userbuf .= pack('n', strlen($roomStr));
			//roomStrLen   unsigned short /2   字符串房间号长度
			$userbuf .= pack('a' . strlen($roomStr), $roomStr);
			//roomStr   roomStrLen 字符串房间号
		}
		return $userbuf;
	}
	/**
	 * 使用 hmac sha256 生成 sig 字段内容，经过 base64 编码
	 * @param $identifier 用户名，utf-8 编码
	 * @param $curr_time 当前生成 sig 的 unix 时间戳
	 * @param $expire 有效期，单位秒
	 * @param $base64_userbuf base64 编码后的 userbuf
	 * @param $userbuf_enabled 是否开启 userbuf
	 * @return string base64 后的 sig
	 */

	/**
	 * Use hmac sha256 to generate sig field content, base64 encoded
	 * @param $identifier Username, utf-8 encoded
	 * @param $curr_time The unix timestamp of the current generated sig
	 * @param $expire Validity period, in seconds
	 * @param $base64_userbuf base64 encoded userbuf
	 * @param $userbuf_enabled 是No enable userbuf
	 * @return string sig after base64
	 */
	private function hmacsha256($identifier, $curr_time, $expire, $base64_userbuf, $userbuf_enabled)
	{
		$content_to_be_signed = 'TLS.identifier:' . $identifier . "\n"
			. 'TLS.sdkappid:' . $this->sdkappid . "\n"
			. 'TLS.time:' . $curr_time . "\n"
			. 'TLS.expire:' . $expire . "\n";
		if (true == $userbuf_enabled) {
			$content_to_be_signed .= 'TLS.userbuf:' . $base64_userbuf . "\n";
		}
		return base64_encode(hash_hmac('sha256', $content_to_be_signed, $this->key, true));
	}

	/**
	 * 生成签名。
	 *
	 * @param $identifier 用户账号
	 * @param int $expire 过期时间，单位秒，默认 180 天
	 * @param $userbuf base64 编码后的 userbuf
	 * @param $userbuf_enabled 是否开启 userbuf
	 * @return string 签名字符串
	 * @throws \Exception
	 */

	/**
	 * Generate signature.
	 *
	 * @param $identifier user account
	 * @param int $expire Expiration time, in seconds, default 180 days
	 * @param $userbuf base64 encoded userbuf
	 * @param $userbuf_enabled Whether to enable userbuf
	 * @return string signature string
	 * @throws \Exception
	 */
	private function __genSig($identifier, $expire, $userbuf, $userbuf_enabled)
	{
		$curr_time = time();
		$sig_array = array(
			'TLS.ver' => '2.0',
			'TLS.identifier' => strval($identifier),
			'TLS.sdkappid' => intval($this->sdkappid),
			'TLS.expire' => intval($expire),
			'TLS.time' => intval($curr_time)
		);

		$base64_userbuf = '';
		if (true == $userbuf_enabled) {
			$base64_userbuf = base64_encode($userbuf);
			$sig_array['TLS.userbuf'] = strval($base64_userbuf);
		}

		$sig_array['TLS.sig'] = $this->hmacsha256($identifier, $curr_time, $expire, $base64_userbuf, $userbuf_enabled);
		if ($sig_array['TLS.sig'] === false) {
			throw new \Exception('base64_encode error');
		}
		$json_str_sig = json_encode($sig_array);
		if ($json_str_sig === false) {
			throw new \Exception('json_encode error');
		}
		$compressed = gzcompress($json_str_sig);
		if ($compressed === false) {
			throw new \Exception('gzcompress error');
		}
		return $this->base64_url_encode($compressed);
	}

	/**
	 * 验证签名。
	 *
	 * @param string $sig 签名内容
	 * @param string $identifier 需要验证用户名，utf-8 编码
	 * @param int $init_time 返回的生成时间，unix 时间戳
	 * @param int $expire_time 返回的有效期，单位秒
	 * @param string $userbuf 返回的用户数据
	 * @param string $error_msg 失败时的错误信息
	 * @return boolean 验证是否成功
	 * @throws \Exception
	 */

	/**
	 * Verify signature.
	 *
	 * @param string $sig Signature content
	 * @param string $identifier Need to authenticate user name, utf-8 encoding
	 * @param int $init_time Returned generation time, unix timestamp
	 * @param int $expire_time Return the validity period, in seconds
	 * @param string $userbuf returned user data
	 * @param string $error_msg error message on failure
	 * @return boolean Verify success
	 * @throws \Exception
	 */

	private function __verifySig($sig, $identifier, &$init_time, &$expire_time, &$userbuf, &$error_msg)
	{
		try {
			$error_msg = '';
			$compressed_sig = $this->base64_url_decode($sig);
			$pre_level = error_reporting(E_ERROR);
			$uncompressed_sig = gzuncompress($compressed_sig);
			error_reporting($pre_level);
			if ($uncompressed_sig === false) {
				throw new \Exception('gzuncompress error');
			}
			$sig_doc = json_decode($uncompressed_sig);
			if ($sig_doc == false) {
				throw new \Exception('json_decode error');
			}
			$sig_doc = (array)$sig_doc;
			if ($sig_doc['TLS.identifier'] !== $identifier) {
				throw new \Exception("identifier dosen't match");
			}
			if ($sig_doc['TLS.sdkappid'] != $this->sdkappid) {
				throw new \Exception("sdkappid dosen't match");
			}
			$sig = $sig_doc['TLS.sig'];
			if ($sig == false) {
				throw new \Exception('sig field is missing');
			}

			$init_time = $sig_doc['TLS.time'];
			$expire_time = $sig_doc['TLS.expire'];

			$curr_time = time();
			if ($curr_time > $init_time + $expire_time) {
				throw new \Exception('sig expired');
			}

			$userbuf_enabled = false;
			$base64_userbuf = '';
			if (isset($sig_doc['TLS.userbuf'])) {
				$base64_userbuf = $sig_doc['TLS.userbuf'];
				$userbuf = base64_decode($base64_userbuf);
				$userbuf_enabled = true;
			}
			$sigCalculated = $this->hmacsha256($identifier, $init_time, $expire_time, $base64_userbuf, $userbuf_enabled);

			if ($sig != $sigCalculated) {
				throw new \Exception('verify failed');
			}

			return true;
		} catch (\Exception $ex) {
			$error_msg = $ex->getMessage();
			return false;
		}
	}

	/**
	 * 带 userbuf 验证签名。
	 *
	 * @param string $sig 签名内容
	 * @param string $identifier 需要验证用户名，utf-8 编码
	 * @param int $init_time 返回的生成时间，unix 时间戳
	 * @param int $expire_time 返回的有效期，单位秒
	 * @param string $error_msg 失败时的错误信息
	 * @return boolean 验证是否成功
	 * @throws \Exception
	 */

	/**
	 * Verify signature with userbuf.
	 *
	 * @param string $sig Signature content
	 * @param string $identifier Need to authenticate user name, utf-8 encoding
	 * @param int $init_time Returned generation time, unix timestamp
	 * @param int $expire_time Return the validity period, in seconds
	 * @param string $error_msg error message on failure
	 * @return boolean Verify success
	 * @throws \Exception
	 */
	public function verifySig($sig, $identifier, &$init_time, &$expire_time, &$error_msg)
	{
		$userbuf = '';
		return $this->__verifySig($sig, $identifier, $init_time, $expire_time, $userbuf, $error_msg);
	}

	/**
	 * 验证签名
	 * @param string $sig 签名内容
	 * @param string $identifier 需要验证用户名，utf-8 编码
	 * @param int $init_time 返回的生成时间，unix 时间戳
	 * @param int $expire_time 返回的有效期，单位秒
	 * @param string $userbuf 返回的用户数据
	 * @param string $error_msg 失败时的错误信息
	 * @return boolean 验证是否成功
	 * @throws \Exception
	 */

	/**
	 * Verify signature
	 * @param string $sig Signature content
	 * @param string $identifier Need to authenticate user name, utf-8 encoding
	 * @param int $init_time Returned generation time, unix timestamp
	 * @param int $expire_time Return the validity period, in seconds
	 * @param string $userbuf returned user data
	 * @param string $error_msg error message on failure
	 * @return boolean Verify success
	 * @throws \Exception
	 */
	public function verifySigWithUserBuf($sig, $identifier, &$init_time, &$expire_time, &$userbuf, &$error_msg)
	{
		return $this->__verifySig($sig, $identifier, $init_time, $expire_time, $userbuf, $error_msg);
	}
}


class TencentUserSign
{
	public static function Computer($user_id, $expire = 86400 * 365 * 3)
	{
		$api = new TLSSigAPIv2(1600054212, '1892fc755a0d24fdd042e4c1f8f8cdde3ba0e7c856452e4a29bdd0bc7515c999');
		$sig = $api->genUserSig($user_id, $expire);
		return $sig;
	}
}
