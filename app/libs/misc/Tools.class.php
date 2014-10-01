<?php

namespace libs\misc;

class Tools {

	public static function Test() {
		//echo "Hello World";
	}

	public static function CompileFiles() {

		$path = \XENECO_PATH;

		$ignore = array('.htaccess', 'error_log', 'cgi-bin', 'php.ini', '.ftpquota', '.svn', 'swiftmailer');

		$dirTree = self::getDirectory($path, $ignore);

		foreach ($dirTree as $dir => $files) {
			foreach ($files as $file) {
				$a = $dir . DIRECTORY_SEPARATOR . $file;
				$a = str_replace('/', DIRECTORY_SEPARATOR, $a);
				$a = str_replace('\\', DIRECTORY_SEPARATOR, $a);

				$a = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $a);

				$all[] = $a;
			}
		}

		$hooks = array();

		foreach ($all as $file) {

			set_time_limit(30);

			$d = self::getContexts($file);

			//echo "$file\n=========================\n\n".print_r($d,1)."\n";

			for ($i = 0; $i < count($d['classes']); $i++) {

				$class = $d['classes'][$i];

				$lines.='$classlist[\'' . trim($prefix . $d['namespaces'][0] . ($d['namespaces'][0] == '' ? '' : '\\') . $class, '\\') . '\'] = \'' . $file . '\';' . "\n";
			}

			for ($i = 0; $i < count($d['interfaces']); $i++) {
				$interface = $d['interfaces'][$i];

				$lines.='$classlist[\'' . trim($prefix . $d['namespaces'][0] . ($d['namespaces'][0] == '' ? '' : '\\') . $interface, '\\') . '\'] = \'' . $file . '\';' . "\n";
			}

			for ($i = 0; $i < count($d['traits']); $i++) {
				$trait = $d['traits'][$i];

				$lines.='$classlist[\'' . trim($prefix . $d['namespaces'][0] . ($d['namespaces'][0] == '' ? '' : '\\') . $trait, '\\') . '\'] = \'' . $file . '\';' . "\n";
			}

			for ($i = 0; $i < count($d['plugins']); $i++) {
				$hooks[$d['plugins'][$i]] ++;
			}
		}

		$h = array_keys($hooks);
		sort($h);

		file_put_contents(\XENECO_PATH . 'class-list.php', "<?php\n\n" . $lines);
		file_put_contents(\XENECO_PATH . 'hook-list.txt', implode("\n", $h));

