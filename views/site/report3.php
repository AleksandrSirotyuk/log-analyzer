<?php
$this->title = 'Отчет №3';
use miloschuman\highcharts\Highcharts;
?>
<div class="container-fluid" align="center">
    <div class="col-md-6 col-md-offset-3">
        <h3>Отчет №3: нагрузка (число запросов) на сайт за астрономический час</h3>
    </div>
    <?php
    $resultData = ['name' => 'Число запросов', 'data' => []];
    $resultData['data'] = $countActions;
    echo Highcharts::widget([
        'options' => [
            'title' => ['text' => 'График почасовой нагрузки'],
            'xAxis' => [
                'categories' => [$dataForGraph],
                'title' => ['text' => 'Часы']
            ],
            'yAxis' => [
                'title' => ['text' => 'Число запросов']
            ],
            'series' => [$resultData]

        ]
    ]);
    ?>
</div>

