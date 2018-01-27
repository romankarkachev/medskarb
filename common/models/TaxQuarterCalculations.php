<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tax_quarter_calculations".
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
 * @property string $paid_at
 * @property string $comment
 *
 * @property string $calculatedByName
 * @property string $calculatedByProfileName
 * @property string $periodName
 * @property integer $periodStart
 *
 * @property User $calculatedBy
 * @property Profile $calculatedByProfile
 * @property Periods $period
 */
class TaxQuarterCalculations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_quarter_calculations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period_id', 'dt', 'kt', 'diff', 'rate', 'amount'], 'required'],
            [['calculated_at', 'calculated_by', 'period_id'], 'integer'],
            ['period_id', 'unique'],
            [['dt', 'kt', 'diff', 'rate', 'amount', 'amount_fact'], 'number'],
            [['paid_at'], 'safe'],
            [['comment'], 'string'],
            [['calculated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['calculated_by' => 'id']],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => Periods::className(), 'targetAttribute' => ['period_id' => 'id']],
            // собственные правила валидации
            ['amount_fact', 'validateAmountFact'],
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
            'diff' => 'База налогообложения',
            'rate' => 'Ставка налога',
            'amount' => 'Сумма налога',
            'amount_fact' => 'Сумма налога, уплаченная по факту',
            'paid_at' => 'Дата оплаты налога',
            'comment' => 'Примечание',
            // вычисляемые поля
            'periodName' => 'Период',
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

    public function validateAmountFact()
    {
        if ($this->amount_fact != null && $this->amount_fact > 0 && $this->paid_at == null)
            $this->addError('paid_at', 'Если налог оплачен, дата оплаты обязательна.');
    }

    /**
     * Производит расчет налога за переданный в параметрах квартал.
     * @param $period \common\models\Periods
     */
    public function calculateTaxAmountByPeriod($period = null)
    {
        if ($period == null) $period = Periods::getCurrentPeriod();
        // извлекаем настройки
        $settings = Settings::findOne(1);

        if ($settings->tax_usn_rate != null) $this->rate = $settings->tax_usn_rate; else $this->rate = 10;
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
        $this->amount = 0;

        // поищем движение с оплатой налога в следующем квартале
        if ($period->quarter_num < 4) try {
            // определим следующий квартал, поскольку оплата будет в следующем квартале
            if ($period->quarter_num != null && $period->year != null)
                $nextPeriod = Periods::find()->where(['quarter_num' => ($period->quarter_num + 1), 'year' => $period->year])->one();

            if (isset($nextPeriod)) {
                $query = BankStatements::find()
                    ->where(['like', 'bank_description', 'доходы минус расходы'])
                    ->andWhere(['period_id' => $nextPeriod->id]);

                if ($settings->tax_inspection_id != null)
                    $query->andWhere([
                        'or',
                        'ca_id' => $settings->taxInspection->id,
                        'inn' => $settings->taxInspection->inn,
                    ]);

                $paidFact = $query->all();
                if (count($paidFact) == 1) {
                    $amountFact = $paidFact[0]->bank_amount_dt;
                    $amountFactPaidAt = $paidFact[0]->bank_date;
                }
            }
        }
        catch (\Exception $exception) {}

        if ($calculations != null) {
            $this->dt = floatval($calculations['dt']);
            $this->kt = floatval($calculations['kt']);
            $this->diff = $this->kt - $this->dt;
            $this->amount = round($this->diff * $this->rate / 100);
            if (isset($amountFact)) $this->amount_fact = $amountFact;
            if (isset($amountFactPaidAt)) $this->paid_at = $amountFactPaidAt;
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
     * @return \yii\db\ActiveQuery
     */
    public function getCalculatedByProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'calculated_by']);
    }

    /**
     * Возвращает имя пользователя, который последним производил расчеты в виде profileName (username).
     * @return string
     */
    public function getCalculatedByProfileName()
    {
        return $this->calculatedByProfile != null ? ($this->calculatedByProfile->name != null ? $this->calculatedByProfile->name : $this->calculatedBy->username) : '';
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
