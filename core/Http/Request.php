<?php


namespace Core\Http;

use Core\Http\RequestInterface;


// fazer try catch de todo mundo
class Request implements RequestInterface{
    private $url;
    private $path;
    private $query;
    private $method;
    private $postInputs;

    public function __construct($request = null) {
        $request = isset($request) ? $request : $this->request();
        
        $this->url          = $this->sanitizeUrl($request['url']); // metodos para sanitizar a url
        $this->path         = $this->getValuePath($request['url']); // metodos para sanitizar a url path
        $this->query        = $this->getValueQuery($request['url']); // metodos para sanitizar a url query
        $this->method       = $this->getValueMethod($request['method']); // metodos para retornar o method correto
        $this->postInputs   = $this->getValuePostIputs($request['postInputs']);
    }


    public function request()
    {
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
        return [
            'url' =>$protocol."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'postInputs' => $_POST
        ];
    }

    private function getValuePath(string $url)
    {
        return parse_url($this->sanitizeUrl($url), PHP_URL_PATH);
    }

    private function getValueMethod($method)
    {
        if(empty($method)) {
            throw new InvalidArgumentException('I need valid value');
        }
        switch (strtoupper($method)) {
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
                return strtoupper($method);
                break;           
            default:
                throw new InvalidArgumentException('Unexpected value.');
                break;
        }
    }

    private function getValueQuery(string $url)
    {
        $query = parse_url($this->sanitizeUrl($url), PHP_URL_QUERY);
        if ($query == null) {
            return [];
        }
        parse_str($query, $finalQuery);

        return $this->queryFormater($finalQuery);
    }

    private function queryFormater(array $query) // vai validar as chaves e valores da query
    {
        $newQueryArray = []; // nova query retornada
        foreach ($query as $key => $value) {
            $matchKey = "";
            if(preg_match('/^(?![0-9])[a-zA-Z0-9]+$/', $key, $matchKey)){ // checar se chave não começa com numero
                $newQueryArray[$key] = $value;
            }
        }
        return $newQueryArray;
    }


    private function sanitizeUrl(string $url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    private function getValuePostIputs(array $postInputs = null)
    {
        if (!isset($postInputs)) {
            return [];
        }
        return $postInputs; // fazer validação no futuro
    }

    private function getValuesIfExistsInArray(array $indexes = null, array $arrayBase)
    {
        
        if ($arrayBase != []) {
            if (!isset($indexes)) {
                return $arrayBase;
            }
            
            $newArray = [];
            
            foreach ($indexes as $key) {
                if(array_key_exists($key, $arrayBase)){
                    $newArray[$key] = $arrayBase[$key];
                }
            }
            return $newArray;
        }

        return [];
    }

    public function get(array $indexes = null) // retornar as queries paramters
    {
        return $this->getValuesIfExistsInArray($indexes, $this->query);
    }

    public function input(array $indexes = null) // retornar as input post
    {
        return $this->getValuesIfExistsInArray($indexes, $this->postInputs);
    }

    public function getMethod(){
        return $this->method;
    }
    public function path(){
        return $this->path;
    }
    public function url(){
        $fullUrl = parse_url($this->url);
        $urlWithoutQuery = $fullUrl['scheme'] . "://". $fullUrl['host'] . $fullUrl['path'];
        
        return $urlWithoutQuery;
    }
    public function fullUrl(){
        return $this->url;
    }

    public function isAjax()
    {
        return (bool)
        (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        );
    }
}

