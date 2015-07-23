<?php
/**
 * 模版处理程序参考和修改自zblog
 * @link http://www.zblogcn.com/
 */
class Template{
	private $tags = array();
	private $parsephpcodes=array();
	/**
	 * 去掉BOM头
	 * @param string $s
	 * @return string
	 */
	public function RemoveBOM($s){
		$charset=array();
		$charset[1] = substr($s, 0, 1);
		$charset[2] = substr($s, 1, 1);
		$charset[3] = substr($s, 2, 1);
		if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
			$s = substr($s, 3);
		}
		return $s;
	}

	/**
	 * @param $content
	 * @return mixed
	 */
	public function Compiling($content)
	{
		$this->parsePHP($content);
		$this->parse_template($content);
		$this->parse_vars($content);
		$this->parse_function($content);
		$this->parse_if($content);
		$this->parse_foreach($content);
		$this->parse_for($content);
		$this->parsePHP2($content);
		return $content;
	}
	/**
	 * @param $content
	 */
	private function parsePHP(&$content)
	{
		$this->parsephpcodes=array();
		$matches=array();
		if($i=preg_match_all ( "/\{php\}([\D\d]+?)\{\/php\}/si" ,  $content ,  $matches )>0){
			if(isset($matches[1]))
				foreach($matches[1] as $j=>$p) {
					$content = str_replace($p,'<!--'.$j.'-->',$content);
					$this->parsephpcodes[$j]=$p;
				}
		}
	}

	/**
	 * @param $content
	 */
	private function parsePHP2(&$content)
	{
		foreach($this->parsephpcodes as $j=>$p) {
			$content = str_replace('{php}<!--'.$j.'-->{/php}','<'.'?php '.$p.' ?'.'>',$content);
		}
		$content = preg_replace('/\{php\}([\D\d]+?)\{\/php\}/', '<'.'?php $1 ?'.'>', $content);
		$this->parsephpcodes=array();
	}

	/**
	 * @param $content
	 */
	private function parse_template(&$content)
	{
		$content = preg_replace('/\{template:([^\}]+)\}/', '{php} include bootstrap::view(\'$1\',null,RENDERER_DEFAULT_PATH,$bootstrap_option); {/php}', $content);
	}

	/**
	 * @param $content
	 */
	private function parse_vars(&$content)
	{
		$content = preg_replace_callback('#\{\$(?!\()([^\}]+)\}#',array($this,'parse_vars_replace_dot'), $content);
	}

	/**
	 * @param $content
	 */
	private function parse_function(&$content)
	{
		$content = preg_replace_callback('/\{([a-zA-Z0-9_]+?)\((.+?)\)\}/',array($this,'parse_funtion_replace_dot'), $content);
	}

	/**
	 * @param $content
	 */
	private function parse_if(&$content)
	{
		while(preg_match('/\{if [^\n\}]+\}.*?\{\/if\}/s', $content))
			$content = preg_replace_callback(
				'/\{if ([^\n\}]+)\}(.*?)\{\/if\}/s',
				array($this,'parse_if_sub'),
				$content
			);
	}

	/**
	 * @param $matches
	 * @return string
	 */
	private function parse_if_sub($matches)
	{

		$content = preg_replace_callback(
			'/\{elseif ([^\n\}]+)\}/',
			array($this, 'parse_elseif'),
			$matches[2]
		);

		$ifexp = str_replace($matches[1],$this->replace_dot($matches[1]),$matches[1]);
		$content = str_replace('{else}', '{php}}else{ {/php}', $content);
		return "<?php if ($ifexp) { ?>$content<?php } ?>";

	}

	/**
	 * @param $matches
	 * @return string
	 */
	private function parse_elseif($matches)
	{
		$ifexp = str_replace($matches[1],$this->replace_dot($matches[1]),$matches[1]);
		return "{php}}elseif($ifexp) { {/php}";
	}


	/**
	 * @param $content
	 */
	private function parse_foreach(&$content)
	{
		while(preg_match('/\{foreach(.+?)\}(.+?){\/foreach}/s', $content))
			$content = preg_replace_callback(
				'/\{foreach(.+?)\}(.+?){\/foreach}/s',
				array($this,'parse_foreach_sub'),
				$content
			);
	}

	/**
	 * @param $matches
	 * @return string
	 */
	private function parse_foreach_sub($matches)
	{
		$exp = $this->replace_dot($matches[1]);
		$code = $matches[2];
		return "{php} foreach ($exp) {{/php} $code{php} }  {/php}";
	}

	/**
	 * @param $content
	 */
	private function parse_for(&$content)
	{
		while(preg_match('/\{for(.+?)\}(.+?){\/for}/s', $content))
			$content = preg_replace_callback(
				'/\{for(.+?)\}(.+?){\/for}/s',
				array($this,'parse_for_sub'),
				$content
			);
	}

	/**
	 * @param $matches
	 * @return string
	 */
	private function parse_for_sub($matches)
	{
		$exp = $this->replace_dot($matches[1]);
		$code = $matches[2];
		return "{php} for($exp) {{/php} $code{php} }  {/php}";
	}

	/**
	 * @param $matches
	 * @return string
	 */
	private function parse_vars_replace_dot($matches)
	{
		if(strpos($matches[1],'=')===false){
			return '{php} echo $' . $this->replace_dot($matches[1]) . '; {/php}';
		}else{
			return '{php} $' . $this->replace_dot($matches[1]) . '; {/php}';
		}
	}

	/**
	 * @param $matches
	 * @return string
	 */
	private function parse_funtion_replace_dot($matches)
	{
		return '{php} echo ' . $matches[1] . '(' . $this->replace_dot($matches[2]) . '); {/php}';
	}

	/**
	 * @param $content
	 * @return mixed
	 */
	private function replace_dot($content)
	{
		$array=array();
		preg_match_all('/".+?"|\'.+?\'/', $content,$array,PREG_SET_ORDER);
		if(count($array)>0){
			foreach($array as $a){
				$a=$a[0];
				if(strstr($a,'.')!=false){
					$b=str_replace('.','{%_dot_%}',$a);
					$content=str_replace($a,$b,$content);
				}
			}
		}
        $content=preg_replace("/(?<=[^\\s])+\\.(?=[^\\s]+)/", '->', $content);
		$content=str_replace('{%_dot_%}','.',$content);
		return $content;
	}
}
?>