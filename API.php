<?php

// Роутер
function route($method, $urlData, $formData, $token) {
    include_once './conf.php';

    #модуль авторизации
    $sql="SELECT * FROM `gl_api` WHERE `token`='".$token."' LIMIT 1";
    $result = $connect->query($sql);
    $isuser = false;
    if($result->num_rows>0){
        $isuser = true;
    }
    
    // Получение ключевой ставки
    // GET /cbrfkeyrate
    if ($method === 'GET' && empty($urlData) && $isuser==false) {

        // Вытаскиваем ставку и дату
        $sql="SELECT Rate, DateStamp FROM `cbrfkeyrate` ORDER BY id DESC LIMIT 1";
        $result= $connect->query($sql);
        $ratedata=$result->fetch_assoc();

      
        //Выводим ставку и дату
        echo json_encode(array(
            'method' => 'GET',
            'rate' => $ratedata['Rate'],
			'date' => $ratedata['DateStamp'],
        ),JSON_UNESCAPED_UNICODE);

        return;
    }

    // Получение ключевой ставки
    // GET /cbrfkeyrate/all
    if ($method === 'GET' && count($urlData) === 1 && $urlData[0] === 'all' && $isuser==false) {
      	if(empty($formData['start']) && empty($formData['end'])){
        	$sql="SELECT Rate, DateStamp FROM `cbrfkeyrate`";
        } else {
        	$sql="SELECT Rate, DateStamp FROM `cbrfkeyrate` WHERE DateStamp BETWEEN '".$formData['start']."' AND '".$formData['end']."'";
        }

        // Вытаскиваем ставку и дату
        $result= $connect->query($sql);
      	$rows =[];
      	while ($row = $result->fetch_assoc()) {
        	$row['DateStamp']=$row['DateStamp']." 12:00:00";
        	$rows[] = $row;
        }

      
        //Выводим ставку и дату
        echo json_encode(array(
            'method' => 'GET',
            'Dataset' => $rows,
            
        ),JSON_UNESCAPED_UNICODE);

        return;
    }
  
    // Получение ключевой ставки
    // GET /cbrfkeyrate/invest
    if ($method === 'GET' && count($urlData) === 1 && $urlData[0] === 'invest' && $isuser==false) {

        // Вытаскиваем ставку и дату
        $sql="SELECT Rate, DateStamp FROM `cbrfkeyrate` ORDER BY id DESC LIMIT 1";
        $result= $connect->query($sql);
        $ratedata=$result->fetch_assoc();
      
      
      	$keypercent=$ratedata['Rate'];
      	$amount = $formData['amount'];
        $month = $formData['month'];
    	if ($amount >= 50000 && $amount < 100000) {
        	$percent=$keypercent+2.5+($month-5)*0.25;
    	}elseif ($amount >= 100000 && $amount < 500000) {
        	$percent=$keypercent+3+($month-5)*0.25;
    	}elseif ($amount >= 500000 && $amount < 1000000) {
        	$percent=$keypercent+3.5+($month-5)*0.25;
    	} else {
        	$percent=0;
    	}
		$totalamount=$amount+(($percent*$amount)/100)*$month/12;
		$profit=(($percent*$amount)/100)*$month/12;
		$permonthprofit=$profit/$month;

 		

        //Выводим ставку и дату
        echo json_encode(array(
            'rate' => $ratedata['Rate'],
			'keypercent' => $percent,
            'amount' => $amount,
          	'totalamount' => $totalamount,
          	'profit' => $profit,
          	'permonthprofit' => $permonthprofit,
          	'months' => $month,
        ),JSON_UNESCAPED_UNICODE);
 		
        return;
    }	

    // Возвращаем ошибку
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));

