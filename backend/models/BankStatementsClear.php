<?php

namespace backend\models;

use yii\base\Model;
use common\models\Periods;

/**
 * @property Periods $period
 */
class BankStatementsClear extends Model
{
    /**
     * Период, к которому относятся платежи.
     */
    public $period_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['period_id', 'required'],
            [['period_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'period_id' => 'Период',
        ];
    }

    /**
     * @return Periods
     */
    public function getPeriod()
    {
        return Periods::findOne($this->period_id);
    }
}
