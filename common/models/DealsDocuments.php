<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "deals_documents".
 *
 * @property integer $id
 * @property integer $deal_id
 * @property integer $doc_id
 *
 * @property Documents $document
 * @property Deals $deal
 */
class DealsDocuments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deals_documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deal_id', 'doc_id'], 'required'],
            [['deal_id', 'doc_id'], 'integer'],
            [['doc_id'], 'exist', 'skipOnError' => true, 'targetClass' => Documents::className(), 'targetAttribute' => ['doc_id' => 'id']],
            [['deal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Deals::className(), 'targetAttribute' => ['deal_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'deal_id' => 'Сделка',
            'doc_id' => 'Документ',
            // для сортировки
            'documentCaName' => 'Контрагент',
            'documentAmount' => 'Сумма',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Documents::className(), ['id' => 'doc_id']);
    }

    /**
     * Возвращает сумму документа.
     * @return float
     */
    public function getDocumentAmount()
    {
        return $this->document == null ? null : $this->document->amount;
    }

    /**
     * Лефтджойнит таблицу типов контрагентов.
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentCaType()
    {
        return $this->hasOne(TypesCounteragents::className(), ['id' => 'type_id'])
            ->via('documentCa');
    }

    /**
     * Лефтджойнит таблицу типов документов.
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentType()
    {
        return $this->hasOne(TypesDocuments::className(), ['id' => 'type_id'])
            ->via('document');
    }

    /**
     * Лефтджойнит таблицу контрагентов.
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentCa()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'ca_id'])
            ->via('document');
    }

    /**
     * Возвращает наименование контрагента.
     * @return string
     */
    public function getDocumentCaName()
    {
        return $this->documentCa == null ? '' : $this->documentCa->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeal()
    {
        return $this->hasOne(Deals::className(), ['id' => 'deal_id']);
    }
}
