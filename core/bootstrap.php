<?php

namespace Core;

use Core\Routing\Route;
use Core\Routing\Router;
use Core\Http\Request;
//use Core\Database\QueryBuilder; Testes para QueryBuilder


require_once __DIR__."/../app/routes.php";


$router = new Router(new Request());

$router->run();

/*
 *          Tesstes para QueryBuilder
require_once __DIR__."/../config/databaseConfig.php";
$db = new QueryBuilder($options);

#INSERT
$qb
    ->table('users')
    ->fields(['nome', 'login', 'password'])
    ->insert(['William', 'wilcorrea', crypt('senha')]);
#SELECT
$qb
    ->table('users') // poderia não informar, pois já está "salvo"
    ->fields(['id', 'nome', 'login', 'password'])
    ->select();
# UPDATE
$qb
    ->table('users') // poderia não informar, pois já está "salvo"
    ->fields(['nome'])
    ->where(['id = ?'])
    ->update(['William Correa'], [2]);
#DELETE
$qb
    ->table('users') // poderia não informar, pois já está "salvo"
    ->where(['id = ?'])
    ->delete([1]);
*/
