<?php
namespace yurni\framework\Http;
use yurni\framework\Application;
use yurni\framework\Router\Router;
use yurni\framework\Router\Route;
use yurni\framework\Exception\ForbiddenException;

class Request {
    public $files;
    protected $_server;
    protected Route $route;
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->_server = $_SERVER;
        $this->files = $_FILES;
    }

    
    public function setRoute(Route $route){
        $this->route = $route;
        return $this;
    }
    
    public function route(){
        return $this->route;
    }
    public function getSession()
    {
        return new Session();
    }

    public function server($val)
    {
        return isset($this->_server[$val]) ? $this->_server[$val] : false;
    }

    public function getPath()
    {
        $path_info = $this->server("PATH_INFO");
        if(!$path_info) {
            $request_uri = $this->server('REQUEST_URI');
            $script_name = $this->server('SCRIPT_NAME');
            if (pathinfo($script_name, PATHINFO_EXTENSION) == 'php') {
                $path_info = str_replace($script_name, '', $request_uri);
            }  else {
                $path_info = $request_uri;
            }
            
            $path_info = explode('?', $path_info, 2)[0];
        }
        return '/'.trim($path_info, '/');
    }

    public function file($key)
    {
        $_file = $this->files[$key];
        return $this->hasFile($key)? $this->makeUploader($_file) : NULL;
    }

    public function multiFiles($key)
    {
        if(!$this->hasMultiFiles($key)) return array();

        $input_files = array();

        $files = $this->files[$key];

        $names = $files["name"];
        $types = $files["type"];
        $temps = $files["tmp_name"];
        $errors = $files["error"];
        $sizes = $files["size"];

        foreach($temps as $i => $tmp) {
            if(empty($tmp) OR !is_uploaded_file($tmp)) continue;

            $_file = array(
                'name' => $names[$i],
                'type' => $types[$i],
                'tmp_name' => $tmp,
                'error' => $errors[$i],
                'size' => $sizes[$i]
            );

            $input_files[] = $this->makeUploader($_file);
        }

        return $input_files;
    }

    public function hasFile($key)
    {
        $file = $this->files[$key] ?? false;

        if(!$file) return FALSE;

        $tmp = $file["tmp_name"];

        if(!is_string($tmp)) return FALSE;

        return is_uploaded_file($tmp);
    }

    public function hasMultiFiles($key)
    {
        $files = $this->files[$key] ?? false;
        if(!$files) return FALSE;
        $uploaded_files = $files["tmp_name"];

        if(!is_array($uploaded_files)) return FALSE;

        foreach($uploaded_files as $tmp_file) {
            if(!empty($tmp_file) AND is_uploaded_file($tmp_file)) return TRUE;
        }

        return FALSE;
    }

    protected function makeUploader(array $_file)
    {
        return new fileUpload($_file);
    }

    public function getMethod()
    {
        return strtolower($this->server('REQUEST_METHOD'));
    }

    public function isPost()
    {
        return ($this->getMethod() == "post") ? true : false;
    }

    public function isGet()
    {
        return ($this->getMethod() == "get") ? true : false;
    }

    public function isPut()
    {
        return ($this->getMethod() == "put") ? true : false;
    }
    
    public function isPatch()
    {
        return ($this->getMethod() == "patch") ? true : false;
    }
    
    public function isDelete()
    {
        return ($this->getMethod() == "delete") ? true : false;
    }
    public function isHttps()
    {
        if($this->server('HTTPS')) {
            return true;
        } else {
            return false;
        }
    }

    public function isHttp()
    {
        return !$this->isHttps();
    }
    
    public function isAjax()
    {
        if(!empty($this->server('HTTP_X_REQUESTED_WITH')) && strtolower($this->server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') 
            return true;
        else 
            return false;
    }

    public function body()
    {
        return file_get_contents("php://input");
    }

    public function inputs()
    {
        $body = [];
        if($this->isGet() || isset($_GET)){
            foreach($_GET as $key => $val){
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if($this->isPost() || isset($_POST)){
            foreach($_POST as $key => $val){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if($this->isPut() || $this->isDelete() || $this->isPatch()){
            //$body = $this->body();
            $obj = json_decode($this->body(),true);
            foreach($obj as $key => $val){
                $body[$key] = $val;
            }
        }
        
        return $body;
    }

    public function input($key)
    {
        return $this->inputs()[$key] ?? null;
    }
    public function __get( $key )
    {
        return $this->input($key) ?? null;
    }

}