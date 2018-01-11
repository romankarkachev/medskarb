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
 *
 * @property string $defaultBuyerName
 * @property string $defaultBrokerRuName
 * @property string $defaultBrokerLnrName
 *
 * @property Counteragents $defaultBuyer
 * @property Counteragents $defaultBrokerRu
 * @property Counteragents $defaultBrokerLnr
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
            [['default_buyer_id', 'default_broker_ru', 'default_broker_lnr'], 'integer'],
            [['default_broker_lnr'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['default_broker_lnr' => 'id']],
            [['default_broker_ru'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['default_broker_ru' => 'id']],
            [['default_buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['default_buyer_id' => 'id']],
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
}
