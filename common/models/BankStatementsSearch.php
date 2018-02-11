<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BankStatements;
use yii\helpers\ArrayHelper;

/**
 * BankStatementsSearch represents the model behind the search form about `common\models\BankStatements`.
 */
class BankStatementsSearch extends BankStatements
{
    /**
     * Разделитель имен файлов для присоединяемого запроса
     */
    const FILES_DELIMITER = '|';

    /**
     * Значения для поля отбора "Направление движения"
     */
    const FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_ДОХОДЫ = 1;
    const FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_РАСХОДЫ = 2;
    const FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_ВСЕ = 3;

    /**
     * Значения для поля отбора "Форма оплаты"
     */
    const FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_БАНК = 1;
    const FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_НАЛ = 2;
    const FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_ВСЕ = 3;

    /**
     * Значения для поля отбора "Признание"
     */
    const FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_ДА = 1;
    const FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_НЕТ = 2;
    const FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_ВСЕ = 3;

    /**
     * Поле отбора, определяющее период (в том числе год).
     * @var string
     */
    public $searchPeriod;

    /**
     * Поле отбора, определяющее начало периода даты движения.
     * @var string
     */
    public $searchDateStart;

    /**
     * Поле отбора, определяющее окончания периода даты движения.
     * @var string
     */
    public $searchDateEnd;

    /**
     * Поле отбора, которое позволяет отобразить на выбор: доходы, расходы, все
     * @var integer
     */
    public $searchGroupDirection;

    /**
     * Поле отбора, которое позволяет отобразить на выбор: банк, наличные, все
     * @var integer
     */
    public $searchGroupPaymentMethod;

