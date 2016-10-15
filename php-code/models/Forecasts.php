<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "forecasts".
 *
 * @property integer $id
 * @property integer $dayId
 * @property string $dayPart
 * @property string $temp
 * @property string $condition
 * @property string $airPressure
 * @property integer $humidity
 * @property string $wind
 * @property string $windSpeed
 *
 * @property Days $day
 */
class Forecasts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'forecasts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dayId', 'humidity'], 'integer'],
            [['dayPart', 'temp', 'condition', 'airPressure', 'wind', 'windSpeed'], 'string', 'max' => 255],
            [['dayId'], 'exist', 'skipOnError' => true, 'targetClass' => Days::className(), 'targetAttribute' => ['dayId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dayId' => 'Day ID',
            'dayPart' => 'Day Part',
            'temp' => 'Temp',
            'condition' => 'Condition',
            'airPressure' => 'Air Pressure',
            'humidity' => 'Humidity',
            'wind' => 'Wind',
            'windSpeed' => 'Wind Speed',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDay()
    {
        return $this->hasOne(Days::className(), ['id' => 'dayId']);
    }
}
