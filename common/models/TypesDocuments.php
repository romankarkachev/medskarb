<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "types_documents".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Counteragents[] $counteragents
 * @property Documents[] $documents
 */
class TypesDocuments extends \yii\db\ActiveRecord
{
    const DOCUMENT_TYPE_ДОГОВОР = 1;
    const DOCUMENT_TYPE_ПРИХОДНАЯ_НАКЛАДНАЯ = 2;
    const DOCUMENT_TYPE_РАСХОДНАЯ_НАКЛАДНАЯ = 3;
    const DOCUMENT_TYPE_АКТ_ВЫПОЛНЕННЫХ_РАБОТ = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'types_documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
        ];
    }

    /**
     * Делает выборку типов документов и возвращает в виде массива.
     * Применяется для вывода в виджетах Select2.
     * @return array
     */
    public static function arrayMapForSelect2()
    {
        return ArrayHelper::map(TypesDocuments::find()->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounteragents()
    {
        return $this->hasMany(Counteragents::className(), ['contract_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Documents::className(), ['type_id' => 'id']);
    }
}
