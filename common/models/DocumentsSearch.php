<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Documents;

/**
 * DocumentsSearch represents the model behind the search form about `common\models\Documents`.
 */
class DocumentsSearch extends Documents
{
    /**
     * Поле отбора, определяющее начало периода даты документа.
     * @var string
     */
    public $searchDateStart;

    /**
     * Поле отбора, определяющее окончания периода даты документа.
     * @var string
     */
    public $searchDateEnd;

    /**
     * Дополнительный реквизит отбора.
     * @var array массив идентификаторов документов, которые исключаются из выборки
     */
    public $not_in_id;

    /**
     * Дополнительный реквизит отбора.
     * @var integer тип контрагента
     */
    public $ca_type_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'ca_id', 'type_id', 'ca_type_id'], 'integer'],
            [['doc_num', 'doc_date', 'comment'], 'safe'],
            [['amount'], 'number'],
            // для отбора
            [['searchDateStart', 'searchDateEnd', 'not_in_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['searchDateStart'] = 'Дата документа с';
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
     * @param $params array
     * @param $route string
     * @return ActiveDataProvider
     */
    public function search($params, $route = 'documents')
    {
        $query = Documents::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
                'route' => $route,
            ],
            'sort' => [
                'route' => $route,
                'defaultOrder' => ['doc_date' => SORT_DESC],
                'attributes' => [
                    'id',
                    'ca_id',
                    'type_id',
                    'doc_num',
                    'doc_date',
                    'amount',
                    'comment',
                    'caName' => [
                        'asc' => ['counteragents.name' => SORT_ASC],
                        'desc' => ['counteragents.name' => SORT_DESC],
                    ],
                    'typeName' => [
                        'asc' => ['types_documents.name' => SORT_ASC],
                        'desc' => ['types_documents.name' => SORT_DESC],
                    ],
                    'documentRep' => [
                        'asc' => ['documents.doc_date' => SORT_ASC, 'documents.doc_num' => SORT_ASC],
                        'desc' => ['documents.doc_date' => SORT_DESC, 'documents.doc_num' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
        $query->joinWith(['ca', 'type']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'documents.id' => $this->id,
            'documents.created_at' => $this->created_at,
            'documents.created_by' => $this->created_by,
            'documents.updated_at' => $this->updated_at,
            'documents.updated_by' => $this->updated_by,
            'ca_id' => $this->ca_id,
            'documents.type_id' => $this->type_id,
            'amount' => $this->amount,
            'counteragents.type_id' => $this->ca_type_id,
        ]);

        if ($this->searchDateStart !== null or $this->searchDateEnd !== null)
            if ($this->searchDateStart !== '' && $this->searchDateEnd !== '') {
                // если указаны обе даты
                $query->andFilterWhere(['between', '`documents`.`doc_date`', $this->searchDateStart.' 00:00:00', $this->searchDateEnd.' 23:59:59']);
            }
            else if ($this->searchDateStart !== '' && $this->searchDateEnd === '') {
                // если указано только начало периода
                $query->andFilterWhere(['>=','`documents`.`doc_date`', $this->searchDateStart.' 00:00:00']);
            }
            else if ($this->searchDateStart === '' && $this->searchDateEnd !== '') {
                // если указан только конец периода
                $query->andFilterWhere(['<=', '`documents`.`doc_date`', $this->searchDateEnd.' 23:59:59']);
            };

        $query->andFilterWhere(['like', 'doc_num', $this->doc_num])
            ->andFilterWhere(['like', 'documents.comment', $this->comment]);

        // исключаем идентификаторы, переданные снаржи
        // не включаются в выборку те документы, которые уже используются в других сделках
        if ($this->not_in_id != null)
            $query->andFilterWhere(['not in', 'id', $this->not_in_id]);

        return $dataProvider;
    }
}
