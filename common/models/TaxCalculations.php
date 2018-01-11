<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tax_calculations".
 *
 * @property integer $id
 * @property integer $calculated_at
 * @property integer $calculated_by
 * @property integer $period_id
 * @property string $dt
 * @property string $kt
 * @property string $diff
 * @property string $rate
 * @property string $amount
 * @property string $amount_fact
 * @property string $min
 * @property string $comment
 *
 * @property string $periodName
 * @property integer $periodStart
 *
 * @property User $calculatedBy
 * @property Periods $period
 */
class TaxCalculations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_calculations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period_id', 'dt', 'kt', 'diff', 'rate', 'amount', 'min'], 'required'],
            [['calculated_at', 'calculated_by', 'period_id'], 'integer'],
            [['dt', 'kt', 'diff', 'rate', 'amount', 'amount_fact', 'min'], 'number'],
            [['comment'], 'string'],
            [['calculated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['calculated_by' => 'id']],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => Periods::className(), 'targetAttribute' => ['period_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calculated_at' => 'Дата и время расчета',
            'calculated_by' => 'Автор расчета',
            'period_id' => 'Период',
            'dt' => 'Расходы',
            'kt' => 'Доходы',
            'diff' => 'Разница',
            'rate' => 'Ставка налога',
            'amount' => 'Сумма налога',
            'amount_fact' => 'Сумма налога, уплаченная по факту',
            'min' => 'Минимальный налог',
            'comment' => 'Примечание',
            // для сортировки
            'periodName' => 'Период', // период
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['calculated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['calculated_at'],
                ],
            ],
            'blameable' => [
                'class' => 'yii\behaviors\BlameableBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['calculated_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['calculated_by'],
                ],
            ],
        ];
    }

    /**
     * Производит расчет налога за текущий квартал.
     * @param $period \common\models\Periods
     */
    public function calculateTaxAmountByPeriod($period = null)
    {
        if ($period == null) $period = Periods::getCurrentPeriod();
        $this->rate = 10;
        $calculations = BankStatements::find()
            ->select([
                'dt' => 'SUM(bank_amount_dt)',
                'kt' => 'SUM(bank_amount_kt)',
            ])
            // отбор только за выбранный период
            ->where(['period_id' => $period->id])
            // только те движения, которые признаются в качестве доходов и расходов
            ->andWhere(['is_active' => true])
            ->groupBy('period_id')->asArray()->one();

        $this->dt = 0;
        $this->kt = 0;
        $this->diff = 0;
        $this->min = 0;
        $this->amount = 0;

        if ($calculations != null) {
            $this->dt = floatval($calculations['dt']);
            $this->kt = floatval($calculations['kt']);
            $this->diff = $this->kt - $this->dt;
            $this->min = round($this->kt / 100, 2);
            $amount = $this->diff * $this->rate / 100;
            $this->amount = max($this->min, $amount);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalculatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'calculated_by']);
    }

    /**
     * Возвращает имя автора-создателя в виде ivan (Иван).
     * @return string
     */
    public function getCalculatedByName()
    {
        return $this->calculated_by == null ? '' : ($this->calculatedBy->profile == null ? $this->calculatedBy->username :
            $this->calculatedBy->username . ' (' . $this->calculatedBy->profile->name . ')');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriod()
    {
        return $this->hasOne(Periods::className(), ['id' => 'period_id']);
    }

    /**
     * Возвращает наименование периода.
     * @return string
     */
    public function getPeriodName()
    {
        return $this->period == null ? '' : $this->period->name;
    }

    /**
     * Возвращает начало периода.
     * @return string
     */
    public function getPeriodStart()
    {
        return $this->period == null ? '' : $this->period->start;
    }
}
