<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Json;

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
 * @property string $tdr020
 * @property string $tdr040
 * @property string $tdr070
 * @property string $tdr100
 * @property string $tdr210
 * @property string $tdr211
 * @property string $tdr212
 * @property string $tdr213
 * @property string $tdr220
 * @property string $tdr221
 * @property string $tdr222
 * @property string $tdr223
 * @property string $tdr240
 * @property string $tdr241
 * @property string $tdr242
 * @property string $tdr243
 * @property string $tdr270
 * @property string $tdr271
 * @property string $tdr272
 * @property string $tdr273
 * @property string $declared_at
 * @property string $paid_fact
 * @property string $paid_at
 * @property string $pf_base
 * @property string $pf_limit
 * @property string $pf_rate
 * @property string $pf_amount
 * @property string $pf_paid_at
 * @property string $calculation_details
 * @property string $tdm
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
            [['kt', 'dt', 'base', 'rate', 'min', 'amount', 'amount_fact', 'amount_to_pay', 'tdr020', 'tdr040', 'tdr070', 'tdr100', 'tdr210', 'tdr211', 'tdr212', 'tdr213', 'tdr220', 'tdr221', 'tdr222', 'tdr223', 'tdr240', 'tdr241', 'tdr242', 'tdr243', 'tdr270', 'tdr271', 'tdr272', 'tdr273', 'paid_fact', 'pf_base', 'pf_limit', 'pf_rate', 'pf_amount'], 'number'],
            [['declared_at', 'paid_at', 'pf_paid_at'], 'safe'],
            [['calculation_details', 'tdm', 'comment'], 'string'],
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
            'tdr020' => 'Сумма авансового платежа I квартал',
            'tdr040' => 'Сумма авансового платежа II квартал',
            'tdr070' => 'Сумма авансового платежа III квартал',
            'tdr100' => 'Сумма налога, подлежащая доплате за налоговый период',
            'tdr210' => 'Доходы за первый квартал',
            'tdr211' => 'Доходы за полугодие',
            'tdr212' => 'Доходы за девять месяцев',
            'tdr213' => 'Доходы за налоговый период',
            'tdr220' => 'Расходы за первый квартал',
            'tdr221' => 'Расходы за полугодие',
            'tdr222' => 'Расходы за девять месяцев',
            'tdr223' => 'Расходы за налоговый период',
            'tdr240' => 'Налоговая база за первый квартал',
            'tdr241' => 'Налоговая база за полугодие',
            'tdr242' => 'Налоговая база за девять месяцев',
            'tdr243' => 'Налоговая база за налоговый период',
            'tdr270' => 'Налог исчисленный за первый квартал',
            'tdr271' => 'Налог исчисленный за полугодие',
            'tdr272' => 'Налог исчисленный за девять месяцев',
            'tdr273' => 'Налог исчисленный за налоговый период',
            'declared_at' => 'Дата подачи годовой декларации',
            'paid_fact' => 'Доплачено фактически',
            'paid_at' => 'Дата оплаты годового налога',
            'pf_base' => 'База налогообложения',
            'pf_limit' => 'Сумма лимита',
            'pf_rate' => 'Ставка налога',
            'pf_amount' => 'Сумма налога',
            'pf_paid_at' => 'Дата оплаты в ПФ',
            'calculation_details' => 'Сводная подробная таблица за год в json',
            'tdm' => 'Данные для декларации в json',
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
     * Строит таблицу с исходными для расчетов данными.
     * @param $year
     * @return array
     */
    public function buildDetailedTaxTable($year)
    {
        $query = Periods::find()->select([
            'id' => 'periods.id',
            'period_name' => 'periods.name',
            'q_num' => 'periods.quarter_num',
            'bsKtCrude',
            'bsKtExclude',
            'bsKt',
            'bsDtCrude',
            'bsDtExclude',
            'bsDt',
            'amountFact',
        ])->where(['periods.year' => $year]);

        // ДОХОДНАЯ ЧАСТЬ
        // присоединяем "грязные" доходы
        $subQuery = (new Query())->select([
            'bank_statements.period_id',
            'bsKtCrude' => 'SUM(`bank_amount_kt`)',
        ])->from('bank_statements')
            ->leftJoin('periods', '`periods`.`id` = `bank_statements`.`period_id`')
            ->where(['`periods`.`year`' => $year])
            ->groupBy('`bank_statements`.`period_id`');
        $query->leftJoin(['bs_kt_crude' => $subQuery], '`bs_kt_crude`.`period_id` = `periods`.`id`');
        unset($subQuery);

        // присоединяем доходы, исключаемые из декларации
        $subQuery = (new Query())->select([
            'bank_statements.period_id',
            'bsKtExclude' => 'SUM(`bank_amount_kt`)',
        ])->from('bank_statements')
            ->leftJoin('periods', '`periods`.`id` = `bank_statements`.`period_id`')
            ->where(['`periods`.`year`' => $year])
            ->andWhere(['is_active' => false])
            ->groupBy('`bank_statements`.`period_id`');
        $query->leftJoin(['bs_kt_exclude' => $subQuery], '`bs_kt_exclude`.`period_id` = `periods`.`id`');
        unset($subQuery);

        // присоединяем доходы, которые принимаются к расчету
        $subQuery = (new Query())->select([
            'bank_statements.period_id',
            'bsKt' => 'SUM(`bank_amount_kt`)',
        ])->from('bank_statements')
            ->leftJoin('periods', '`periods`.`id` = `bank_statements`.`period_id`')
            ->where(['`periods`.`year`' => $year])
            ->andWhere(['is_active' => true])
            ->groupBy('`bank_statements`.`period_id`');
        $query->leftJoin(['bs_kt' => $subQuery], '`bs_kt`.`period_id` = `periods`.`id`');
        unset($subQuery);

        // РАСХОДНАЯ ЧАСТЬ
        // присоединяем "грязные" расходы
        $subQuery = (new Query())->select([
            'bank_statements.period_id',
            'bsDtCrude' => 'SUM(`bank_amount_dt`)',
        ])->from('bank_statements')
            ->leftJoin('periods', '`periods`.`id` = `bank_statements`.`period_id`')
            ->where(['`periods`.`year`' => $year])
            ->groupBy('`bank_statements`.`period_id`');
        $query->leftJoin(['bs_dt_crude' => $subQuery], '`bs_dt_crude`.`period_id` = `periods`.`id`');

        // присоединяем расходы, исключаемые из декларации
        $subQuery = (new Query())->select([
            'bank_statements.period_id',
            'bsDtExclude' => 'SUM(`bank_amount_dt`)',
        ])->from('bank_statements')
            ->leftJoin('periods', '`periods`.`id` = `bank_statements`.`period_id`')
            ->where(['`periods`.`year`' => $year])
            ->andWhere(['is_active' => false])
            ->groupBy('`bank_statements`.`period_id`');
        $query->leftJoin(['bs_dt_exclude' => $subQuery], '`bs_dt_exclude`.`period_id` = `periods`.`id`');
        unset($subQuery);

        // присоединяем расходы, которые принимаются к расчету
        $subQuery = (new Query())->select([
            'bank_statements.period_id',
            'bsDt' => 'SUM(`bank_amount_Dt`)',
        ])->from('bank_statements')
            ->leftJoin('periods', '`periods`.`id` = `bank_statements`.`period_id`')
            ->where(['`periods`.`year`' => $year])
            ->andWhere(['is_active' => true])
            ->groupBy('`bank_statements`.`period_id`');
        $query->leftJoin(['bs_dt' => $subQuery], '`bs_dt`.`period_id` = `periods`.`id`');
        unset($subQuery);

        // ПРОЧЕЕ
        // присоединяем фактически уплаченные суммы
        $subQuery = (new Query())->select([
            'tax_quarter_calculations.period_id',
            'amountFact' => 'amount_fact',
        ])->from('tax_quarter_calculations')
            ->leftJoin('periods', '`periods`.`id` = `tax_quarter_calculations`.`period_id`')
            ->where(['`periods`.`year`' => $year])
            ->groupBy('`tax_quarter_calculations`.`period_id`');
        $query->leftJoin(['fact' => $subQuery], '`fact`.`period_id` = `periods`.`id`');
        unset($subQuery);

        return $query->asArray()->all();
    }

    /**
     * @param $calculationsTable array
     */
    public function buildTaxDeclarationMeasurements($calculationsTable)
    {
        $result = [];
        if ($this->rate == 0 || $this->rate == null) return;

        $prevKt = 0;
        $prevDt = 0;
        $prevBase = 0;
        $prevTax = 0;
        $totalFact = 0;
        foreach ($calculationsTable as $row) {
            $kt = round($row['bsKt'] + $prevKt);
            $dt = round($row['bsDt'] + $prevDt);
            $base = $kt - $dt;
            $tax = round($base / $this->rate);
            $taxQ = $tax - $prevTax;
            $fact = $row['amountFact'];

            // заполним графы для декларации
            switch ($row['q_num']) {
                case 1:
                    $this->tdr020 = $taxQ;
                    $this->tdr210 = $kt;
                    $this->tdr220 = $dt;
                    $this->tdr240 = $base;
                    $this->tdr270 = $tax;
                    break;
                case 2:
                    $this->tdr040 = $taxQ;
                    $this->tdr211 = $kt;
                    $this->tdr221 = $dt;
                    $this->tdr241 = $base;
                    $this->tdr271 = $tax;
                    break;
                case 3:
                    $this->tdr070 = $taxQ;
                    $this->tdr212 = $kt;
                    $this->tdr222 = $dt;
                    $this->tdr242 = $base;
                    $this->tdr272 = $tax;
                    break;
                case 4:
                    $this->tdr100 = $taxQ;
                    $this->tdr213 = $kt;
                    $this->tdr223 = $dt;
                    $this->tdr243 = $base;
                    $this->tdr273 = $tax;

                    if ($row['amountFact'] == null) $fact = $tax - $totalFact;
                    break;
            }

            $result[] = [
                'period_name' => $row['period_name'],
                'q_num' => $row['q_num'],
                'kt' => $kt, // доходы
                'dt' => $dt, // расходы
                'base' => $base, // база налогообложения
                'tax' => $tax, // налог нарастающим итогом
                'taxQ' => $taxQ, // налог за квартал
                'fact' => $fact, // оплачено за квартал фактически
            ];

            $prevKt += $row['bsKt'];
            $prevDt += $row['bsDt'];
            $prevBase += $base;
            $prevTax = $tax;
            $totalFact += $row['amountFact'];
        }

        $this->tdm = Json::encode($result);
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

        // если год не передается в параметрах, берем текущий
        if ($year == null) $year = date('Y');

        // сформируем исходную таблицу
        $calculationsTable = $this->buildDetailedTaxTable($year);
        $this->calculation_details = Json::encode($calculationsTable);
        $this->buildTaxDeclarationMeasurements($calculationsTable);

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
