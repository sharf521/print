<?php
if (file_exists(__DIR__ . '/' . ucfirst($_G['class']) . 'Controller.php')) {
    $_classpath = "\\App\\Controller\\Data\\" . ucfirst($_G['class']) . "Controller";
    $class = new $_classpath();
    if (method_exists($class, $_G['func'])) {
        return call_user_func(array($class, $_G['func']), array());
    } else {
        return call_user_func(array($class, 'error'), array());
    }
} else {
    $class = new \App\Controller\Data\DataController();
    return call_user_func(array($class, 'error'), array());
}