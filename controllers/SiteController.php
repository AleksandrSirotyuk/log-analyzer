<?php

namespace app\controllers;

use app\models\Product;
use yii\web\Controller;
use app\models\LogsParser;
use app\models\Category;
use app\models\Action;
use app\models\Cart;
use app\models\Transaction;
use app\models\Report1Form;
use app\models\Report2Form;
use Yii;

class SiteController extends Controller
{
    public function actionIndex(){

        return $this->render('index');
    }
    public function actionReport1(){
        $report1Form = new Report1Form();
        if (isset($_POST['Report1Form'])) {
            $report1Form->attributes = Yii::$app->request->post('Report1Form');
            if($report1Form->validate()){
                $success_transactions = array();
                $actions = Action::find()
                    ->where(['between', 'date_time', $report1Form->date_from, $report1Form->date_to])
                    ->andWhere(['not', ['id_cart' => null]])
                    ->asArray()->all();
                for($i=0; $i<count($actions); $i++)
                    $success_transactions[$i] = Transaction::find()
                        ->Where(['id_cart' => $actions[$i]['id_cart']])
                        ->andWhere(['is_success_transaction' => '1'])
                        ->asArray()->one();
                $success_transactions = array_diff($success_transactions, array(''));
                $result1 = count($actions) - count($success_transactions);
            }
        }
        return $this->render('report1', compact('report1Form', 'result1'));
    }
    public function actionReport2(){
        $report2Form = new Report2Form();
        $ip_transactions = array();
        $count = 0;
        if (isset($_POST['Report2Form'])) {
            $report2Form->attributes = Yii::$app->request->post('Report2Form');
            if ($report2Form->validate()) {
                $result = 0;
                $transactions = Transaction::find()
                    ->where(['between', 'date_time', $report2Form->date_from, $report2Form->date_to])
                    ->andWhere(['is_success_transaction' => '1'])
                    ->asArray()->all();
                foreach ($transactions as $transaction) {
                    $ip_transactions[$count] = $transaction['ip_address'];
                    $count++;
                }
                $ip_transactions = array_unique($ip_transactions);
                for($i=0; $i<count($ip_transactions); $i++) {
                    $buffer = Transaction::find()
                        ->where(['between', 'date_time', $report2Form->date_from, $report2Form->date_to])
                        ->andWhere(['is_success_transaction' => '1'])
                        ->andWhere(['ip_address' => $ip_transactions[$i]])
                        ->asArray()->all();
                    if(count($buffer) > 1)
                        $result++;
                }
            }
        }
        return $this->render('report2', compact('report2Form', 'result', 'transactions', 'ip_transactions'));
    }
    public function actionReport3(){
        $theLastAction = Action::find()->orderBy(['id' => SORT_DESC])->one();
        $actions = array();
        $countActions = array();
        $i = 0;
        $startingDateTime = '2018-08-01 00:00:00';
        preg_match('|(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})|', $startingDateTime, $res);
        $date_time = self::toIntDateTime($res);
        while (true){
            $startingIterationDateTime = $date_time['year'].'-'
                .$date_time['month'].'-'.$date_time['day'].' '.$date_time['hour'].':'
                .$date_time['minute'].':'.$date_time['second'];
            $date_time['hour']+=1;
            $endingIterationDatetime = $date_time['year'].'-'
                .$date_time['month'].'-'.$date_time['day'].' '.$date_time['hour'].':'
                .$date_time['minute'].':'.$date_time['second'];
            $actions[$i] = Action::find()
                ->where(['between', 'date_time', $startingIterationDateTime, $endingIterationDatetime])
                ->asArray()->all();
           $countActions[$i] = count($actions[$i]);
            if($date_time['hour'] == 23) {
                $date_time['hour'] = 0;
                $date_time['day'] += 1;
                if( ($date_time['month'] == 2) && ($date_time['day'] == 28) ){
                    $date_time['day'] = 1;
                    $date_time['month'] += 1;
                }
                if( ($date_time['month'] % 2) == 0 && ($date_time['day'] == 30)
                    || ($date_time['month'] % 2) !=0 && ($date_time['day'] == 31) ) {
                    $date_time['day'] = 1;
                    $date_time['month'] += 1;
                }
            }
            if($actions[$i][$countActions[$i]-1]['id'] == $theLastAction['id'])
                break;
            $i++;
        }
        return $this->render('report3', compact('countActions', 'dataForGraph'));
    }
    public function actionUpdatingTables(){
        self::fillingOfTables(0);
        return $this->render('updating-tables');
    }
    public function actionUpdatingTablesForTesting(){
        self::fillingOfTables(1);
        return $this->render('updating-tables-for-testing');
    }
    public function actionClearTables(){
        self::cleaningOfTables();
        return $this->render('clear-tables');
    }
    public function fillingOfTables($testFlag){
        $logsParser = new LogsParser();

        /*Parsing of categories and filling of the table "Category"*/
        $categories = $logsParser->parsingOfCategories();
        for ($i=0; $i<count($categories); $i++) {
            if(Category::findOne(['name' => $categories[$i]]) == NULL) {
                $categoryModel = new Category();
                $categoryModel->addCategory($categories[$i]);
            }
        }

        /*Parsing of products and filling of the table "Product"*/
        $products = $logsParser->parsingOfProducts($categories);
        while (current($products)) {
            $category = Category::findOne(['name' => key($products)]);
            foreach ($products[key($products)] as $product) {
                if(Product::findOne(['name' => $product]) == NULL) {
                    $productModel = new Product();
                    $productModel->addProduct($product, $category['id']);
                }
            }
            next($products);
        }

        /*Parsing of products and filling of the table "Product"*/
        $actions = $logsParser->parsingOfActions();
        $category_id = NULL;
        $is_filling_of_a_cart = false;
        $product_id = NULL;
        $category_id = NULL;
        $is_transaction = '0';
        $is_entrance = false;
        $is_success_transaction = '0';
        $id_cart = NULL;

        for($i=0; $i<count($actions); $i++){
            if(Action::findOne(['code' => $actions[$i]['code']]) == NULL) {
                $actionModel = new Action();

                /* Merging of date and time */
                $date_time = $actions[$i]['date'].' '.$actions[$i]['time'];

                /* Is this action a entrance to the site? */
                if(preg_match('|entrance|', $actions[$i]['link']))
                    $is_entrance = true;

                /* Work with transactions */
                if(preg_match('|pay|', $actions[$i]['link']))
                    if(Transaction::findOne(['code' => $actions[$i]['code']]) == NULL) {
                        $is_transaction = '1';
                        if (preg_match('|success|', $actions[$i]['link']))
                            $is_success_transaction = '1';
                        $id_paid_cart = $logsParser->parsingOfIdPaidCart($actions[$i]['link']);
                        $transactionModel = new Transaction();
                        $transactionModel->addTransaction($actions[$i]['ip'], $actions[$i]['code'], $date_time, $id_paid_cart, $is_success_transaction);
                    }
                /* Filling of a cart */
                if(preg_match('|goods|', $actions[$i]['link'])) {
                    $id_cart = $logsParser->parsingOfIdCart($actions[$i]['link']);
                    if (Cart::findOne(['id' => $id_cart]) == NULL) {
                        $cartModel = new Cart();
                        $prevAction = $actionModel->getPrevActionByIP($actions[$i]['ip']);
                        $amountOfProducts = $logsParser->parsingOfAmountOfProducts($actions[$i]['link']);
                        $cartModel->addToCart($id_cart, $prevAction[0]['scanned_product_id'], $amountOfProducts);
                        $is_filling_of_a_cart = true;
                    }
                }

                /* Determining of "category_id" */
                if( ($is_transaction == '0') && ($is_filling_of_a_cart == false) && ($is_entrance == false)){
                    $namesOfCategories = Category::find()->asArray()->all();
                    for($j=0; $j<count($namesOfCategories); $j++) {
                        $pattern = '|'.$namesOfCategories[$j]['name'].'|';
                        if (preg_match($pattern, $actions[$i]['link'])){
                            $category_id = $j+1;
                            break;
                        }
                    }
                }

                /* Determining of "product_id" */
                if( ($is_transaction == '0') && ($is_filling_of_a_cart == false) && ($is_entrance == false)){
                    $namesOfProducts = Product::find()->asArray()->all();
                    for($j=0; $j<count($namesOfProducts); $j++) {
                        $pattern = '|'.$namesOfProducts[$j]['name'].'|';
                        if (preg_match($pattern, $actions[$i]['link'])){
                            $product_id = $j+1;
                            break;
                        }
                    }
                }

                /* Filling of the table "Action" */
                $actionModel->addAction($actions[$i]['ip'], $actions[$i]['code'], $date_time, $id_cart,
                    $product_id, $category_id, $is_transaction);

                /* Reset */
                if($is_transaction == '1')
                    $is_transaction = '0';
                if($is_entrance == true)
                    $is_entrance = false;
                if($product_id != NULL)
                    $product_id = NULL;
                if($category_id != NULL)
                    $category_id = NULL;
                if($is_filling_of_a_cart == true)
                    $is_filling_of_a_cart = false;
                if($is_success_transaction == '1')
                    $is_success_transaction = '0';
                if($id_cart != NULL)
                    $id_cart = NULL;
            }

            /* For TESTING! */
            if( ($testFlag == 1) && ($i == 499) )
                break;
        }
    }
    private function toIntDateTime($res){
        $date_time = array();
        $date_time['year'] = (int)$res[1];
        $date_time['month'] = (int)$res[2];
        $date_time['day'] = (int)$res[3];
        $date_time['hour'] = (int)$res[4];
        $date_time['minute'] = (int)$res[5];
        $date_time['second'] = (int)$res[6];
        return $date_time;
    }
    private function cleaningOfTables(){
        Yii::$app->db->createCommand('DELETE FROM `transaction`')->query();
        Yii::$app->db->createCommand('DELETE FROM `action`')->query();
        Yii::$app->db->createCommand('DELETE FROM `cart`')->query();
        Yii::$app->db->createCommand('ALTER TABLE `action` AUTO_INCREMENT = 1')->query();
        Yii::$app->db->createCommand('ALTER TABLE `transaction` AUTO_INCREMENT = 1')->query();
        Yii::$app->db->createCommand('DELETE FROM `product`;')->query();
        Yii::$app->db->createCommand('DELETE FROM `category`')->query();
        Yii::$app->db->createCommand('ALTER TABLE `product` AUTO_INCREMENT = 1')->query();
        Yii::$app->db->createCommand('ALTER TABLE `category` AUTO_INCREMENT = 1')->query();
    }
}
