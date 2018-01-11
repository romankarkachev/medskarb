<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "periods".
 *
 * @property integer $id
 * @property string $name
 * @property integer $start
 * @property integer $end
 * @property integer $quarter_num
 * @property integer $year
 *
 * @property BankStatements[] $bankStatements
 * @property TaxCalculations[] $taxCalculations
 */
class Periods extends \yii\db\ActiveRecord
{
    /**
     * Временные реквизиты, чтобы задать полностью весь выбранный день.
     * @var string
     */
    public $temp_start;
    public $temp_end;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'periods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'temp_start', 'temp_end'], 'required'],
            [['start', 'end', 'quarter_num', 'year'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['temp_start', 'temp_end'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'start' => 'Начало периода',
            'end' => 'Конец периода',
            'quarter_num' => 'Номер квартала',
            'year' => 'Номер года',
            'temp_start' => 'Начало периода',
            'temp_end' => 'Конец периода',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->start = strtotime($this->temp_start . ' 00:00:00');
            $this->end = strtotime($this->temp_end . ' 23:59:59');
            return true;
        }
        return false;
    }

    /**
     * Получает период по текущему году и номеру квартала, переданному в параметрах.
     * @param $quarter integer номер квартала
     * @param $year integer год
     * @return \yii\db\ActiveRecord|null
     */
    public static function getPeriodByYearAndQuarter($quarter, $year = null)
    {
        if ($year == null) $year = intval(date('Y'));
        return Periods::find()
            ->select('id')
            ->where(['FROM_UNIXTIME(`start`, "%Y")' => $year])
            ->andWhere(['quarter_num' => $quarter])
            ->one();
    }

    /**
     * Получает и возвращает в случае успеха предыдущий квартал (от текущей даты).
     * @return \yii\db\ActiveRecord|null
     */
    public static function getPreviousPeriod()
    {
        $previous_quarter = intval((date('n')+2)/3) - 1;
        $previous_year = intval(date('Y'));
        if ($previous_quarter < 1) {
            $previous_quarter = 4;
            $previous_year--;
        }
        return self::getPeriodByYearAndQuarter($previous_quarter, $previous_year);
    }

    /**
     * Получает и возвращает в случае успеха текущий квартал (от текущей даты).
     * @return \yii\db\ActiveRecord|null
     */
    public static function getCurrentPeriod()
    {
        $current_quarter = intval((date('n')+2)/3);
        return self::getPeriodByYearAndQuarter($current_quarter);
    }

    /**
     * Делает выборку периодов и возвращает в виде массива.
     * Применяется для вывода в виджетах Select2.
     * @return array
     */
    public static function arrayMapForSelect2()
    {
        return ArrayHelper::map(Periods::find()->orderBy('start DESC')->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBankStatements()
    {
        return $this->hasMany(BankStatements::className(), ['period_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxCalculations()
    {
        return $this->hasMany(TaxCalculations::className(), ['period_id' => 'id']);
    }
}
