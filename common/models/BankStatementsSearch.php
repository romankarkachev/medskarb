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
     * Разделитель имен файлов для присоединяемого запроса.
     */
    const FILES_DELIMITER = '|';

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'period_id', 'type', 'ca_id', 'is_active', 'searchPeriod'], 'integer'],
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
        ]);
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
                $query->andFilterWhere(['between', '`bank_statements`.`bank_date`', $this->searchDateStart.' 00:00:00', $this->searchDateEnd.' 23:59:59']);
            }
            else if ($this->searchDateStart !== '' && $this->searchDateEnd === '') {
                // если указано только начало периода
                $query->andFilterWhere(['>=','`bank_statements`.`bank_date`', $this->searchDateStart.' 00:00:00']);
            }
            else if ($this->searchDateStart === '' && $this->searchDateEnd !== '') {
                // если указан только конец периода
                $query->andFilterWhere(['<=', '`bank_statements`.`bank_date`', $this->searchDateEnd.' 23:59:59']);
            };

        $query->andFilterWhere(['like', 'bank_dt', $this->bank_dt])
            ->andFilterWhere(['like', 'bank_kt', $this->bank_kt])
            ->andFilterWhere(['like', 'bank_bik_name', $this->bank_bik_name])
            ->andFilterWhere(['like', 'bank_doc_num', $this->bank_doc_num])
            ->andFilterWhere(['like', 'bank_description', $this->bank_description])
            ->andFilterWhere(['like', 'inn', $this->inn]);

        return $dataProvider;
    }
}
