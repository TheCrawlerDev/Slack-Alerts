<?php

class CrawlerController{

	// este metodo calcula se a media de performace está abaxio do minimo, na media ou acima da média e envia alertas baseados nisso
	public function alerts($model_generic,$model,$request,$body,$performance,$date){
		if($performance['ganho_percent']<=MEDIA_PERFORMANCE && $performance['ganho_percent']>MIN_PERFORMANCE){// alerta amarelo ou laranja
			$dados['active'] = 1;
			$dados['level'] = 1;
			$valid_alert = $model->valid_alert($performance['table']);
			if($valid_alert['active']<>$dados['active'] && $valid_alert['level']<>$dados['level']){
				$slack_message = json_encode(
					array(
						"fallback"=>"Problemas na performance da tabela ".$performance['table'],
						"text"=> "Performance da coleta está abaixo de ".MEDIA_PERFORMANCE."%",
						"color"=> "warning",
						"fields"=>	array(array(
							"title"=> "Ajuste a tabela ".$performance['table'],
							"value"=> "O potencial máximo da tabela é ".$performance['max'].", mas coletamos apenas ".$performance['score']." na data ".$date,
							"short"=> false
						)),
					)
				);
				print_r(['dados'=>$dados,'valid_alert'=>$valid_alert,'performance'=>$performance,'message'=>$slack_message]);
				carregar('https://hooks.slack.com/services/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',['payload'=>$slack_message]);
				$result = $model_generic->update('cerebro_slack_alerts',['name'=>stringify_sql($performance['table'])],$dados);
			}else{
				echo "Mensagem 1 já foi enviada!";
			}
		}else if($performance['ganho_percent']<=MIN_PERFORMANCE){// alerta vermelho
			$dados['active'] = 1;
			$dados['level'] = 2;
			$valid_alert = $model->valid_alert($performance['table']);
			var_dump(	$valid_alert );
			if($valid_alert['active']<>$dados['active'] && $valid_alert['level']<>$dados['level']){
				$slack_message = json_encode(
					array(
						"fallback"=>"Problemas sérios na performance da tabela ".$performance['table'],
						"text"=> "Performance da coleta está abaixo de ".MIN_PERFORMANCE."%",
						"color"=> "danger",
						"fields"=>	array(array(
							"title"=> "Ajuste com urgência a tabela ".$performance['table'],
							"value"=> "O potencial máximo da tabela é ".$performance['max'].", mas coletamos apenas ".$performance['score']." na data ".$date,
							"short"=> false
						)),
					),JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
				);
				print_r(['dados'=>$dados,'valid_alert'=>$valid_alert,'performance'=>$performance,'message'=>$slack_message]);
				carregar('https://hooks.slack.com/services/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',['payload'=>$slack_message]);
				$result = $model_generic->update('cerebro_slack_alerts',['name'=>stringify_sql($performance['table'])],$dados);
			}else{
				echo "Mensagem 2 já foi enviada!";
			}
		}else{// não existe alerta
			$dados['active'] = 0;
			$dados['level'] = 0;
			$valid_alert = $model->valid_alert($performance['table']);
			if($valid_alert['active']<>$dados['active'] && $valid_alert['level']<>$dados['level']){
				$slack_message = json_encode(
					array(
					"fallback"=>"Ajustes performance de tabela ".$performance['table'],
					"text"=> "Performance da coleta está acima de ".MEDIA_PERFORMANCE."% para a tabela ".$performance['table'],
					"color"=> "good",
					"fields"=>	array(array(
							"title"=> "Ajustes funcionaram",
							"value"=> "O potencial máximo da tabela é ".$performance['max'].", coletamos ".$performance['score']." na data ".$date,
							"short"=> false
						)),
					)
				);
				print_r(['dados'=>$dados,'valid_alert'=>$valid_alert,'performance'=>$performance,'message'=>$slack_message]);
				print_r(carregar('https://hooks.slack.com/services/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',['payload'=>$slack_message]));
				$result = $model_generic->update('cerebro_slack_alerts',['name'=>stringify_sql($performance['table'])],$dados);
			}else{
				echo "Mensagem 3 já foi enviada!";
			}
		}
	}

	// mede a performance da tabela alexa
	public function alertas_performance_alexa($model,$request,$body){
		$date = date('Y-m-d', strtotime('-1 day'));
		$model_generic = new Model();
		$links = $model->alexa();
		$performance = array();
		$performance['table'] = 'alexa';
		$performance['max'] = count($links);// conta o numero de links a serem coletados da tabela monitoring links
		$performance['score'] = $model->valid_quant_alexa($date);
		$performance['perda'] = $performance['max'] - $performance['score'];
		$performance['perda_percent'] = ($performance['perda']/$performance['max'])*100;
		$performance['ganho_percent'] = ($performance['score']/$performance['max'])*100;
		print_r($performance);
		self::alerts($model_generic,$model,$request,$body,$performance,$date);
	}

	// mede a performance da tabela lighthouse
	public function alertas_performance_lighthouse($model,$request,$body){
		$date = date('Y-m-d H:00:00', strtotime('-1 hour'));
		$model_generic = new Model();
		$links = $model->lighthouse();
		$performance = array();
		$performance['table'] = 'lighthouse';
		$performance['max'] = count($links);// conta o numero de links a serem coletados da tabela monitoring links
		$performance['score'] = $model->valid_quant_lighthouse($date);
		$performance['perda'] = $performance['max'] - $performance['score'];
		$performance['perda_percent'] = ($performance['perda']/$performance['max'])*100;
		$performance['ganho_percent'] = ($performance['score']/$performance['max'])*100;
		print_r($performance);
		self::alerts($model_generic,$model,$request,$body,$performance,$date);
	}

