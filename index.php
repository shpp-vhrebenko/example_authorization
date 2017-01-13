<?php

function __autoload($classname){
    switch ($classname[0])
    {
        case 'C':
            include_once("classes/controllers/$classname.php");
            break;
        case 'M':
            include_once("classes/model/$classname.php");
    }
}

$action = 'action_';
$action .= (isset($_GET['act'])) ? $_GET['act'] : 'index';

switch ($_GET['c'])
{
    case 'articles':
        $controller = new C_Articles();
        break;
    case 'editor':
        $controller = new C_Editor();
        break;
    case 'auth':
        $controller = new C_Auth();
        break;
    case 'reg':
        $controller = new C_Registration();
        break;
    case 'admin':
        $controller = new C_AdminPanel();
        break;
    default:
        $controller = new C_Articles();
}

$controller->request($action);