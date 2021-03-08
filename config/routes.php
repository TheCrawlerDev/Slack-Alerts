<?php

// definimos as rotas
// uma rota é
// $routes['metodo (get,post,...)']['url relativa'] = array('set'=>'Controller@metodo do controller','params'=>'url relativa');
$routes['get']['alertas_performance_alexa'] = array('set'=>'Crawler@alertas_performance_alexa','params'=>'alertas_performance_alexa');
$routes['get']['alertas_performance_lighthouse'] = array('set'=>'Crawler@alertas_performance_lighthouse','params'=>'alertas_performance_lighthouse');
$routes['get']['alertas_performance_robots'] = array('set'=>'Crawler@alertas_performance_robots','params'=>'alertas_performance_robots');
$routes['get']['alertas_performance_seo_crawler'] = array('set'=>'Crawler@alertas_performance_seo_crawler','params'=>'alertas_performance_seo_crawler');
$routes['get']['alertas_performance_observatory'] = array('set'=>'Crawler@alertas_performance_observatory','params'=>'alertas_performance_observatory');
$routes['get']['alertas_performance_mysql'] = array('set'=>'Crawler@alertas_performance_mysql','params'=>'alertas_performance_mysql');
$routes['get']['all_alerts'] = array('set'=>'Crawler@all_alerts','params'=>'all_alerts');

?>
