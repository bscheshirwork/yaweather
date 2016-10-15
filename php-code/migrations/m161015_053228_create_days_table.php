<?php

use yii\db\Migration;

/**
 * Handles the creation for table `days`.
 */
class m161015_053228_create_days_table extends Migration
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
        $this->createTable('days', [
            'id' => $this->primaryKey(),
            'weekday' => $this->string(255),
            'dayYear' => $this->integer(),
            'dayMonth' => $this->string(255),
            'dayNumber' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('days');
    }
}
