<?php

use yii\db\Migration;

/**
 * Handles the creation for table `forecasts`.
 */
class m161015_053432_create_forecasts_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('forecasts', [
            'id' => $this->primaryKey(),
            'dayId' => $this->integer(),
            'dayPart' => $this->string(255),
            'temp' => $this->string(255),
            'condition' => $this->string(255),
            'airPressure' => $this->string(255),
            'humidity' => $this->integer(),
            'wind' => $this->string(255),
            'windSpeed' => $this->string(255), //$this->float(), //упрощения для сохранения с форматом
        ], $tableOptions);
        $this->addForeignKey('fk_forecasts_2_day', 'forecasts', 'dayId', 'days', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_forecasts_2_day', 'forecasts');
        $this->dropTable('forecasts');
    }
}
