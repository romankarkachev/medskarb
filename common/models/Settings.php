<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property integer $default_buyer_id
 * @property integer $default_broker_ru
 * @property integer $default_broker_lnr
 * @property integer $tax_inspection_id
 * @property integer $tax_usn_rate
 * @property string $tax_pf_limit
 * @property integer $tax_pf_rate
 *
 * @property string $defaultBuyerName
 * @property string $defaultBrokerRuName
 * @property string $defaultBrokerLnrName
 * @property string $taxInspectionName
 *
 * @property Counteragents $defaultBuyer
 * @property Counteragents $defaultBrokerRu
 * @property Counteragents $defaultBrokerLnr
 * @property Counteragents $taxInspection
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['default_buyer_id', 'default_broker_ru', 'default_broker_lnr', 'tax_inspection_id', 'tax_usn_rate', 'tax_pf_rate'], 'integer'],
            [['tax_usn_rate', 'tax_pf_limit', 'tax_pf_rate'], 'required'],
            [['tax_pf_limit'], 'number'],
            [['default_broker_lnr'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['default_broker_lnr' => 'id']],
            [['default_broker_ru'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['default_broker_ru' => 'id']],
            [['default_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['default_buyer_id' => 'id']],
            [['tax_inspection_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['tax_inspection_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'default_buyer_id' => 'Основной покупатель',
            'default_broker_ru' => 'Основной брокер РФ',
            'default_broker_lnr' => 'Основной брокер ЛНР',
            'tax_inspection_id' => 'Налоговая инспекция',
            'tax_usn_rate' => 'Ставка УСН',
            'tax_pf_limit' => 'Сумма превышения',
            'tax_pf_rate' => 'Ставка 300 000',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultBuyer()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'default_buyer_id']);
    }

    /**
     * Возвращает наименование основного покупателя.
     * @return string
     */
    public function getDefaultBuyerName()
    {
        return $this->defaultBuyer != null ? $this->defaultBuyer->name : '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultBrokerRu()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'default_broker_ru']);
    }

    /**
     * Возвращает наименование основного брокера РФ.
     * @return string
     */
    public function getDefaultBrokerRuName()
    {
        return $this->defaultBrokerRu != null ? $this->defaultBrokerRu->name : '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultBrokerLnr()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'default_broker_lnr']);
    }

    /**
     * Возвращает наименование основного брокера ЛНР.
     * @return string
     */
    public function getDefaultBrokerLnrName()
    {
        return $this->defaultBrokerLnr != null ? $this->defaultBrokerLnr->name : '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxInspection()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'tax_inspection_id']);
    }

    /**
     * Возвращает наименование налоговой инспекции.
     * @return string
     */
    public function getTaxInspectionName()
    {
        return $this->taxInspection != null ? $this->taxInspection->name : '';
    }
}
