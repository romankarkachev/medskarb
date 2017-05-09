<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Counteragents;

/**
 * CounteragentsSearch represents the model behind the search form about `common\models\Counteragents`.
 */
class CounteragentsSearch extends Counteragents
{
    /**
     * Поле отбора для универсального поиска (во всем полям).
     * @var string
     */
    public $searchEntire;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'type_id', 'contract_id'], 'integer'],
            [
                [
                    'name', 'name_full', 'inn', 'kpp', 'ogrn', 'bank_an', 'bank_bik', 'bank_name', 'bank_ca', 'email',
                    'contact_person', 'address_j', 'address_p', 'address_m', 'phones', 'comment', 'searchEntire'
                ]
            , 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['searchEntire'] = 'Значение для поиска по всем полям';

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
        $query = Counteragents::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
                'route' => 'counteragents',
            ],
            'sort' => [
                'route' => 'counteragents',
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => [
                    'id',
                    'name',
                    'type_id',
                    'inn',
                    'kpp',
                    'ogrn',
                    'bank_an',
                    'bank_bik',
                    'bank_name',
                    'bank_ca',
                    'phones',
                    'email',
                    'address_j',
                    'address_p',
                    'address_m',
                    'tax_kind',
                    'is_accnt',
                    'typeName' => [
                        'asc' => ['types_counteragents.name' => SORT_ASC],
                        'desc' => ['types_counteragents.name' => SORT_DESC],
                    ],
                    'contractRep' => [
                        'asc' => ['documents.doc_date' => SORT_ASC, 'documents.doc_num' => SORT_ASC],
                        'desc' => ['documents.doc_date' => SORT_DESC, 'documents.doc_num' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
        $query->joinWith(['type', 'contract']);

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
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'counteragents.type_id' => $this->type_id,
            'contract_id' => $this->contract_id,
        ]);

        if ($this->searchEntire != null)
            $query->orFilterWhere(['like', 'name', $this->searchEntire])
                ->andFilterWhere(['like', 'name_full', $this->searchEntire])
                ->andFilterWhere(['like', 'inn', $this->searchEntire])
                ->andFilterWhere(['like', 'kpp', $this->searchEntire])
                ->andFilterWhere(['like', 'ogrn', $this->searchEntire])
                ->andFilterWhere(['like', 'bank_an', $this->searchEntire])
                ->andFilterWhere(['like', 'bank_bik', $this->searchEntire])
                ->andFilterWhere(['like', 'bank_name', $this->searchEntire])
                ->andFilterWhere(['like', 'bank_ca', $this->searchEntire])
                ->andFilterWhere(['like', 'email', $this->searchEntire])
                ->andFilterWhere(['like', 'contact_person', $this->searchEntire])
                ->orFilterWhere(['like', 'address_j', $this->searchEntire])
                ->orFilterWhere(['like', 'address_p', $this->searchEntire])
                ->orFilterWhere(['like', 'address_m', $this->searchEntire])
                ->orFilterWhere(['like', 'phones', $this->searchEntire])
                ->orFilterWhere(['like', 'comment', $this->searchEntire]);
        else
            $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'name_full', $this->name_full])
                ->andFilterWhere(['like', 'inn', $this->inn])
                ->andFilterWhere(['like', 'kpp', $this->kpp])
                ->andFilterWhere(['like', 'ogrn', $this->ogrn])
                ->andFilterWhere(['like', 'bank_an', $this->bank_an])
                ->andFilterWhere(['like', 'bank_bik', $this->bank_bik])
                ->andFilterWhere(['like', 'bank_name', $this->bank_name])
                ->andFilterWhere(['like', 'bank_ca', $this->bank_ca])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'contact_person', $this->contact_person])
                ->andFilterWhere(['like', 'address_j', $this->address_j])
                ->andFilterWhere(['like', 'address_p', $this->address_p])
                ->andFilterWhere(['like', 'address_m', $this->address_m])
                ->andFilterWhere(['like', 'phones', $this->phones])
                ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