    /**
     * Поле отбора, которое позволяет отобразить на выбор: признаваемые, не признаваемые, все
     * @var integer
     */
    public $searchGroupActive;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'period_id', 'type', 'ca_id', 'is_active', 'searchPeriod', 'searchGroupDirection', 'searchGroupPaymentMethod', 'searchGroupActive'], 'integer'],
            [['bank_date', 'bank_dt', 'bank_kt', 'bank_bik_name', 'bank_doc_num', 'bank_description', 'inn'], 'safe'],
            [['bank_amount_dt', 'bank_amount_kt'], 'number'],
            // для отбора
            [['searchDateStart', 'searchDateEnd'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'searchDateStart' => 'Дата платежа с',
            'searchDateEnd' => 'По',
            'searchPeriod' => 'Период',
            'searchGroupDirection' => 'Направление движения',
            'searchGroupPaymentMethod' => 'Форма оплаты',
            'searchGroupActive' => 'Признание',
        ]);
    }

    /**
     * Возвращает набор значений для отбора по направлению движения.
     * @return array
     */
    public static function fetchFilterGroupDirections()
    {
        return [
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_ДОХОДЫ,
                'name' => 'Доходы',
                'hint' => 'Отбирать только поступления на р/с',
            ],
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_РАСХОДЫ,
                'name' => 'Расходы',
                'hint' => 'Отбирать только списания с р/с',
            ],
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_ВСЕ,
                'name' => 'Все',
                'hint' => 'Не применять отбор по направлению движения',
            ],
        ];
    }

    /**
     * Возвращает набор значений для отбора по форме оплаты.
     * @return array
     */
    public static function fetchFilterGroupPaymentMethods()
    {
        return [
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_БАНК,
                'name' => 'Банк',
                'hint' => 'Отбирать только движения по банку',
            ],
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_НАЛ,
                'name' => 'Наличные, карта',
                'hint' => 'Отбирать только введенные вручную',
            ],
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_ВСЕ,
                'name' => 'Все',
                'hint' => 'Не применять отбор по форме оплаты',
            ],
        ];
    }

    /**
     * Возвращает набор значений для отбора по признаваемости.
     * @return array
     */
    public static function fetchFilterGroupActive()
    {
        return [
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_ДА,
                'name' => 'Признаваемые',
                'hint' => 'Отбирать только те движения, которые признаются доходами или расходами',
            ],
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_НЕТ,
                'name' => 'Не признаваемые',
                'hint' => 'Отбирать только те движения, которые не признаются доходами или расходами',
            ],
            [
                'id' => self::FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_ВСЕ,
                'name' => 'Все',
                'hint' => 'Не применять отбор по признаваемости',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BankStatements::find();
        $query->select([
            '*',
            'id' => 'bank_statements.id',
            'filesCount' => 'files.count',
            'filesDetails' => 'files.details',
        ]);

        // LEFT JOIN выполняется быстрее, чем подзапрос в SELECT-секции
        // присоединяем количество файлов
        $query->leftJoin('(
            SELECT
                bank_statements_files.bs_id,
                COUNT(bank_statements_files.id) AS count,
                GROUP_CONCAT(bank_statements_files.fn SEPARATOR "' . self::FILES_DELIMITER . '") AS details
            FROM bank_statements_files
            GROUP BY bank_statements_files.bs_id
        ) AS files', '`bank_statements`.`id` = `files`.`bs_id`');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'route' => 'bank-statements',
                'defaultOrder' => ['bank_date' => SORT_ASC],
                'attributes' => [
                    'id',
                    'created_at',
                    'created_by',
                    'period_id',
                    'type',
                    'ca_id',
                    'is_active',
                    'bank_date',
                    'bank_dt:ntext',
                    'bank_kt:ntext',
                    'bank_amount_dt',
                    'bank_amount_kt',
                    'bank_bik_name:ntext',
                    'bank_doc_num',
                    'bank_description:ntext',
                    'inn',
                    'caName' => [
                        'asc' => ['counteragents.name' => SORT_ASC],
                        'desc' => ['counteragents.name' => SORT_DESC],
                    ],
                    'periodName' => [
                        'asc' => ['periods.name' => SORT_ASC],
                        'desc' => ['periods.name' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
        $query->joinWith(['ca', 'period']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'type' => $this->type,
            'ca_id' => $this->ca_id,
            'is_active' => $this->is_active,
            //'bank_date' => $this->bank_date,
            'bank_amount_dt' => $this->bank_amount_dt,
            'bank_amount_kt' => $this->bank_amount_kt,
        ]);

        // проверим, выбран ли период
        if ($this->searchPeriod != null)
            if ($this->searchPeriod > 2000)
                // выбран год
                $query->andFilterWhere([
                    'periods.year' => $this->searchPeriod,
                ]);
            else
                // выбран конкретный период
                $query->andFilterWhere([
                    'period_id' => $this->searchPeriod,
                ]);
        else
            // выбран конкретный период
            $query->andFilterWhere([
                'period_id' => $this->period_id,
            ]);

        if ($this->searchDateStart !== null or $this->searchDateEnd !== null)
            if ($this->searchDateStart !== '' && $this->searchDateEnd !== '') {
                // если указаны обе даты
                $query->andWhere(['between', '`bank_statements`.`bank_date`', $this->searchDateStart.' 00:00:00', $this->searchDateEnd.' 23:59:59']);
            }
            else if ($this->searchDateStart !== '' && $this->searchDateEnd === '') {
                // если указано только начало периода
                $query->andWhere(['>=','`bank_statements`.`bank_date`', $this->searchDateStart.' 00:00:00']);
            }
            else if ($this->searchDateStart === '' && $this->searchDateEnd !== '') {
                // если указан только конец периода
                $query->andWhere(['<=', '`bank_statements`.`bank_date`', $this->searchDateEnd.' 23:59:59']);
            };

        // отбор по направлению движения (доходы, расходы, все)
        if ($this->searchGroupDirection != null) {
            // доходы - это kt, расходы - это dt
            switch ($this->searchGroupDirection) {
                case self::FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_ДОХОДЫ:
                    $query->andWhere([
                        'or',
                        ['bank_amount_dt' => null],
                        ['bank_amount_dt' => 0],
                    ]);
                    break;
                case self::FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_РАСХОДЫ:
                    $query->andWhere([
                        'or',
                        ['bank_amount_kt' => null],
                        ['bank_amount_kt' => 0],
                    ]);
                    break;
            }
        }
        else $this->searchGroupDirection = self::FILTER_ЗНАЧЕНИЕ_НАПРАВЛЕНИЕ_ДВИЖЕНИЯ_ВСЕ;

        // отбор по форме оплаты (банк, нал, все)
        if ($this->searchGroupPaymentMethod != null) {
            // 0 - авто, 1 - вручную
            switch ($this->searchGroupPaymentMethod) {
                case self::FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_БАНК:
                    $query->andWhere([
                        'type' => 0,
                    ]);
                    break;
                case self::FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_НАЛ:
                    $query->andWhere([
                        'type' => 1,
                    ]);
                    break;
            }
        }
        else $this->searchGroupPaymentMethod = self::FILTER_ЗНАЧЕНИЕ_ФОРМА_ОПЛАТЫ_ВСЕ;

        // отбор по признаваемости в качестве дохода или расхода
        if ($this->searchGroupActive != null) {
            switch ($this->searchGroupActive) {
                case self::FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_ДА:
                    $query->andWhere([
                        'is_active' => true,
                    ]);
                    break;
                case self::FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_НЕТ:
                    $query->andWhere([
                        'is_active' => false,
                    ]);
                    break;
            }
        }
        else $this->searchGroupActive = self::FILTER_ЗНАЧЕНИЕ_ПРИЗНАНИЕ_ВСЕ;

        $query->andFilterWhere(['like', 'bank_dt', $this->bank_dt])
            ->andFilterWhere(['like', 'bank_kt', $this->bank_kt])
            ->andFilterWhere(['like', 'bank_bik_name', $this->bank_bik_name])
            ->andFilterWhere(['like', 'bank_doc_num', $this->bank_doc_num])
            ->andFilterWhere(['like', 'bank_description', $this->bank_description])
            ->andFilterWhere(['like', 'inn', $this->inn]);

        return $dataProvider;
    }
}
