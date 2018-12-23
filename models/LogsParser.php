<?php
namespace app\models;

use yii\base\Model;

class LogsParser extends Model
{
    public function parsingOfFile(){
        $fileDescriptor = fopen ("../logs.txt", "rt") or die ("Error! File was not opened.");
        $st = file_get_contents("../logs.txt");
        fclose($fileDescriptor);
        return $st;
    }
    public function parsingOfCategories(){
        $categories = array();
        $i = 0;
        $data = self::parsingOfFile();
        preg_match_all('|com/(\D+?)/|', $data, $res1);
        $buffer = array_unique($res1[1]);
        foreach ($buffer as $value)  {
            $categories[$i] = $value;
            ++$i;
        }
        return $categories;
    }
    public function parsingOfProducts($categories){
        $data = self::parsingOfFile();
        $products = array();
        for($i = 0; $i<count($categories); $i++) {
            preg_match_all('|' . $categories[$i] . '/(\D+?)/|', $data, $res1);
            $buffer[$categories[$i]] = array_unique($res1[1]);
            $products = array_merge($products, $buffer);
        }
        return $products;
    }
    public function parsingOfActions(){
        $data = self::parsingOfFile();
        $records = array();
        preg_match_all('~[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])~', $data, $res1);
        preg_match_all( '|[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*|', $data, $res2);
        preg_match_all('~([0-1]\d|2[0-3])(:[0-5]\d){2}~', $data, $res3);
        preg_match_all('|\[(\w+?)\]|', $data, $res4);
        preg_match_all('|com/(.*)|', $data, $res5);
        $dates =  $res1[0];
        $ipAddresses = $res2[0];
        $times = $res3[0];
        $codes = $res4[1];
        $links = $res5[1];
        foreach ($links as &$link) {
            if(strlen($link) == 0)
                $link = 'entrance';
        }
        for($i=0; $i<count($links); $i++) {
            $records[$i]['date'] = $dates[$i];
            $records[$i]['time'] = $times[$i];
            $records[$i]['code'] = $codes[$i];
            $records[$i]['ip'] = $ipAddresses[$i];
            $records[$i]['link'] = $links[$i];
        }
        return $records;
    }
    public function parsingOfAmountOfProducts($data){
        preg_match_all('|nt=([0-9]*)&ca|', $data, $res);
        $result = $res[1][0];
        return $result;
    }
    public function parsingOfIdCart($data){
        preg_match_all('|cart_id=([0-9]*)|', $data, $res);
        $result = $res[1][0];
        return $result;
    }
    public function parsingOfIdPaidCart($data){
        if(preg_match_all('|cart_id=([0-9]*)|', $data, $res))
            $result = $res[1][0];
        else{
            preg_match_all('|pay_([0-9]*)|', $data, $res);
            $result = $res[1][0];
        }

        return $result;
    }
}
