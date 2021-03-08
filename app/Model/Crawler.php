<?php

class CrawlerModel extends Model{

	function __construct(){
		$this->model = new Model();
	}

	public function alexa(){
		//return $this->model->query('select *,(SELECT max(date) FROM `alexa` al WHERE al.url_id = ml.id) as ult_date from monitoring_links ml where Alexa = 1 and active = 1 order by ult_date;');
		return $this->model->query('select * from monitoring_links ml where Alexa = 1 and active = 1 order by id;');
	}

	public function valid_date_alexa($url_id){
		return $this->model->query("select max(date) as date from alexa where (url_id) = (".$url_id.") and date = '".date('Y-m-d')."';")[0]['date'];
	}

	public function pagespeed(){
		return $this->model->query('select * from monitoring_links where pagespeed = 1 and active = 1;');
	}

	public function valid_date_pagespeed($url_id){
		return count($this->model->query("select max(date) as date from alexa where (url_id) = (".$url_id.") and date >= '".date('Y-m-d')."';"))[0]['date'];
	}

	public function lighthouse(){
		//return $this->model->query('select *,(SELECT max(datetime) FROM `lighthouse` al WHERE al.url_id = mon.id) as ult_date from monitoring_links mon where mon.lighthouse = 1 and mon.active = 1 order by ult_date;');
		return $this->model->query('select * from monitoring_links mon where mon.lighthouse = 1 and mon.active = 1 order by id;');
	}

	public function valid_date_lighthouse($url_id){
		return $this->model->query("select max(datetime) as date from lighthouse_temp where (url_id) = (".$url_id.") and datetime = '".date('Y-m-d H:00:00')."';")[0]['date'];
	}

	public function valid_date_performance($url_id){
    return $this->model->query("select max(datetime) as date from performance where (url_id) = (".$url_id.") and datetime = '".date('Y-m-d H:00:00')."';")[0]['date'];
  }

	public function robots(){
		return $this->model->query('select * from monitoring_links mon where mon.robots = 1 and mon.active = 1 order by id;');
	}

	public function valid_date_robots($url_id){
		return $this->model->query("select max(datetime) from robots_test where url_id = ".$url_id." and datetime = '".date('Y-m-d H:00:00')."';");
	}

	public function robots_old($url_id){
		return $this->model->query("select rules,status from robots where url_id = ".$url_id." order by datetime desc;");
	}

	public function seo_crawler(){
		return $this->model->query('select * from monitoring_links mon where mon.SEO_crawl = 1 and mon.active = 1 order by rand();');
	}

	public function valid_date_seo_crawler($url_id){
		return count($this->model->query("select status from seo_monitoring_test where url_id = ".$url_id." and datetime = '".date('Y-m-d H:00:00')."';"));
	}

	public function seo_old($url_id){
		return count($this->model->query("select status from seo_monitoring where url_id = ".$url_id." order by url_id desc limit 1;"));
	}

	public function valid_date_observatory($url_id){
		return $this->model->query("select max(datetime) as date from observatory_test where url_id = ".$url_id." and datetime = '".date('Y-m-d H:00:00')."';")[0]['date'];
	}

	public function observatory($limit){
		return $this->model->query("select * from monitoring_links ml where observatory = 1 and active = 1 order by id;");
	}

	public function valid_quant_observatory($date){
		return count($this->model->query("select count(id) as quant from observatory where datetime = '".$date."' group by url_id limit 10000;"));
	}

	public function valid_quant_seo_crawler($date){
		return count($this->model->query("select count(id) as quant from seo_monitoring where datetime = '".$date."' group by url_id limit 10000;"));
	}

	public function valid_quant_lighthouse($date){
		return count($this->model->query("select count(id) as quant from lighthouse where datetime = '".$date."' group by url_id limit 10000;"));
	}

	public function valid_quant_robots($date){
		return count($this->model->query("select count(id) as quant from robots where datetime = '".$date."' group by url_id limit 10000;"));
	}

	public function valid_quant_performance($date){
		return count($this->model->query("select count(id) as quant from performance where datetime = '".$date."' group by url_id limit 10000;"));
	}

	public function valid_quant_alexa($date){
		return $this->model->query("select count(id) as quant from alexa where date = '".$date."' limit 10000;")[0]['quant'];
	}

	public function valid_alert($name){
		return $this->model->query("select * from cerebro_slack_alerts where name = '".$name."';")[0];
	}

	public function slow_queries(){
		return $this->model->query("SHOW FULL PROCESSLIST;");
	}


}

?>
