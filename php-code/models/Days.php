<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "days".
 *
 * @property integer $id
 * @property string $weekday
 * @property integer $dayYear
 * @property string $dayMonth
 * @property integer $dayNumber
 *
 * @property Forecasts[] $forecasts
 */
class Days extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'days';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dayYear', 'dayNumber'], 'integer'],
            [['weekday', 'dayMonth'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'weekday' => 'Weekday',
            'dayYear' => 'Day Year',
            'dayMonth' => 'Day Month',
            'dayNumber' => 'Day Number',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForecasts()
    {
        return $this->hasMany(Forecasts::className(), ['dayId' => 'id']);
    }
}
