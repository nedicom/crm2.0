<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MetrikaController extends Controller
{
    public function test()
	{
		$list = [
			0 => [
				'create_date_t' => '02.12.2024 14:01',
				'id' => '101',
				'phones' => '89811747237',
				'order_status' => 'CANCELLED',
				'revenue' => 11,
			],
			1 => [
				"create_date_t" => "02.12.2024 13:27",
				"id" => "102",
				"phones" => "89786684252",
				"order_status" => "IN_PROGRESS",
				"revenue" => 5,
			],	
		];
		//dd($list);
        $path = 'data.csv';
        if(is_file($path)) {
            unlink($path);
        }
        $this->produceCSV($path, $list, true);
        //dd(file_get_contents($path));

        $resp = Http::withToken('y0_AgAAAAAPGhtRAAzhMQAAAAEaxRB_AAAEQ_XYWPNHy5srO2Eq0mSoTaVgPA')->attach('attachment', file_get_contents($path), 'data.csv')
            ->post('https://api-metrika.yandex.net/cdp/api/v1/counter/24900584/data/simple_orders?merge_mode=SAVE&delimiter_type=COMMA');
        dd($resp);
	}

    protected function produceCSV($file_name, $arr, $has_header = false) {

        //$has_header = true;

        foreach ($arr as $c) {

            $fp = fopen($file_name, 'a');

            if (!$has_header) {
                fputcsv($fp, array_keys($c));
                $has_header = true;
            }

            fputcsv($fp, $c);
            fclose($fp);

        }

    }

    private function getInfoYa($url,$token,$data,$boundary){

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host:api-metrika.yandex.net','Authorization: OAuth '.$token,"Content-Type: multipart/form-data; boundary=------------------------$boundary","Content-Length: " . strlen($data)));

        $response = array();
        $response['html']     = curl_exec($ch);
        $response['err']      = curl_errno($ch);
        $response['errmsg']   = curl_error($ch);
        $response['header']   = curl_getinfo($ch);



        curl_close($ch);


        return $response;
    }

    public function test2()
    {
        //dd(2);
        $token = "y0_AgAAAAAPGhtRAAzhMQAAAAEaxRB_AAAEQ_XYWPNHy5srO2Eq0mSoTaVgPA";
        $orders = "UserId,Target,DateTime,Price,Currency".PHP_EOL;



        $orders .= "719992702,PAY,".time().",50,RUB".PHP_EOL;
        $orders .= "599163530,PAY,".time().",150,RUB".PHP_EOL;
        $orders .= "317910723,PAY,".time().",175,RUB".PHP_EOL;

        $counterId = 24900584; //id счетчика
        $boundary = "7zDUQOAIAE9hEWoV";
        $filename = 'data.csv';

        $data = "--------------------------$boundary\x0D\x0A";
        $data .= "Content-Disposition: form-data; name=\"file\"; filename=\"$filename\"\x0D\x0A";
        $data .= "Content-Type: text/csv\x0D\x0A\x0D\x0A";
        $data .= $orders . "\x0A\x0D\x0A";
        $data .= "--------------------------$boundary--";

        $url = "https://api-metrika.yandex.net/management/v1/counter/".$counterId."/offline_conversions/upload?client_id_type=CLIENT_ID&oauth_token=".$token;

        $yaInfo = $this->getInfoYa($url,$token,$data,$boundary);
        dd($yaInfo);

        $yaInfo = json_decode($yaInfo["response"]["html"],true);

        ?><pre><?print_r($yaInfo)?></pre><?
    }
}
