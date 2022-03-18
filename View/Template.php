<?php
namespace yurni\framework\View;

class Template {
    private $blocks = array();
    private $temp_path;
    private $cache_path;
	private $cache_enabled;
    
    /**
     * __construct
     *
     * @param  mixed $data
     * @return void
     */
    public function __construct($data = []){
		$this->temp_path = $data["temp_path"];
		$this->cache_path = $data["cache_path"];
		$this->cache_enabled = $data["cache"] ?? false;
		$this->optimize = $data["optimize"] ?? true;
	}
	public function render($file, $params = array()) {
		$cached_file = $this->cache($file);
		
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
	   	require $cached_file;
		return ob_get_clean();
		
	}

	public function cache($file) {
		
		if (!file_exists($this->cache_path)) {
		  	mkdir($this->cache_path, 0744);
		}
		
	    $cached_file = $this->cache_path . str_replace(array('/', '.html'), array('_', ''), $this->temp_path.$file . '.php');
		
	    if (!$this->cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($this->temp_path.$file)) {
			if($this->optimize) 
				$code = trim(preg_replace('/\s\s+/', ' ', $this->includeFile($file)));
			else
				$code = $this->includeFile($file);
			$code = $this->compiler($code);
	        file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
	    }

		return $cached_file;
	}
	public function clearCache() {
	
		foreach(glob($this->cache_path . '*') as $file) {
			unlink($file);
		}
	}
	public function includeFile($file){
		$code = "";
		if (file_exists($this->temp_path.$file)) {
			$code = file_get_contents($this->temp_path.$file);
	  	}
	
		if(preg_match_all("/\{%\s*(extends|include)\s*(.+?)\s*\%}/is",$code,$matches,PREG_SET_ORDER)){
			foreach ($matches as $value) {
				$code = str_replace($value[0], $this->includeFile($value[2]), $code);
			}
		}
		$code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
		return $code;
	}

	public function compilePHP($output){
		$output = preg_replace("/\{{{\s*(.+?)\s*\}}}/is","<?php echo htmlspecialchars($1, ENT_QUOTES, 'UTF-8') ?>",$output);
		$output = preg_replace("/\{{\s*(.+?)\s*\}}/is","<?php echo $1; ?>",$output);
		$output = preg_replace("/{%\s* each\s*(.*?) as (.*?)\s*%}(.*?){%\s*endeach\s*%}/is","<?php foreach($1 as $2){ ?> $3 <?php } ?>",$output);
		$output = preg_replace('/\{%\s*(.+?)\s*\%}/is', '<?php $1 ?>', $output);

		return $output;
	}
	public function compileBlock($code){
		if(preg_match_all('/{% ?block ?(?<blockName>.*?) ?%}(?<blockContent>.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER)){
			foreach ($matches as $value) {
				
				if(!array_key_exists($value["blockName"],$this->blocks))
					$this->blocks[$value["blockName"]] = '';

				if (strpos($value["blockContent"], '@parent') === false) {
					$this->blocks[$value["blockName"]] = $value["blockContent"];
				} else {
					$this->blocks[$value["blockName"]] = str_replace('@parent', $this->blocks[$value["blockName"]], $value["blockContent"]);
				}
				$code = str_replace($value[0], '', $code);
			}
		}
		return $code;
	}

	public function compileYield($code) {
		foreach($this->blocks as $block => $value) {
			$code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
		}
		$code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
		return $code;
	}
    public function compiler($output) {
		$output = $this->compileBlock($output);
		$output = $this->compileYield($output);
		$output = $this->compilePHP($output);
		return $output;
	}
}