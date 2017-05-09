<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Deals;

/**
 * DealsSearch represents the model behind the search form about `common\models\Deals`.
 */
class DealsSearch extends Deals
{
    /**
     * Поле отбора, определяющее начало периода даты сделки.
     * @var string
     */
    public $searchDateStart;

    /**
     * Поле отбора, определяющее окончания периода даты сделки.
     * @var string
     */
    public $searchDateEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'customer_id', 'contract_id', 'broker_lnr_id', 'broker_ru_id', 'is_closed'], 'integer'],
            [['deal_date'], 'safe'],
            // для отбора
            [['searchDateStart', 'searchDateEnd'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['searchDateStart'] = 'Дата сделки с';
        $labels['searchDateEnd'] = 'По';

        return $labels;
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
        $query = Deals::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
                'route' => 'deals',
            ],
            'sort' => [
                'route' => 'deals',
                'defaultOrder' => ['deal_date' => SORT_DESC],
                'attributes' => [
                    'id',
                    'deal_date',
                    'customer_id',
                    'contract_id',
                    'broker_ru_id',
                    'broker_lnr_id',
                    'is_closed',
                    'customerName' => [
                        'asc' => ['customer.name' => SORT_ASC],
                        'desc' => ['customer.name' => SORT_DESC],
                    ],
                    'contractRep' => [
                        'asc' => ['documents.doc_date' => SORT_ASC, 'documents.doc_num' => SORT_ASC],
                        'desc' => ['documents.doc_date' => SORT_DESC, 'documents.doc_num' => SORT_DESC],
                    ],
                    'brokerRuName' => [
                        'asc' => ['broker_ru.name' => SORT_ASC],
                        'desc' => ['broker_ru.name' => SORT_DESC],
                    ],
                    'brokerLnrName' => [
                        'asc' => ['broker_lnr.name' => SORT_ASC],
                        'desc' => ['broker_lnr.name' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
        $query->joinWith(['customer', 'contract', 'brokerRu', 'brokerLnr']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'deals.id' => $this->id,
            'deals.created_at' => $this->created_at,
            'deals.created_by' => $this->created_by,
            'deals.updated_at' => $this->updated_at,
            'deals.updated_by' => $this->updated_by,
            'deals.customer_id' => $this->customer_id,
            'contract_id' => $this->contract_id,
            'broker_ru_id' => $this->broker_ru_id,
            'broker_lnr_id' => $this->broker_lnr_id,
            'is_closed' => $this->is_closed,
        ]);

        if ($this->searchDateStart !== null or $this->searchDateEnd !== null)
            if ($this->searchDateStart !== '' && $this->searchDateEnd !== '') {
                // если указаны обе даты
                $query->andFilterWhere(['between', '`deal_date`', $this->searchDateStart.' 00:00:00', $this->searchDateEnd.' 23:59:59']);
            }
            else if ($this->searchDateStart !== '' && $this->searchDateEnd === '') {
                // если указано только начало периода
                $query->andFilterWhere(['>=','`deal_date`', $this->searchDateStart.' 00:00:00']);
            }
            else if ($this->searchDateStart === '' && $this->searchDateEnd !== '') {
                // если указан только конец периода
                $query->andFilterWhere(['<=', '`deal_date`', $this->searchDateEnd.' 23:59:59']);
            };

        return $dataProvider;
    }
}
