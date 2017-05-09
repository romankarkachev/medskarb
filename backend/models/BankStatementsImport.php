<?php

namespace backend\models;

use yii\base\Model;
use yii\web\UploadedFile;
use common\models\Periods;

/**
 * @property Periods $period
 */
class BankStatementsImport extends Model
{
    /**
     * Период, к которому относятся платежи.
     */
    public $period_id;

    /**
     * @var UploadedFile
     */
    public $importFile;

    /**
     * Признак предварительного просмотра.
     * @var integer
     */
    public $is_preview;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['period_id', 'required'],
            [['period_id', 'is_preview'], 'integer'],
            [['importFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['xls', 'xlsx'], 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'period_id' => 'Период',
            'importFile' => 'Файл',
            'is_preview' => 'Предварительный просмотр',
        ];
    }

    /**
     * Проверяет, не встречаются ли слова из массива $excludes, которые исключают движение из расчета в $description.
     * @param $excludes array
     * @param $description string
     * @return bool
     */
    public static function CheckIfExcludes($excludes, $description)
    {
        foreach ($excludes as $exclude) {
            if (mb_stripos(mb_strtolower($description), mb_strtolower($exclude)) !== false) {
                return 0;
            }
        }

        return 1;
    }

    /**
     * Значение из параметров приводит к дате в формате YYYY-mm-dd.
     * @param $value string
     * @return string
     */
    public static function normalizeDate($value)
    {
        $date_array = explode('.', $value);
        if (count($date_array) == 3) {
            // проверим год, при необходимости добавим две тысячи
            // главное, чтобы не получилось как у Майя: кого это будет волновать через столько лет
            $year = intval($date_array[2]);
            if ($year < 100) $year += 2000;
            return $year . '-' . $date_array[1] . '-' . $date_array[0];
        }
        else return false;
    }

    /**
     * Извлекает из массива, переданного в параметрах, ИНН контрагента.
     * Он идет второй строкой.
     * @param $bank_kt
     * @return string|null
     */
    public static function DetermineInn($bank_kt)
    {
        $kt = explode("\n", $bank_kt);
        if (isset($kt[1])) return $kt[1];

        return null;
    }

    /**
     * Определяет идентификатор контрагента по ИНН, переданному в параметрах.
     * @param $inns array
     * @param $inn string
     * @return integer|null
     */
    public static function DetermineCounteragent($inns, $inn)
    {
        $key = array_search($inn, array_column($inns, 'inn'));
        if ($key !== false) return $inns[$key]['id'];
        return null;
    }

    /**
     * Значение из параметров приводит к числу с плавающей точкой.
     * @param $value
     * @return float
     */
    public static function normalizeAmount($value)
    {
        $result = trim($value);
        $result = str_replace(chr(194).chr(160), '', $result);
        $result = str_replace(' ', '', $result);
        $result = str_replace(',', '.', $result);
        return floatval($result);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function upload($filename)
    {
        $upl_dir = \Yii::getAlias('@uploads');
        if (!file_exists($upl_dir) && !is_dir($upl_dir)) mkdir($upl_dir, 0755);

        return $this->importFile->saveAs($filename);
    }

    /**
     * @return Periods
     */
    public function getPeriod()
    {
        return Periods::findOne($this->period_id);
    }
}
