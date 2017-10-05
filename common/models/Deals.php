<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "deals".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property string $deal_date
 * @property integer $customer_id
 * @property integer $contract_id
 * @property integer $broker_ru_id
 * @property integer $broker_lnr_id
 * @property integer $is_closed
 *
 * @property Counteragents $brokerRu
 * @property Counteragents $brokerLnr
 * @property Documents $contract
 * @property User $createdBy
 * @property Counteragents $customer
 * @property User $updatedBy
 * @property DealsDocuments[] $dealsDocuments
 * @property DealsFiles[] $dealsFiles
 */
class Deals extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deals';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'customer_id', 'contract_id', 'broker_ru_id', 'broker_lnr_id', 'is_closed'], 'integer'],
            [['deal_date'], 'safe'],
            [['broker_ru_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['broker_ru_id' => 'id']],
            [['broker_lnr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['broker_lnr_id' => 'id']],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Documents::className(), 'targetAttribute' => ['contract_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            // собственные правила валидации
            ['contract_id', 'validateContract', 'skipOnEmpty' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата и время создания',
            'created_by' => 'Автор создания',
            'updated_at' => 'Дата и время изменения',
            'updated_by' => 'Автор изменений',
            'deal_date' => 'Дата',
            'customer_id' => 'Покупатель',
            'contract_id' => 'Договор',
            'broker_ru_id' => 'Брокер РФ',
            'broker_lnr_id' => 'Брокер ЛНР',
            'is_closed' => 'Сделка закрыта',
            // для сортировки
            'customerName' => 'Покупатель',
            'contractRep' => 'Договор',
            'brokerRuName' => 'Брокер РФ',
            'brokerLnrName' => 'Брокер ЛНР',
        ];
    }

    /**
     * Валидация поля "Договор".
     */
    public function validateContract()
    {
        if ($this->customer_id != null && $this->contract_id == null)
            $this->addError('contract_id', 'Поле "Договор" обязательно для заполнения.');
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'blameable' => [
                'class' => 'yii\behaviors\BlameableBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            // Удаление связанных объектов перед удалением сделки

            // удаление привязанных документов
            DealsDocuments::deleteAll(['deal_id' => $this->id]);

            // удаление возможных файлы
            // deleteAll не вызывает beforeDelete, поэтому делаем перебор
            $files = DealsFiles::find()->where(['deal_id' => $this->id])->all();
            foreach ($files as $file) $file->delete();

            return true;
        }

        return false;
    }

    /**
     * Собирает объекты типа ActiveDataProvider в одном массиве и возвращает его.
     * Каждый отдельный dataProvider - это набор документов определенного типа, приаттаченный к сделке.
     * @return array
     */
    public function collectDocumentsDataProviders()
    {
        // документы к сделке
        $documentsSearchModel = new DealsDocumentsSearch();

        // приходные накладные
        $dpDocumentsRecepit = $documentsSearchModel->search([$documentsSearchModel->formName() => [
            'deal_id' => $this->id,
            'document_type_id' => TypesDocuments::DOCUMENT_TYPE_ПРИХОДНАЯ_НАКЛАДНАЯ,
        ]]);

        // расходные накладные
        $dpDocumentsExpense = $documentsSearchModel->search([$documentsSearchModel->formName() => [
            'deal_id' => $this->id,
            'document_type_id' => TypesDocuments::DOCUMENT_TYPE_РАСХОДНАЯ_НАКЛАДНАЯ,
        ]]);

        // акты брокера РФ
        $dpDocumentsBrokerRu = $documentsSearchModel->search([$documentsSearchModel->formName() => [
            'deal_id' => $this->id,
            'document_type_id' => TypesDocuments::DOCUMENT_TYPE_АКТ_ВЫПОЛНЕННЫХ_РАБОТ,
            'ca_type_id' => TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_РФ,
        ]]);

        // акты брокера ЛНР
        $dpDocumentsBrokerLnr = $documentsSearchModel->search([$documentsSearchModel->formName() => [
            'deal_id' => $this->id,
            'document_type_id' => TypesDocuments::DOCUMENT_TYPE_АКТ_ВЫПОЛНЕННЫХ_РАБОТ,
            'ca_type_id' => TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_ЛНР,
        ]]);

        return [
            'dpDocumentsRecepit' => $dpDocumentsRecepit,
            'dpDocumentsExpense' => $dpDocumentsExpense,
            'dpDocumentsBrokerRu' => $dpDocumentsBrokerRu,
            'dpDocumentsBrokerLnr' => $dpDocumentsBrokerLnr,
        ];
    }

    /**
     * Функция делает выборку свободных (не привязанных ни к одной сделке) документов.
     * @return \yii\data\ActiveDataProvider
     */
    public static function collectUnattachedDocuments()
    {
        $query = Documents::find()
            ->leftJoin('deals_documents', 'deals_documents.doc_id = documents.id')
            ->where(['`deals_documents`.`id`' => null])
            ->andWhere('`types_documents`.`id` <> ' . TypesDocuments::DOCUMENT_TYPE_ДОГОВОР);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'route' => 'deals/update',
                'defaultOrder' => ['doc_date' => SORT_DESC, 'typeName' => SORT_ASC],
                'attributes' => [
                    'id',
                    'doc_date',
                    'amount',
                    'typeName' => [
                        'asc' => ['types_documents.name' => SORT_ASC],
                        'desc' => ['types_documents.name' => SORT_DESC],
                    ],
                    'caName' => [
                        'asc' => ['counteragents.name' => SORT_ASC],
                        'desc' => ['counteragents.name' => SORT_DESC],
                    ],
                ],
            ]
        ]);
        $query->joinWith(['type', 'ca']);

        return $dataProvider;
    }

    /**
     * Делает выборку файлов, приаттаченных ко всем связанным объектам - документам и контрагенту.
     * @return ArrayDataProvider
     */
    public function collectFilesFromAllRelatedObjects($attachedDocsIds)
    {
        $array = [];

        // файлы, привязанные к сделке
        $subarray = DealsFiles::find()
            ->select(['id', 'uploaded_at', 'ofn', 'size'])
            ->where(['deal_id' => $this->id])
            ->asArray()->all();
        foreach ($subarray as $file) {
            /* @var $file \common\models\DealsFiles */
            $new_row = $file;
            $new_row['sort'] = 0;
            $new_row['type'] = 0;
            $new_row['rep'] = 'Эта сделка';
            $new_row['url'] = Url::to(['/deals/download', 'id' => $file['id']]);
            $array[] = $new_row;
        }
        unset($subarray);

        // файлы, привязанные к контрагенту
        $subarray = CounteragentsFiles::find()
            ->select(['id', 'uploaded_at', 'ofn', 'size'])
            ->where(['ca_id' => $this->customer_id])
            ->asArray()->all();
        foreach ($subarray as $file) {
            /* @var $file \common\models\CounteragentsFiles */
            $new_row = $file;
            $new_row['sort'] = 1;
            $new_row['type'] = 1;
            $new_row['rep'] = 'Контрагент';
            $new_row['url'] = Url::to(['/counteragents/download', 'id' => $file['id']]);
            $array[] = $new_row;
        }
        unset($subarray);

        // файлы, привязанные к договору с контрагентом
        $subarray = DocumentsFiles::find()
            ->select(['id', 'doc_id', 'uploaded_at', 'ofn', 'size'])
            ->where(['doc_id' => $this->customer->contract_id])
            ->all();
        foreach ($subarray as $file) {
            /* @var $file \common\models\DocumentsFiles */
            $array[] = [
                'id' => $file->id,
                'uploaded_at' => $file->uploaded_at,
                'ofn' => $file->ofn,
                'sort' => 2,
                'type' => 2,
                'rep' => 'Договор контрагента № ' . $file->doc->id . ' от ' . Yii::$app->formatter->asDate($file->doc->doc_date),
                'url' => Url::to(['/documents/download', 'id' => $file['id']]),
                'size' => $file->size,
            ];
        }
        unset($subarray);

        // файлы, привязанные к документам
        $subarray = DocumentsFiles::find()
            ->select(['id', 'doc_id', 'uploaded_at', 'ofn', 'size'])
            //->leftJoin('types_documents', '')
            ->where(['in', 'doc_id', $attachedDocsIds])
            ->all();
        foreach ($subarray as $file) {
            /* @var $file \common\models\DocumentsFiles */
            $array[] = [
                'id' => $file->id,
                'uploaded_at' => $file->uploaded_at,
                'ofn' => $file->ofn,
                'sort' => 3,
                'type' => $file->doc->type->id,
                'rep' => $file->doc->type->name .
                    ' № ' . $file->doc->id .
                    ' от ' . Yii::$app->formatter->asDate($file->doc->doc_date) .
                    ' (' . $file->doc->ca->name . ')',
                'url' => Url::to(['/documents/download', 'id' => $file['id']]),
                'size' => $file->size,
            ];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $array,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['sort' => SORT_ASC, 'type' => SORT_ASC, 'uploaded_at' => SORT_DESC],
                'attributes' => [
                    'sort',
                    'type',
                    'uploaded_at',
                    'ofn',
                    'size',
                ],
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Делает выборку незакрытых сделок и возвращает в виде массива.
     * Применяется для вывода в виджетах Select2.
     * @return array
     */
    public static function arrayMapOfAvailableDealsForSelect2()
    {
        return ArrayHelper::map(Deals::find()->select([
            'id',
            'name' => 'CONCAT("№ ", `id`, CASE WHEN `deal_date` IS NULL THEN CONCAT(" (создана ", FROM_UNIXTIME(`created_at`, "%d.%m.%Y"), ")") ELSE CONCAT(" от ", DATE_FORMAT(`deal_date`, "%d.%m.%Y")) END)',
        ])->where(['is_closed' => false])->orderBy('deal_date DESC, created_at DESC')->asArray()->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Возвращает имя автора-создателя в виде ivan (Иван).
     * @return string
     */
    public function getCreatedByName()
    {
        return $this->created_by == null ? '' : ($this->createdBy->profile == null ? $this->createdBy->username :
            $this->createdBy->username . ' (' . $this->createdBy->profile->name . ')');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Возвращает имя пользователя, который вносил изменения в запись последним в виде ivan (Иван).
     * @return string
     */
    public function getUpdatedByName()
    {
        return $this->updated_by == null ? '' : ($this->updatedBy->profile == null ? $this->updatedBy->username :
            $this->updatedBy->username . ' (' . $this->updatedBy->profile->name . ')');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'customer_id'])->from(['customer' => Counteragents::tableName()]);
    }

    /**
     * Возвращает наименование покупателя.
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customer == null ? '' : $this->customer->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Documents::className(), ['id' => 'contract_id']);
    }

    /**
     * Возвращает номер и дату договора.
     * @return string
     */
    public function getContractRep()
    {
        $number = '';
        $date = '-';
        if ($this->contract != null) {
            if ($this->contract->doc_num != null && trim($this->contract->doc_num) != '')
                $number = '№ ' . trim($this->contract->doc_num);

            if ($this->contract->doc_date != null && $this->contract->doc_date != '')
                $date = ($number == '' ? '' : ' ') . 'от ' . Yii::$app->formatter->asDate($this->contract->doc_date, 'php: d.m.Y г.');
        }

        return $number . $date;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrokerRu()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'broker_ru_id'])->from(['broker_ru' => Counteragents::tableName()]);
    }

    /**
     * Возвращает наименование брокера.
     * @return string
     */
    public function getBrokerRuName()
    {
        return $this->brokerRu == null ? '' : $this->brokerRu->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrokerLnr()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'broker_lnr_id'])->from(['broker_lnr' => Counteragents::tableName()]);
    }

    /**
     * Возвращает наименование брокера.
     * @return string
     */
    public function getBrokerLnrName()
    {
        return $this->brokerLnr == null ? '' : $this->brokerLnr->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealsDocuments()
    {
        return $this->hasMany(DealsDocuments::className(), ['deal_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealsFiles()
    {
        return $this->hasMany(DealsFiles::className(), ['deal_id' => 'id']);
    }
}
