<?php

/* @var $this yii\web\View */
/* @var $day \app\models\Days */
/* @var $forecastCollection \app\models\Forecasts[] */

?>

        <div class="row">
            <div class="col-lg-4">
                <h2><?= $day->dayNumber?> <?= $day->dayMonth?> </h2>
            </div>
            <div class="col-lg-6">
                <?php
                if (isset($forecastCollection) && is_array($forecastCollection))
                    foreach($forecastCollection as $forecast): ?>
                <p><?= $forecast->dayPart ?> <?= $forecast->temp?> <?= $forecast->condition?> <?= $forecast->airPressure?> <p>
                <?php endforeach;?>

            </div>
        </div>
<div class="separator">
    <hr>
</div>
