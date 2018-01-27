<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TaxYearCalculations;

/**
 * TaxYearCalculationsSearch represents the model behind the search form about `common\models\TaxYearCalculations`.
 */
class TaxYearCalculationsSearch extends TaxYearCalculations
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'calculated_at', 'calculated_by', 'year'], 'integer'],
            [['kt', 'dt', 'base', 'rate', 'amount', 'amount_fact', 'amount_to_pay', 'pf_base', 'pf_limit', 'pf_rate', 'pf_amount'], 'number'],
            [['calculation_details', 'comment'], 'safe'],
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
        $query = TaxYearCalculations::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
                'route' => 'tax-year-calculations',
            ],
            'sort' => [
                'route' => 'tax-year-calculations',
                'defaultOrder' => ['year' => SORT_DESC],
                'attributes' => [
                    'id',
                    'calculated_at',
                    'calculated_by',
                    'year',
                    'kt',
                    'dt',
                    'base',
                    'rate',
                    'min',
                    'amount',
                    'amount_fact',
                    'amount_to_pay',
                    'declared_at',
                    'paid_at',
                    'pf_base',
                    'pf_limit',
                    'pf_rate',
                    'pf_amount',
                    'pf_paid_at',
                    'calculation_details',
                    'comment'
                ],
            ],
        ]);

        $this->load($params);

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
            'year' => $this->year,
            'kt' => $this->kt,
            'dt' => $this->dt,
            'base' => $this->base,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'amount_fact' => $this->amount_fact,
            'amount_to_pay' => $this->amount_to_pay,
            'pf_base' => $this->pf_base,
            'pf_limit' => $this->pf_limit,
            'pf_rate' => $this->pf_rate,
            'pf_amount' => $this->pf_amount,
        ]);

        $query->andFilterWhere(['like', 'calculation_details', $this->calculation_details])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
