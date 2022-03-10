<?php


namespace Core\Models;


interface ModelsInterface
{
    public static function create();
    public static function all();
    public static function find();
    public static function findOrFail();
}
