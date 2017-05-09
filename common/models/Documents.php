<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "documents".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $ca_id
 * @property integer $type_id
 * @property string $doc_num
 * @property string $doc_date
 * @property string $amount
 * @property string $comment
 *
 * @property float $amountUsed
 * @property TypesDocuments $type
 * @property Counteragents $ca
 * @property User $createdBy
 * @property User $updatedBy
 * @property DocumentsFiles[] $documentsFiles
 */
class Documents extends ActiveRecord
{
    /**
     * Идентификатор сделки, в которую необходимо включить создаваемый документ.
     * @var integer|null
     */
    public $includeInDeal_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ca_id', 'type_id', 'doc_num', 'doc_date'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'ca_id', 'type_id', 'includeInDeal_id'], 'integer'],
            [['doc_date'], 'safe'],
            [['amount'], 'number'],
            [['comment'], 'string'],
            [['doc_num'], 'string', 'max' => 30],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TypesDocuments::className(), 'targetAttribute' => ['type_id' => 'id']],
            [['ca_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['ca_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
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
            'ca_id' => 'Контрагент',
            'type_id' => 'Тип документа',
            'doc_num' => 'Номер',
            'doc_date' => 'Дата',
            'amount' => 'Сумма',
            'comment' => 'Описание',
            'includeInDeal_id' => 'Включить в сделку',
            // для сортировки
            'caName' => 'Контрагент',
            'typeName' => 'Тип',
            'documentRep' => 'Номер и дата',
        ];
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
            // Удаление связанных объектов перед удалением документа

            // удаляем привязки к сделкам
            DealsDocuments::deleteAll(['doc_id' => $this->id]);

            // удаляем возможные файлы
            // deleteAll не вызывает beforeDelete, поэтому делаем перебор
            $files = DocumentsFiles::find()->where(['doc_id' => $this->id])->all();
            foreach ($files as $file) $file->delete();

            return true;
        }

        return false;
    }

    /**
     * @param $formName string|null
     * @return array
     */
    public static function fetchDocumentsSettings($formName = null)
    {
        $result = [
            // для списка документов типа Договор
            'contracts' => [
                'type_id' => TypesDocuments::DOCUMENT_TYPE_ДОГОВОР,
                'final_bc' => 'Договоры',
            ],
            // для списка документов типа Приходная накладная
            'receipts' => [
                'type_id' => TypesDocuments::DOCUMENT_TYPE_ПРИХОДНАЯ_НАКЛАДНАЯ,
                'final_bc' => 'Приходные накладные',
            ],
            // для списка документов типа Расходная накладная
            'expenses' => [
                'type_id' => TypesDocuments::DOCUMENT_TYPE_РАСХОДНАЯ_НАКЛАДНАЯ,
                'final_bc' => 'Расходные накладные',
            ],
            // для списка документов типа Акты брокера РФ
            'broker-ru' => [
                'type_id' => TypesDocuments::DOCUMENT_TYPE_АКТ_ВЫПОЛНЕННЫХ_РАБОТ,
                'final_bc' => 'Акты выполненных работ брокера РФ',
            ],
            // для списка документов типа Акты брокера ЛНР
            'broker-lnr' => [
                'type_id' => TypesDocuments::DOCUMENT_TYPE_АКТ_ВЫПОЛНЕННЫХ_РАБОТ,
                'final_bc' => 'Акты выполненных работ брокера ЛНР',
            ],
        ];

        // дополняем условиями отбора, если это для отображения списка документов
        if ($formName != null) {
            $result['contracts']['searchConditions'] = [$formName => ['type_id' => TypesDocuments::DOCUMENT_TYPE_ДОГОВОР]];
            $result['receipts']['searchConditions'] = [$formName => ['type_id' => TypesDocuments::DOCUMENT_TYPE_ПРИХОДНАЯ_НАКЛАДНАЯ]];
            $result['expenses']['searchConditions'] = [$formName => ['type_id' => TypesDocuments::DOCUMENT_TYPE_РАСХОДНАЯ_НАКЛАДНАЯ]];
            $result['broker-ru']['searchConditions'] = [$formName => [
                'type_id' => TypesDocuments::DOCUMENT_TYPE_АКТ_ВЫПОЛНЕННЫХ_РАБОТ,
                'ca_type_id' => TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_РФ,
            ]];
            $result['broker-lnr']['searchConditions'] = [$formName => [
                'type_id' => TypesDocuments::DOCUMENT_TYPE_АКТ_ВЫПОЛНЕННЫХ_РАБОТ,
                'ca_type_id' => TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_ЛНР,
            ]];
        }

        return $result;
    }

    /**
     * По имеющемуся типу документов определяет url для отображения списка документов этого типа.
     * @return array|false
     */
    public function getDocumentsListUrlByType()
    {
        $ds = Documents::fetchDocumentsSettings();
        foreach ($ds as $url => $row) {
            if ($row['type_id'] == $this->type_id)
                return [
                    'url' => $url,
                    'bc' => $row['final_bc'],
                ];
        }

        return false;
    }

    /**
     * Возвращает список сделок, к которым прикреплен документ, в виде строки.
     * @return string
     */
    public function getDealDocumentsScalar()
    {
        return DealsDocuments::find()->select(
            new Expression('GROUP_CONCAT(CONCAT("№ ", `deals`.`id`, CASE WHEN `deal_date` IS NULL THEN CONCAT(" (создана ", FROM_UNIXTIME(`created_at`, "%d.%m.%Y"), ")") ELSE CONCAT(" от ", DATE_FORMAT(`deal_date`, "%d.%m.%Y")) END) SEPARATOR ", ")')
        )->leftJoin('deals', 'deals.id = deals_documents.deal_id')
            ->where(['doc_id' => $this->id])->groupBy('doc_id')->scalar();
    }

    /**
     * Возвращает список сделок, к которым прикреплен документ, в виде массива.
     * Используется для формирования списка ссылок.
     * @return array
     */
    public function getDealDocumentsArray()
    {
        return DealsDocuments::find()->select([
            'deals.id',
            'name' => 'CONCAT("№ ", `deals`.`id`, CASE WHEN `deal_date` IS NULL THEN CONCAT(" (создана ", FROM_UNIXTIME(`created_at`, "%d.%m.%Y"), ")") ELSE CONCAT(" от ", DATE_FORMAT(`deal_date`, "%d.%m.%Y")) END)',
        ]
        )->leftJoin('deals', 'deals.id = deals_documents.deal_id')
            ->where(['doc_id' => $this->id])->asArray()->all();
    }

    /**
     * Возвращает сумму использованных средств по договору.
     * Пример запроса:
     * SELECT SUM(`documents`.`amount`)
     * FROM `deals_documents`
     * LEFT JOIN `deals` ON `deals`.`id` = `deals_documents`.`deal_id`
     * LEFT JOIN `documents` ON `documents`.`id` = `deals_documents`.`doc_id`
     * WHERE (`documents`.`type_id`=3) AND (`deals`.`contract_id`=2)
     * @return float
     */
    public function getAmountUsed()
    {
        return DealsDocuments::find()
            ->select(['IFNULL(SUM(`documents`.`amount`), 0)'])
            ->leftJoin('deals', '`deals`.`id` = `deals_documents`.`deal_id`')
            ->leftJoin('documents', '`documents`.`id` = `deals_documents`.`doc_id`')
            ->where(['`documents`.`type_id`' => TypesDocuments::DOCUMENT_TYPE_РАСХОДНАЯ_НАКЛАДНАЯ])
            ->andWhere(['`deals`.`contract_id`' => $this->id])
            ->scalar();
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
    public function getCa()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'ca_id']);
    }

    /**
     * Возвращает наименование контрагента
     * @return string
     */
    public function getCaName()
    {
        return $this->ca == null ? '' : $this->ca->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(TypesDocuments::className(), ['id' => 'type_id']);
    }

    /**
     * Возвращает наименование типа документа.
     * @return string
     */
    public function getTypeName()
    {
        return $this->type == null ? '' : $this->type->name;
    }

    /**
     * Возвращает представление документа.
     * @return string
     */
    public function getDocumentRep()
    {
        $number = '';
        $date = '-';
        if ($this->doc_num != null && trim($this->doc_num) != '')
            $number = '№ ' . trim($this->doc_num);

        if ($this->doc_date != null && $this->doc_date != '')
            $date = ($number == '' ? '' : ' ') . 'от ' . Yii::$app->formatter->asDate($this->doc_date, 'php: d.m.Y г.');

        return $number . $date;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentsFiles()
    {
        return $this->hasMany(DocumentsFiles::className(), ['doc_id' => 'id']);
    }
}
