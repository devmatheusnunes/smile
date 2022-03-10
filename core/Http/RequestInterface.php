<?php

namespace Core\Http;

interface RequestInterface{
    // get query paramters
    // get (all) input post
    // sanitize url
    // get url, and get full url
    // get http method
    // query formater

    public function get(); // v
    public function input(); // v
    public function getMethod(); // v
    public function path(); // v
    public function url(); // v
    public function fullUrl(); // v
    public function isAjax(); // v
    // public static function sanitizeUrl();
    // public static function queryFormater();

    /*
    olhar em https://laravel.com/docs/5.7/requests
    
    public function isMethod();
    public function has();
    public function **(); via json
    
    */
}