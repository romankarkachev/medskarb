<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TaxCalculations;

/**
 * TaxCalculationsSearch represents the model behind the search form about `common\models\TaxCalculations`.
 */
class TaxCalculationsSearch extends TaxCalculations
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'calculated_at', 'calculated_by', 'period_id'], 'integer'],
            [['dt', 'kt', 'diff', 'rate', 'amount', 'min'], 'number'],
            [['comment'], 'safe'],
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
        $query = TaxCalculations::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
                'route' => 'tax-calculations',
            ],
            'sort' => [
                'route' => 'tax-calculations',
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
                    'min',
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'calculated_at' => $this->calculated_at,
            'calculated_by' => $this->calculated_by,
            'period_id' => $this->period_id,
            'dt' => $this->dt,
            'kt' => $this->kt,
            'diff' => $this->diff,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'min' => $this->min,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
