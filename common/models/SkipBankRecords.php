<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "skip_bank_records".
 *
 * @property integer $id
 * @property string $substring
 */
class SkipBankRecords extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'skip_bank_records';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['substring'], 'required'],
            [['substring'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'substring' => 'Искомая подстрока',
        ];
    }
}