		//echo "class list written";
	}

	public static function getDirectory($path = '.', $ignore = '') {
		$dirTree = array();
		$dirTreeTemp = array();
		$ignore[] = '.';
		$ignore[] = '..';

		$dh = @opendir($path);

		while (false !== ($file = readdir($dh))) {

			if (!in_array($file, $ignore)) {
				if (!is_dir("$path/$file")) {
					$dirTree["$path"][] = $file;
				} else {
					$dirTreeTemp = self::getDirectory("$path/$file", $ignore);
					if (is_array($dirTreeTemp)) {
						$dirTree = array_merge($dirTree, $dirTreeTemp);
					}
				}
			}
		}
		closedir($dh);
		return $dirTree;
	}

	public static function getContexts($path) {

		$c = file_get_contents($path);

		//echo "Scanning File ".$path."....\n";

		$a = token_get_all($c);

		for ($i = 0; $i < count($a); $i++) {

			if (strtolower($a[$i][1]) == 'namespace') {
				$j = 1;
				$namespace = '';
				while (true) {
					if (trim($a[$i + $j][1]) == '') {
						if ($j != 1) {
							break;
						}
					}

					$namespace.=$a[$i + $j][1];
					$j++;
				}
				$namespaces[] = trim($namespace);
				$i+=$j;
				//echo "Logging Nmaespace ".$namespace.".....\n";
			}

			if (strtolower($a[$i][1]) == 'class') {
				$j = 1;
				$class = '';
				while (true) {
					if (trim($a[$i + $j][1]) == '') {
						if ($j != 1) {
							break;
						}
					}

					$class.=$a[$i + $j][1];
					$j++;
				}

				$classes[] = trim($class);
				$i+=$j;
				//echo "Logging Class ".$class.".....\n";
			}

			if (strtolower($a[$i][1]) == 'interface') {
				$j = 1;
				$interface = '';
				while (true) {
					if (trim($a[$i + $j][1]) == '') {
						if ($j != 1) {
							break;
						}
					}

					$interface.=$a[$i + $j][1];
					$j++;
				}

				$interfaces[] = trim($interface);
				$i+=$j;
				//echo "Logging Interface ".$interface.".....\n";
			}

			if (strtolower($a[$i][1]) == 'trait') {
				$j = 1;
				$trait = '';
				while (true) {
					if (trim($a[$i + $j][1]) == '') {
						if ($j != 1) {
							break;
						}
					}

					$trait.=$a[$i + $j][1];
					$j++;
				}

				$traits[] = trim($trait);
				$i+=$j;
				//echo "Logging Trait ".$trait.".....\n";
			}
		}

		$ps = preg_split("/DoPlugins\('|DoPlugins\(\"/", $c);

		for ($i = 1; $i < count($ps); $i++) {

			list($pi, $t) = preg_split("/'|\"/", $ps[$i]);

			if (strpos($pi, "==") > -1) {
				;
			} else {
				$plugins[] = $pi;
			}
		}

		return array('namespaces' => $namespaces, 'classes' => $classes, 'interfaces' => $interfaces, "traits" => $traits, "plugins" => $plugins);
	}

	public static function getContext() {

		$req = explode(".", $_SERVER['SERVER_NAME']);

		return $_GET['context'] == '' ? $req[0] : $_GET['context'];
	}

	public static function getContextUnid($context) {

		return "ZEST_MAILER";
	}

	public static function getRequest() {

		$str = $_SERVER['QUERY_STRING'];

		$str = preg_replace("/_dc=[0-9]+/", "", $str);

		$req = explode("/", $str);
		$module = array_shift($req);
		$action = array_shift($req);

		for ($i = 0; $i < count($req); $i+=2) {
			$_GET[$req[$i]] = $_GET[$i + 1];
		}

		return $_GET;
	}

	public static function generatePassword($len = 8, $selection = 'lower', $removeConfusing = 1) {

		$lower = 'abcdefghijklmnopqrstuvwxyz';
		$upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$number = '0123456789';

		$confusing = array('0', '1', '2', '5', 'i', 'l', 'o', 's', 'z', 'I', 'L', 'O', 'S', 'Z');

		switch ($selection) {
			case 'default':
				$str = $lower . $upper . $number;
				break;
			case 'lower':
				$str = $lower . $number;
				break;
			case 'upper':
				$str = $upper . $number;
				break;
			case 'alpha':
				$str = $lower . $number;
				break;
			default:
				$str = $lower . $upper . $number;
				break;
		}

		if ($removeConfusing > 0) {
			$str = str_replace($confusing, '', $str);
		}

		$pw = "";

		for ($i = 0; $i < $len; $i++) {
			$pw.=$str[rand(0, strlen($str) - 1)];
		}

		return $pw;
	}

	public static function hashPassword($plain) {
		//old
		//$hash = sha1($password.md5($password))."-".md5($password.sha1($password));
		//return $hash;
		//new
		$password = new \libs\misc\password;
		$hashed = $password->getHashToStore($plain);
		return $hashed;
	}

	public static function hashPasswordOld($password) {
		//old
		$hash = sha1($password . md5($password)) . "-" . md5($password . sha1($password));
		return $hash;
	}

	public static function encryptStr($msg, $key) {
		$c = new \libs\misc\Crypto;
		return $c->encrypt($msg, $key);
	}

	public static function decryptStr($msg, $key) {
		$c = new \libs\misc\Crypto;
		return $c->decrypt($msg, $key);
	}

	public static function camel_to_title($str) {
		return
			trim(
			ucwords(
				strtolower(
					preg_replace(
						'/([0-9]+)|([A-Z])/', ' $0', $str
					)
				)
			)
		);
	}

	public static function to_camel_case($str) {

		$str = str_replace('_', ' ', $str);
		$str = strtolower($str);
		$str = ucwords($str);
		$str = str_replace(' ', '', $str);



		$str[0] = strtolower($str[0]);

		return $str;
	}

	public static function is_unid($str) {
		$pattern = "/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/";

		return preg_match($pattern, $str) ? true : false;
	}

	public static function is_email($str, &$email = false) {

		if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
			$email = $str;
			return true;
		}

		$str = filter_var($str, FILTER_SANITIZE_EMAIL);

		if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
			$email = $str;
			return false;
		}

		return false;
	}

	public static function is_url($str, $url = false) {
		if (filter_var($str, FILTER_VALIDATE_URL)) {
			$email = $str;
			return true;
		}

		$str = filter_var($str, FILTER_SANITIZE_URL);

		if (filter_var($str, FILTER_VALIDATE_URL)) {
			$email = $str;
			return false;
		}

		return false;
	}

	public static function is_ip($str) {
		return filter_var($str, FILTER_VALIDATE_IP);
	}

	public static function json_to_ext($input) {
		if (is_array($input)) {
			$str = json_encode($input);
		} else {
			$str = $input;
		}

		$pattern = "/\"[a-zA-Z0-9]+\"\:/";

		preg_match_all($pattern, $str, $matches);

		$find = $replace = array();

		foreach ($matches[0] as $match) {
			$f = $match;
			$r = str_replace('"', '', $match);

			if (!in_array($f, $find)) {
				$find[] = $f;
			}

			if (!in_array($r, $replace)) {
				$replace[] = $r;
			}
		}

		return str_replace($find, $replace, $str);
	}

	public static function isAssoc($arr) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	public static function setCache($key, $data, $ttl = 3600) {
		return false;
		apc_store($key, $data, $ttl);
	}

	public static function getCache($key) {
		return false;
		return apc_fetch($key);
	}

	public static function html_escape($raw_input) {
		return htmlspecialchars($raw_input, ENT_QUOTES | ENT_HTML401, 'UTF-8');
	}

	public static function array2object($array) {
		return json_decode(json_encode($arr));
	}

	public static function object2array($object) {
		return json_decode(json_encode($object), 1);
	}

	public static function cleanHtml($html, $attr_black_list = false, $elem_black_list = false) {
		if (!$attr_black_list || !is_array($attr_black_list)) {
			$attr_black_list = ['onclick'];
		}

		if (!$elem_black_list || !is_array($elem_black_list)) {
			$elem_black_list = ['script', 'iframe'];
		}

		$remove_elems = [];

		$dom = new \DOMDocument();

		@$dom->loadHTML("<html><body>" . $html . "</body></html>");

		$els = $dom->getElementsByTagName('*');


		foreach ($els as $el) {

			foreach ($attr_black_list as $attr) {
				if ($el->hasAttribute($attr)) {
					$el->removeAttribute($attr);
				}
			}

			foreach ($elem_black_list as $elem) {
				if (strtolower($el->nodeName) == $elem) {
					$remove_elems[] = $el;
				}
			}
		}

		foreach ($remove_elems as $r) {
			$r->parentNode->removeChild($r);
		}

		$clean = $dom->saveHtml();

		$tidy_config = [
		    'clean' => true,
		    'output-html' => true,
		    'bare' => true,
		    'drop-proprietary-attributes' => false,
		    'fix-uri' => true,
		    'merge-spans' => false, //ensures editor can work
		    'join-styles' => false,
		    'indent' => true,
		    'char-encoding' => 'utf8',
		    'force-output' => true,
		    //'quiet'		=>	true,
		    'tidy-mark' => false
		];

		//$tidy = tidy_parse_string($clean,$tidy_config,'UTF8');
		//$tidy->cleanRepair();
		//$clean = (string) $tidy;
		//$fb = new FirePHP();
		//$fb->fb($clean);

		list($start, $trash) = explode("</body>", $clean);

		list($trash, $return) = explode("<body>", $start);

		return $return;
	}

	public static function getSettings($domain = false) {

		$sql = "
			SELECT
				*
			FROM
				`domain_setting`
			WHERE
				`domain_id` = :domain_id
				AND
				`key` IN (
					SELECT
						`key`
					FROM
						setting_key
				)
			;
		";

		$args['domain_id'] = ($domain ? $domain : $_SESSION['domain']['id']);

		\libs\pdo\DB::Load()->Execute($sql, $args)->fetchArray($settings);

		$c = count($settings);

		$s = [];

		for ($i = 0; $i < $c; $i++) {
			$s[$settings[$i]['key']] = $settings[$i]['value'];
		}

		return $s;
	}

	public static function setSettings($key, $value, $domain = false) {

		$domain = $domain ? $domain : $_SESSION['domain']['id'];

		\libs\models\Resource::Load('domain_setting')->updateSetting($key, $value, $domain);
	}
    
    public static function ReadablePassword($str) {
        $str = self::to_camel_case($str);
        $str = ucfirst($str);
        
        $mapping = array(
            'o'=>'0',
            'O'=>'0',
            'i'=>'1',
            'l'=>'1',
            'I'=>'1',
            'z'=>'2',
            'Z'=>'2',
            'e'=>'3',
            'a'=>'4',
            's'=>'5',
            'S'=>'5',
            'B'=>'8',
            'g'=>'&'
        );
        
        $c = strlen($str);
        $i = 0;
        while($i<=$c) {
            $i++;
            
            if($i % 2 == 0) {
                $str[$i] = str_replace($str[$i],$mapping[$str[$i]],$str[$i]);
            }
        }
        
        return $str;
        
    }

}
