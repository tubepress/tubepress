<?php

return array(
    'debug' => false,
    'mode' => 'development',
    // Can be either mongodb or file.
    /*
    'save.handler' => 'file',
    'save.handler.filename' => dirname(__DIR__) . '/cache/' . 'xhgui.data.' . microtime(true) . '_' . substr(md5($url), 0, 6),
    */
    'save.handler' => 'mongodb',
    // Needed for file save handler. Beware of file locking. You can adujst this file path
    // to reduce locking problems (eg uniqid, time ...)
    //'save.handler.filename' => __DIR__.'/../data/xhgui_'.date('Ymd').'.dat',
    'db.host' => 'mongodb://127.0.0.1:27017',
    'db.db' => 'xhprof',
    // Allows you to pass additional options like replicaSet to MongoClient.
    // 'username', 'password' and 'db' (where the user is added)
    'db.options' => array(),
    'templates.path' => dirname(__DIR__) . '/src/templates',
    'date.format' => 'M jS H:i:s',
    'detail.count' => 6,
    'page.limit' => 25,

    'profiler.enable' => function() {

        if (!isset($_REQUEST['tubepress_profile']) && !isset($_COOKIE['tubepress_profile'])) {

            return false;
        }

        // Remove trace of the special variable from REQUEST_URI
        $_SERVER['REQUEST_URI'] = str_replace(array('?tubepress_profile', '&tubepress_profile'), '', $_SERVER['REQUEST_URI']);

        setcookie('tubepress_profile', 1);

        if (isset($_REQUEST['no_tubepress_profile'])) {

            setcookie('tubepress_profile', 0, time() - 86400);

            return false;
        }

        return true;
    }
);