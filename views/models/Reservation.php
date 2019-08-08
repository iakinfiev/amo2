<?php

// comment out the following two lines when deployed to production

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class Reservation
{
	const COUNT = 100;
	const OFFSET = 100;

	public $n;
	public $daysReturn;
	public $idDate;
	public $apiKeyClients;
	public $host;
	public $status;
	public $dateOt;
	public $dateDo;
	public $offset;
	private $price;
	private $allPrice;

	function __construct()
	{
		require_once __DIR__ . '/../../vendor/autoload.php';

	}

	public function getAllPrice()
	{
		return $this->allPrice;
	}

	function getLeadSuccessfullyImplemented()
	{
		Introvert\Configuration::getDefaultConfiguration()->setApiKey('key', $this->apiKeyClients);
		Introvert\Configuration::getDefaultConfiguration()->setHost($this->host);
		$api = new Introvert\ApiClient();
		$crm_user_id = array(); // int[] | фильтр по id ответственного
		// $status = array(142); // int[] | фильтр по id статуса
		$id = array(); // int[] | фильтр по id
		$ifmodif = null; // string | фильтр по дате изменения. timestamp или строка в формате 'D, j M Y H:i:s'
		// $count = 56; // int | Количество запрашиваемых элементов
		// $offset = 0; // int | смещение, относительно которого нужно вернуть элементы

		$result['count'] = 0;
		$lead = [];
		$i = 1;

		try {

			do {
            $lead = $result = $api->lead->getAll($crm_user_id, $this->status, $id, $ifmodif, self::COUNT, $this->offset);
			    $lead = array_merge((array)$lead, (array)$result['result']);
		    	$this->offset += 100;

			} while ($result['count'] == self::OFFSET);

			} catch (Exception $e) {
			    echo 'Exception when calling LeadApi->getAll: ', $e->getMessage(), PHP_EOL;
			}

			$ar = [];
            foreach ($lead['result'] as $key => $value) {
                foreach ($value['custom_fields'] as $key1 => $value1) {
                    if ( $value1['id'] == $this->idDate){
                        $ar[] = strtotime(strstr($value1['values'][0]['value'], ' ', true));
                    }
                }
            }
        $ar = array_count_values($ar);
        ksort($ar);

        $ars = [];
        foreach ($ar as $k => $v){
            $ars[date("Y, m, d",$k)] = $v;
        }
        // test array

//            $ar = [
//                '2019-08-08' => 2,
//                '2019-08-10' => 5,
//            ];

        foreach ($ars as $key => $v){
            foreach ($this->daysReturn as $k2 => $v2){
                if ($key == $k2){
                    if ($v >= $this->n)
                    $this->daysReturn[$k2] = 0;
                }
            }
        }

        die(json_encode($this->daysReturn));

//        file_put_contents("/home/heess/temp_log/file.txt", print_r($lead, true).PHP_EOL, FILE_APPEND);exit;
	}

	function getClients() {
	    return [
	        [
	            'id' => 1,
	            'name' =>'intrdev',
	            'api' => '',
	        ],
	        [
	            'id' => 2,
	            'name' => 'artedegrass0',
	            'api' => '23bc075b710da43f0ffb50ff9e889aed',
	        ],
	    ];
	}

}


	$run = new Reservation;
    $Date = date("Y-m-d H:i:s");
	$run->dateOt = strtotime($Date);
	$run->dateDo = strtotime(date('Y-m-d', strtotime($Date. ' + 30 days')));
	$run->status = array(143);
	$run->host = 'https://api.s1.yadrocrm.ru/tmp';
	$run->offset = 0;
	$run->idDate = 939049;
    !empty($_POST['num']) ? $run->n = $_POST['num'] : $run->n = 5;

    $days = array_flip(range(strtotime($Date), strtotime(date('Y-m-d', strtotime($Date. ' + 30 days'))), (24*60*60)));

    $day = [];

    foreach ($days as $key => $v){
        $day[date("Y-m-d",$key)] = 1;
    }
    $run->daysReturn = $day;

	$getClients = $run->getClients();
	$listsClient = [];
	foreach ($getClients as $key => $value) {
		if (!empty($value['api'])) {
			$run->apiKeyClients = $value['api'];
			$run->getLeadSuccessfullyImplemented();
		}
	}
?>