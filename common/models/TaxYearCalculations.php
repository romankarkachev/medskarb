<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tax_year_calculations".
 *
 * @property integer $id
 * @property integer $calculated_at
 * @property integer $calculated_by
 * @property integer $year
 * @property string $kt
 * @property string $dt
 * @property string $base
 * @property string $rate
 * @property string $min
 * @property string $amount
 * @property string $amount_fact
 * @property string $amount_to_pay
 * @property string $declared_at
 * @property string $paid_at
 * @property string $pf_base
 * @property string $pf_limit
 * @property string $pf_rate
 * @property string $pf_amount
 * @property string $pf_paid_at
 * @property string $calculation_details
 * @property string $comment
 *
 * @property string $calculatedByName
 * @property string $calculatedByProfileName
 *
 * @property User $calculatedBy
 * @property Profile $calculatedByProfile
 */
class TaxYearCalculations extends \yii\db\ActiveRecord
{
    /**
     * Виртуальное поле
     * @var string дата крайнего срока оплаты
     */
    public $tax_pay_expired_at;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_year_calculations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year', 'kt', 'dt', 'base', 'rate', 'amount', 'amount_fact', 'amount_to_pay', 'pf_base', 'pf_limit', 'pf_rate', 'pf_amount'], 'required'],
            [['calculated_at', 'calculated_by', 'year'], 'integer'],
            [['kt', 'dt', 'base', 'rate', 'min', 'amount', 'amount_fact', 'amount_to_pay', 'pf_base', 'pf_limit', 'pf_rate', 'pf_amount'], 'number'],
            [['declared_at', 'paid_at', 'pf_paid_at'], 'safe'],
            [['calculation_details', 'comment'], 'string'],
            [['calculated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['calculated_by' => 'id']],
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
            'year' => 'Отчетный год',
            'kt' => 'Доходы',
            'dt' => 'Расходы',
            'base' => 'База налогообложения',
            'rate' => 'Ставка налога',
            'min' => 'Минимальный налог',
            'amount' => 'Сумма налога',
            'amount_fact' => 'Сумма фактически оплаченная',
            'amount_to_pay' => 'Сумма доплаты',
            'declared_at' => 'Дата подачи годовой декларации',
            'paid_at' => 'Дата оплаты годового налога',
            'pf_base' => 'База налогообложения',
            'pf_limit' => 'Сумма лимита',
            'pf_rate' => 'Ставка налога',
            'pf_amount' => 'Сумма налога',
            'pf_paid_at' => 'Дата оплаты в ПФ',
            'calculation_details' => 'Сводная подробная таблица за год в json',
            'comment' => 'Произвольный комментарий',
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
     * Производит расчет налога за переданный в параметрах год.
     * @param $year integer
     */
    public function calculateTaxAmount($year = null)
    {
        // извлекаем настройки
        $settings = Settings::findOne(1);
        if ($settings->tax_usn_rate != null) {
            $this->rate = $settings->tax_usn_rate;
            $this->pf_limit = $settings->tax_pf_limit;
            $this->pf_rate = $settings->tax_pf_rate;
        } else {
            $this->rate = 10;
            $this->pf_limit = 300000;
            $this->pf_rate = 1;
        }

        // УСН
        $calculations = BankStatements::find()
            ->select([
                'dt' => 'SUM(bank_amount_dt)',
                'kt' => 'SUM(bank_amount_kt)',
            ])
            ->leftJoin('periods', 'periods.id = bank_statements.period_id')
            // отбор только за выбранный период
            ->where(['periods.year' => $year])
            // только те движения, которые признаются в качестве доходов и расходов
            ->andWhere(['is_active' => true])
            ->groupBy('periods.year')->asArray()->one();
            //->asArray()->one();

        $this->dt = 0;
        $this->kt = 0;
        $this->base = 0;
        $this->amount = 0;

        if ($calculations != null) {
            $this->kt = round($calculations['kt']);
            $this->dt = round($calculations['dt']);
            $this->base = $this->kt - $this->dt;
            $this->amount = round($this->base * $this->rate / 100);
            $this->min = round($this->kt / 100, 2);
            $amount = round($this->base * $this->rate / 100);
            $this->amount = max($this->min, $amount);
        }
        unset($calculations);

        // фактические уплаченные налоги
        $calculations = TaxQuarterCalculations::find()
            ->select(['amount_fact' => 'SUM(amount_fact)'])
            ->leftJoin('periods', 'periods.id = tax_quarter_calculations.period_id')
            // отбор только за выбранный период
            ->where(['periods.year' => $year])
            ->scalar();
        if ($calculations != null) {
            $this->amount_fact = round($calculations);
            $this->amount_to_pay = $this->amount - $this->amount_fact;
        }

        // Пенсионный Фонд
        $this->pf_base = $this->kt;
        if ($this->pf_base > $this->pf_limit){
            $this->pf_amount = round(($this->pf_base - $this->pf_limit) * $this->pf_rate / 100);
        }
        else {
            $this->pf_amount = 0;
        }

        // попытаемся вычислить крайний срок оплаты
        try {
            $period = Periods::findOne(['year' => $year, 'quarter_num' => 4]);
            if ($period != null) $this->tax_pay_expired_at = $period->tax_pay_expired_at;
        }
        catch (\Exception $exception) {}
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
}
