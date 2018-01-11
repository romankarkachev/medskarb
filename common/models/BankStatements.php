<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "bank_statements".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $period_id
 * @property integer $type
 * @property integer $ca_id
 * @property integer $is_active
 * @property string $bank_date
 * @property string $bank_dt
 * @property string $bank_kt
 * @property string $bank_amount_dt
 * @property string $bank_amount_kt
 * @property string $bank_bik_name
 * @property string $bank_doc_num
 * @property string $bank_description
 * @property string $inn
 *
 * @property Counteragents $ca
 * @property User $createdBy
 * @property Periods $period
 * @property BankStatementsFiles[] $bankStatementsFiles
 */
class BankStatements extends \yii\db\ActiveRecord
{
    /**
     * Вычисляемое виртуальное поле.
     * @var integer количество файлов, приаттаченных к банковскому движению
     */
    public $filesCount;

    /**
     * Вычисляемое виртуальное поле.
     * @var integer количество файлов, приаттаченных к документу
     */
    public $filesDetails;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * Добавлено автоматически (при импорте).
     */
    const TYPE_AUTO = 0;

    /**
     * Добавлено вручную (например, как наличные).
     */
    const TYPE_MANUAL = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bank_statements';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period_id'], 'required'],
            [['period_id', 'ca_id', 'bank_date', 'bank_description'], 'required', 'on' => 'create_manual'],
            [['created_at', 'created_by', 'period_id', 'type', 'ca_id', 'is_active'], 'integer'],
            [['bank_date'], 'safe'],
            [['bank_dt', 'bank_kt', 'bank_bik_name', 'bank_description'], 'string'],
            [['bank_amount_dt', 'bank_amount_kt'], 'number'],
            [['bank_amount_dt'], 'number', 'min' => 0.01, 'on' => 'create_manual'],
            [['bank_doc_num'], 'string', 'max' => 20],
            [['inn'], 'string', 'max' => 12],
            [['imageFile'], 'file', 'skipOnEmpty' => false],
            [['ca_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counteragents::className(), 'targetAttribute' => ['ca_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => Periods::className(), 'targetAttribute' => ['period_id' => 'id']],
            // собственные правила валидации
            ['bank_date', 'validateBankDate'],
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
            'period_id' => 'Период',
            'type' => 'Тип (0 - авто, 1 - вручную добавлено)',
            'ca_id' => 'Контрагент',
            'is_active' => 'Принимать к расчету',
            'bank_date' => 'Дата',
            'bank_dt' => 'Дебет',
            'bank_kt' => 'Кредит',
            'bank_amount_dt' => 'Сумма Дт',
            'bank_amount_kt' => 'Сумма Кт',
            'bank_bik_name' => 'БИК и название банка',
            'bank_doc_num' => 'Номер платежного поручения',
            'bank_description' => 'Назначение платежа',
            'inn' => 'ИНН',
            'imageFile' => 'Фото чека',
            // для сортировки
            'caName' => 'Контрагент',
            'periodName' => 'Период',
        ];
    }

    /**
     * @inheritdoc
     */
    public function validateBankDate()
    {
        if ($this->type == BankStatements::TYPE_MANUAL)
            // добавляется вручную
            if ($this->period_id != null) {
                // период задан
                if (strtotime($this->bank_date) < $this->period->start || strtotime($this->bank_date) > $this->period->end)
                    $this->addError('bank_date', 'Дата не входит в выбранный период!');
            }
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
                ],
            ],
            'blameable' => [
                'class' => 'yii\behaviors\BlameableBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCa()
    {
        return $this->hasOne(Counteragents::className(), ['id' => 'ca_id']);
    }

    /**
     * Возвращает наименование контрагента.
     * @return string
     */
    public function getCaName()
    {
        return $this->ca == null ? '' : $this->ca->name;
    }

    /**
     * Возвращает полное наименование контрагента.
     * @return string
     */
    public function getCaNameFull()
    {
        return $this->ca == null ? '' : $this->ca->name_full;
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
    public function getPeriod()
    {
        return $this->hasOne(Periods::className(), ['id' => 'period_id']);
    }

    /**
     * Возвращает наименование периода.
     * @return string
     */
    public function getPeriodName()
    {
        return $this->period == null ? '' : $this->period->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBankStatementsFiles()
    {
        return $this->hasMany(BankStatementsFiles::className(), ['bs_id' => 'id']);
    }
}
