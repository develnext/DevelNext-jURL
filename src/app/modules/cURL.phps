<?php
/*
 * Модуль для поддержки синтаксиса cURL
 */

namespace {
	use php\lib\Str;
	
	if(!function_exists('curl_init')){

		function curl_init($url = NULL){
			return new \app\modules\jURL($url);
		}

		function curl_setopt(\app\modules\jURL $ch, $key, $value){

			$reKeys = [
				'CURLOPT_URL' => 'url',
				'CURLOPT_CONNECTTIMEOUT' => 'connectTimeout',
				'CURLOPT_CONNECTTIMEOUT_MS' => 'connectTimeout',
				'CURLOPT_TIMEOUT' => 'readTimeout',
				'CURLOPT_TIMEOUT_MS' => 'readTimeout',
				'CURLOPT_CUSTOMREQUEST' => 'requestMethod',
				'CURLOPT_POSTFIELDS' => 'postData', // postFiles //
				'CURLOPT_POST' => 'requestMethod',
				'CURLOPT_PUT' => 'requestMethod',
				'CURLOPT_GET' => 'requestMethod',
				'CURLOPT_REFERER' => 'httpReferer',
				'CURLOPT_AUTOREFERER' => 'autoReferer',
				'CURLOPT_COOKIEFILE' => 'cookieFile',
				'CURLOPT_COOKIEJAR' => 'cookieFile',
				'CURLOPT_USERAGENT' => 'userAgent',
				'CURLOPT_HEADER' => 'returnHeaders',
				'CURLOPT_FOLLOWLOCATION' => 'followRedirects',
				'CURLOPT_HTTPHEADER' => 'httpHeader',
				'CURLOPT_USERPWD' => 'basicAuth',
				'CURLOPT_PROXY' => 'proxy',
				'CURLOPT_PROXYTYPE' => 'proxyType',
				'CURLOPT_PROGRESSFUNCTION' => 'progressFunction',
				'CURLOPT_FILE' => 'fileStream',
				'CURLOPT_BUFFERSIZE' => 'bufferLength',
				'CURLOPT_INFILE' => 'bodyFile',
			];
			
			$jKey = isset($reKeys[$key]) ? $reKeys[$key] : NULL;
			
			if($key == 'CURLOPT_POST' and $value === true){
				$value = 'POST';
			}

			elseif($key == 'CURLOPT_GET' and $value === true){
				$value = 'GET';
			}

			elseif($key == 'CURLOPT_PUT' and $value === true){
				$value = 'PUT';
			}

			elseif($key == 'CURLOPT_HTTPHEADER'){
				$headers = [];
				foreach ($value as $h) {
					$t = Str::Split($h, ':', 2);
					$headers[] = [
						Str::Trim( $t[0] ),
						Str::Trim( $t[1] ),
					];
				}

				$value = $headers;
			}

			elseif($key == 'CURLOPT_POST' AND $value === true){
				return $ch->setOpt('requestMethod', 'POST');
			}

			elseif($key == 'CURLOPT_CONNECTTIMEOUT' OR $key == 'CURLOPT_TIMEOUT'){
				$value = $value * 1000;
			}

			elseif($key == 'CURLOPT_POSTFIELDS' AND is_array($value)){

				$str = [];
				$files = [];
				foreach($value as $k=>$v){
					if(Str::Sub($v, 0, 1) == '@')$files[$k] = Str::Sub($v, 1, Str::Length($v));
					else $str[$k] = $v;
				}

				if(sizeof($files) > 0) return $ch->setOpt('postFiles', $files);
				else $value = $str;
			}

			elseif($key == 'CURLOPT_PROXYTYPE'){
				$proxyTypes = [
					'CURLPROXY_HTTP' => 'HTTP',
					'CURLPROXY_SOCKS5' => 'SOCKS'
				];

				$value = (isset($proxyTypes[$value]) ? $proxyTypes[$value] : $value);
			}
			
			$ch->setOpt($jKey, $value);
		}

		function curl_setopt_array(\app\modules\jURL $ch, $array){
			foreach($array as $k=>$v){
				curl_setopt($ch, $k, $v);
			}
		} 

		function curl_exec(\app\modules\jURL $ch){
			return $ch->exec();
		}

		function curl_exec_async(\app\modules\jURL $ch, $callback = null){
			return $ch->aSyncExec($callback);
		}

		function curl_error(\app\modules\jURL $ch){
			return ($ch->getError()['error'] === NULL) ? $ch->getError()['exception'] : $ch->getError()['error'] ;
		}

		function curl_errno(\app\modules\jURL $ch){
			return $ch->getError()['code'];
		}

		function curl_getinfo(\app\modules\jURL $ch){
			return $ch->getConnectionInfo();
		}
		
		function curl_close(\app\modules\jURL $ch){
			return $ch->destroyConnection();
		}
	}
}