	// mede a performance da tabela performance
	public function alertas_performance_performance($model,$request,$body){
		$date = date('Y-m-d H:00:00', strtotime('-1 hour'));
		$model_generic = new Model();
		$links = $model->lighthouse();
		$performance = array();
		$performance['table'] = 'performance';
		$performance['max'] = count($links);// conta o numero de links a serem coletados da tabela monitoring links
		$performance['score'] = $model->valid_quant_performance($date);
		$performance['perda'] = $performance['max'] - $performance['score'];
		$performance['perda_percent'] = ($performance['perda']/$performance['max'])*100;
		$performance['ganho_percent'] = ($performance['score']/$performance['max'])*100;
		print_r($performance);
		self::alerts($model_generic,$model,$request,$body,$performance,$date);
	}

	// mede a performance da tabela robots
	public function alertas_performance_robots($model,$request,$body){
		$date = date('Y-m-d H:00:00', strtotime('-1 hour'));
		$model_generic = new Model();
		$links = $model->robots();
		$performance = array();
		$performance['table'] = 'robots';
		$performance['max'] = count($links);// conta o numero de links a serem coletados da tabela monitoring links
		$performance['score'] = $model->valid_quant_robots($date);
		$performance['perda'] = $performance['max'] - $performance['score'];
		$performance['perda_percent'] = ($performance['perda']/$performance['max'])*100;
		$performance['ganho_percent'] = ($performance['score']/$performance['max'])*100;
		print_r($performance);
		self::alerts($model_generic,$model,$request,$body,$performance,$date);
	}

	// mede a performance da tabela seo_crawler
	public function alertas_performance_seo_crawler($model,$request,$body){
		$date = date('Y-m-d H:00:00', strtotime('-1 hour'));
		$model_generic = new Model();
		$links = $model->seo_crawler();
		$performance = array();
		$performance['table'] = 'seo_crawler';
		$performance['max'] = count($links);// conta o numero de links a serem coletados da tabela monitoring links
		$performance['score'] = $model->valid_quant_seo_crawler($date);
		$performance['perda'] = $performance['max'] - $performance['score'];
		$performance['perda_percent'] = ($performance['perda']/$performance['max'])*100;
		$performance['ganho_percent'] = ($performance['score']/$performance['max'])*100;
		print_r($performance);
		self::alerts($model_generic,$model,$request,$body,$performance,$date);
	}

	// mede a performance da tabela observatory
	public function alertas_performance_observatory($model,$request,$body){
		$date = date('Y-m-d H:00:00', strtotime('-1 hour'));
		$model_generic = new Model();
		$links = $model->observatory(500);
		$performance = array();
		$performance['table'] = 'observatory';
		$performance['max'] = count($links);// conta o numero de links a serem coletados da tabela monitoring links
		$performance['score'] = $model->valid_quant_observatory($date);
		$performance['perda'] = $performance['max'] - $performance['score'];
		$performance['perda_percent'] = ($performance['perda']/$performance['max'])*100;
		$performance['ganho_percent'] = ($performance['score']/$performance['max'])*100;
		print_r($performance);
		self::alerts($model_generic,$model,$request,$body,$performance,$date);
	}

	public function alertas_performance_mysql($model,$request,$body){
		$model_generic = new Model();
		$queries = $model->slow_queries();// busca queries lentas
		foreach($queries as $q){
			$max = array();
			$max['Time'] = 5*60;
			$max['Command'] = 'Query';
			$max['State'] = 'Sending data';
			$max['State_alt'] = 'Sending to client';
			// envia alertas a cada query lenta encontrada no sistema
			if($q['Time']>$max['Time'] && $q['Command']==$max['Command'] && ($q['State']==$max['State'] || $max['State']==$max['State_alt'])){
				$slack_message = json_encode(
					array(
						"fallback"=>"Uma query esta sendo executada pelo usuario ".$q['User']." a mais de 5 minutos.",
						"text"=> "Performance do banco de dados pode ser afetada!<http://142.93.189.150/db_simplex/|Clique aqui> para ver detalhes.",
						"color"=> "danger",
						"fields"=>	array(array(
														"title"=> "Ajuste o banco de dados",
														"value"=> "Ajustes as queries lentas",
														"short"=> false
												)),
					)
				);
				print_r(['dados'=>$q,'message'=>$slack_message]);
				print_r(carregar('https://hooks.slack.com/services/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',['payload'=>$slack_message]));
				break;
			}
		}
	}

	// roda os metodos dos alertas juntos
	public function all_alerts($model,$request,$body){
			echo "</br></br>Alexa</br>";
			self::alertas_performance_alexa($model,$request,$body);
			echo "</br></br>Lighthouse</br>";
			self::alertas_performance_lighthouse($model,$request,$body);
			echo "</br></br>Lighthouse Performance</br>";
			self::alertas_performance_performance($model,$request,$body);
			echo "</br></br>Observatory</br>";
			self::alertas_performance_observatory($model,$request,$body);
			echo "</br></br>Seo Crawler</br>";
			self::alertas_performance_seo_crawler($model,$request,$body);
			echo "</br></br>Robots</br>";
			self::alertas_performance_robots($model,$request,$body);
			echo "</br></br>Mysql</br>";
			self::alertas_performance_mysql($model,$request,$body);
	}

}

?>
