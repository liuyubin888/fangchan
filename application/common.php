<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 生成随机字符串
 * @param $length  生成长度
 * @param int $numeric  0 有英文 、 1 纯数字
 * @return string
 */
function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	if ($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length--;
	}
	$max = strlen($seed) - 1;
	for ($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

function scriptname() {
	$script_name = basename($_SERVER['SCRIPT_FILENAME']);
	if(basename($_SERVER['SCRIPT_NAME']) === $script_name) {
		$script_name = $_SERVER['SCRIPT_NAME'];
	} else {
		if(basename($_SERVER['PHP_SELF']) === $script_name) {
			$script_name = $_SERVER['PHP_SELF'];
		} else {
			if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $script_name) {
				$script_name = $_SERVER['ORIG_SCRIPT_NAME'];
			} else {
				if(($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
					$script_name = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $script_name;
				} else {
					if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
						$script_name = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
					} else {
						$script_name = 'unknown';
					}
				}
			}
		}
	}
	return $script_name;
}

function createSiteUrl(){
	$sitepath = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
    $siteroot = htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . $sitepath);
    $urls = parse_url($siteroot);
    $scriptname = htmlspecialchars(scriptname());
    $query_string = empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING'];
    return $siteurl = $urls['scheme'].'://'.$urls['host'].$scriptname.$query_string;
}