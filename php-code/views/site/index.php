<?php

/* @var $this yii\web\View */
/* @var $dayCollection array */
/* @var $forecastCollection array */

$this->title = 'YaWeather';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Сегодня</h1>
        <?php
        echo $this->render('day', ['day' => array_shift($dayCollection), 'forecastCollection' => array_shift($forecastCollection)]);
        ?>
        <h1>На неделю</h1>
    </div>

    <div class="body-content">
        <?php
        foreach ($dayCollection as $index => $day) {
            echo $this->render('day', ['day' => $day, 'forecastCollection' => $forecastCollection[$index]]);
        }
        ?>

    </div>
</div>
