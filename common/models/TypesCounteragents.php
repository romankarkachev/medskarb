<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "types_counteragents".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Counteragents[] $counteragents
 */
class TypesCounteragents extends \yii\db\ActiveRecord
{
    const COUNTERAGENT_TYPE_ПОСТАВЩИК = 1;
    const COUNTERAGENT_TYPE_ПОКУПАТЕЛЬ = 2;
    const COUNTERAGENT_TYPE_БРОКЕР_РФ = 3;
    const COUNTERAGENT_TYPE_БРОКЕР_ЛНР = 4;
    const COUNTERAGENT_TYPE_ПЕРЕВОЗЧИК = 5;
    const COUNTERAGENT_TYPE_ПРОЧЕЕ = 6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'types_counteragents';
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
     * Делает выборку типов контрагентов и возвращает в виде массива.
     * Применяется для вывода в виджетах Select2.
     * @return array
     */
    public static function arrayMapForSelect2()
    {
        return ArrayHelper::map(TypesCounteragents::find()->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounteragents()
    {
        return $this->hasMany(Counteragents::className(), ['type_id' => 'id']);
    }
}
