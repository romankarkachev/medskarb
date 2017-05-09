<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DealsDocuments;

/**
 * DealsDocumentsSearch represents the model behind the search form about `common\models\DealsDocuments`.
 */
class DealsDocumentsSearch extends DealsDocuments
{
    /**
     * @var integer идентификатор типа документа
     */
    public $document_type_id;

    /**
     * @var integer идентификатор типа контрагента
     */
    public $ca_type_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'deal_id', 'doc_id'], 'integer'],
            // дополнительно прикрепляются через join:
            [['document_type_id', 'ca_type_id'], 'integer'],
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
        $query = DealsDocuments::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
                'route' => 'deals/deal-documents',
            ],
            'sort' => [
                'route' => 'deals/deal-documents',
                'defaultOrder' => ['doc_date' => SORT_DESC],
                'attributes' => [
                    'id',
                    'doc_date',
                    'documentCaName' => [
                        'asc' => ['counteragents.name' => SORT_ASC],
                        'desc' => ['counteragents.name' => SORT_DESC],
                    ],
                    'documentAmount' => [
                        'asc' => ['documents.amount' => SORT_ASC],
                        'desc' => ['documents.amount' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
        $query->joinWith(['document', 'documentType', 'documentCa']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'deal_id' => $this->deal_id,
            'doc_id' => $this->doc_id,
        ]);

        if ($this->document_type_id != null)
            $query->andFilterWhere([
                'documents.type_id' => $this->document_type_id,
            ]);

        if ($this->ca_type_id != null)
            $query->andFilterWhere([
                'counteragents.type_id' => $this->ca_type_id,
            ]);

        return $dataProvider;
    }
}
