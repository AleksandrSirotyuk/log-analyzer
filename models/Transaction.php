<?php

namespace app\models;

use yii\db\ActiveRecord;

class Transaction extends ActiveRecord
{
    public function addTransaction($ip, $code, $date_time, $id_cart, $is_success_transaction)
    {
        $this->ip_address = $ip;
        $this->code = $code;
        $this->date_time = $date_time;
        $this->id_cart = $id_cart;
        $this->is_success_transaction = $is_success_transaction;
        $this->save();
        return true;
    }
}