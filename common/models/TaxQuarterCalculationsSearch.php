<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TaxQuarterCalculations;
use yii\helpers\ArrayHelper;

/**
 * TaxQuarterCalculationsSearch represents the model behind the search form about `common\models\TaxQuarterCalculations`.
 */
class TaxQuarterCalculationsSearch extends TaxQuarterCalculations
{
    /**
     * Поле отбора, определяющее период (в том числе год).
     * @var string
     */
    public $searchPeriod;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'calculated_at', 'calculated_by', 'period_id', 'searchPeriod'], 'integer'],
            [['dt', 'kt', 'diff', 'rate', 'amount', 'amount_fact'], 'number'],
            [['paid_at', 'comment'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
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
        $query = TaxQuarterCalculations::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
                'route' => 'tax-quarter-calculations',
            ],
            'sort' => [
                'route' => 'tax-quarter-calculations',
                'defaultOrder' => ['periodStart' => SORT_DESC],
                'attributes' => [
                    'id',
                    'calculated_at',
                    'calculated_by',
                    'period_id',
                    'dt',
                    'kt',
                    'diff',
                    'rate',
                    'amount',
                    'amount_fact',
                    'comment',
                    'periodName' => [
                        'asc' => ['periods.name' => SORT_ASC],
                        'desc' => ['periods.name' => SORT_DESC],
                    ],
                    'periodStart' => [
                        'asc' => ['periods.start' => SORT_ASC],
                        'desc' => ['periods.start' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
        $query->joinWith(['period']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'calculated_at' => $this->calculated_at,
            'calculated_by' => $this->calculated_by,
            'dt' => $this->dt,
            'kt' => $this->kt,
            'diff' => $this->diff,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'amount_fact' => $this->amount_fact,
            'paid_at' => $this->paid_at,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
