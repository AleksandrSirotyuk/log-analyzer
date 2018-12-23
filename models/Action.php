<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Action extends ActiveRecord
{
    public function addAction($ip, $code, $date_time, $id_cart, $product_id,$category_id, $is_transaction)
    {
        $this->ip_address = $ip;
        $this->code = $code;
        $this->date_time = $date_time;
        $this->id_cart = $id_cart;
        $this->scanned_product_id = $product_id;
        $this->scanned_category_id = $category_id;
        $this->is_transaction = $is_transaction;
        $this->save();
        return true;
    }
    public function getPrevActionByIP($ip){
            $record =Yii::$app->db->createCommand('SELECT * FROM `action` 
            WHERE `action`.`ip_address` = :ip 
            ORDER BY `action`.`id` DESC LIMIT 1',
        [   ':ip' => $ip])->queryAll();
            return $record;
    }
}
