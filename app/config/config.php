<?php
#config.php
#Taken from 2019/maziar/wk5
#Kurt Chiu 101190261
$config = [
    'VIEW_PATH' => APP_PATH . DS . 'view' . DS,
    'MODEL_PATH' => APP_PATH . DS . 'model' . DS,
    'LIB_PATH' => APP_PATH . DS . 'lib' . DS,
	'DATA_PATH' => APP_PATH . DS . 'data' . DS,
];

include $config['LIB_PATH'] . 'functions.php';

?><?php
$source_links [] = "<br><a href='/folder_view/vs.php?s=" . __FILE__ . "' target='_blank'>View Source of ". __FILE__;
?>