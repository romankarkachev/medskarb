<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/**
 * This is the model class for table "counteragents".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property string $name
 * @property string $name_full
 * @property integer $type_id
 * @property string $inn
 * @property string $kpp
 * @property string $ogrn
 * @property string $bank_an
 * @property string $bank_bik
 * @property string $bank_name
 * @property string $bank_ca
 * @property string $email
 * @property string $contact_person
 * @property string $address_j
 * @property string $address_p
 * @property string $address_m
 * @property string $phones
 * @property integer $contract_id
 * @property string $comment
 *
 * @property string $contractRep
 * @property string $typeName
 *
 * @property Documents $contract
 * @property User $createdBy
 * @property TypesCounteragents $type
 * @property User $updatedBy
 * @property CounteragentsFiles[] $counteragentsFiles
 * @property Documents[] $documents
 */
class Counteragents extends ActiveRecord
{
    /**
     * Типы субъектов предпринимательской деятельности для целей поиска по Единому реестру через механизм API.
     */
    const API_CA_TYPE_ЮРЛИЦО = 1;
    const API_CA_TYPE_ФИЗЛИЦО = 2;

    /**
     * Типы полей для поиска по Единому реестру через механизм API.
     */
    const API_FIELD_ИНН = 1;
    const API_FIELD_ОГРН = 2;
    const API_FIELD_НАИМЕНОВАНИЕ = 3;

    /**
     * Признак пожелания создать новый договор к новому контрагенту.
     * @var integer
     */
    public $isCreateNewContract;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'counteragents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'name_full', 'type_id'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'isCreateNewContract', 'type_id', 'contract_id'], 'integer'],
            [['address_j', 'address_p', 'address_m', 'comment'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['name_full'], 'string', 'max' => 200],
            [['inn'], 'string', 'min' => 10, 'max' => 12],
            [['kpp'], 'string', 'length' => 9],
            [['ogrn'], 'string', 'max' => 15],
            [['bank_an', 'bank_ca'], 'string', 'length' => 20],
            [['bank_bik'], 'string', 'length' => 9],
            [['bank_name', 'email'], 'string', 'max' => 255],
            [['contact_person', 'phones'], 'string', 'max' => 50],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Documents::className(), 'targetAttribute' => ['contract_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TypesCounteragents::className(), 'targetAttribute' => ['type_id' => 'id']],
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
            'name' => 'Наименование',
            'name_full' => 'Полное наименование',
            'type_id' => 'Тип контрагента',
            'inn' => 'ИНН',
            'kpp' => 'КПП',
            'ogrn' => 'ОГРН(ИП)',
            'bank_an' => 'Номер р/с',
            'bank_bik' => 'БИК банка',
            'bank_name' => 'Наименование банка',
            'bank_ca' => 'Корр. счет',
            'email' => 'E-mail',
            'contact_person' => 'Контактное лицо',
            'address_j' => 'Адрес юридический',
            'address_p' => 'Адрес фактический',
            'address_m' => 'Адрес почтовый',
            'phones' => 'Телефоны',
            'contract_id' => 'Основной договор',
            'comment' => 'Примечания',
            'isCreateNewContract' => 'Создать договор',
            // для сортировки
            'typeName' => 'Тип', // тип контрагента
            'contractRep' => 'Договор', // основной договор
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
            // Удаление связанных объектов перед удалением контрагента

            // удаляем возможные файлы
            // deleteAll не вызывает beforeDelete, поэтому делаем перебор
            $files = CounteragentsFiles::find()->where(['ca_id' => $this->id])->all();
            foreach ($files as $file) $file->delete();

            return true;
        }

        return false;
    }

    /**
     * Делает запрос данных контрагента по API.
     * @param $type_id integer тип контрагента (1 - юрлицо, 2 - физлицо)
     * @param $field_id integer поле для поиска данных (1 - инн, 2 - огрн(ип), 3 - наименование)
     * @param $value string значение для поиска
     * @return array массив с данными контрагента
     */
    public static function apiFetchCounteragentsInfo($type_id, $field_id, $value)
    {
        $client = new Client();
        $query = $client->createRequest()->setMethod('get');

        // тип контрагента
        if ($type_id == self::API_CA_TYPE_ЮРЛИЦО) {
            // юридическое лицо
            $query->setUrl('https://ru.rus.company/интеграция/компании/');
            switch ($field_id) {
                case self::API_FIELD_ИНН:
                    $query->setData(['инн' => $value]);
                    break;
                case self::API_FIELD_ОГРН:
                    $query->setData(['огрн' => $value]);
                    break;
                case self::API_FIELD_НАИМЕНОВАНИЕ:
                    $query->setData(['наименование' => $value]);
                    break;
            }
        }
        else {
            // физическое лицо
            $query->setUrl('https://ru.rus.company/интеграция/ип/');
            switch ($field_id) {
                case self::API_FIELD_ИНН:
                    $query->setData(['инн' => $value]);
                    break;
                case self::API_FIELD_ОГРН:
                    $query->setData(['огрнип' => $value]);
                    break;
            }
        }

        $response = $query->send();

        if ($response->isOk) {
            $result = $response->data;
            //var_dump($result);
            if (count($result) > 0) {
                if (count($result) == 1) {
                    $details = $response->data[0];
                    if ($type_id == self::API_CA_TYPE_ЮРЛИЦО) {
                        // сразу второй запрос, потому что контрагент-юрлицо идентифицирован однозначно
                        $query->setUrl('https://ru.rus.company/интеграция/компании/' . $details['id'] . '/');
                        $response = $query->send();
                        if ($response->isOk) return [$response->data];
                    }
                }

                return $response->data;
            }
        }

        return [];
    }

    /**
     * Делает запрос данных контрагента, определенного ранее как неоднозначный, по API.
     * @param $type_id integer тип контрагента (1 - юрлицо, 2 - физлицо)
     * @param $id integer идентификатор контрагента в базе данных веб-сервиса
     * @return array
     */
    public static function apiFetchAmbiguousCounteragentsInfo($type_id, $id, $pid)
    {
        $client = new Client();
        $query = $client->createRequest()->setMethod('get');

        if ($type_id == self::API_CA_TYPE_ЮРЛИЦО) {
            $query->setUrl('https://ru.rus.company/интеграция/компании/' . $id . '/');
        }
        else {
            $query->setUrl('https://ru.rus.company/интеграция/ип/');
            $query->setData(['человек' => $id]);
        }

        $response = $query->send();
        if ($response->isOk) {
            if (count($response->data) > 0)
                if (isset($response->data['id'])) return [$response->data];

            foreach ($response->data as $row)
                if (isset($row['id']))
                    if ($row['id'] == $pid)
                        return [$row];
        }

        return [];
    }

    /**
     * Извлекает наименование, заключенное в кавычки и возвращает результат.
     * @param $name string
     * @return string
     */
    public static function api_extractNameInQuotes($name)
    {
        if (preg_match('~"([^"]*)"~u' , $name , $m)) return $m[1];
        return $name;
    }

    /**
     * Делает заглавными первые буквы во всех словах значения, переданного в параметрах.
     * @param $value string
     * @return string
     */
    public static function api_uppercaseFirstLetters($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'utf-8');
    }

    /**
     * Формирует адрес из параметров массива.
     * @param $address array
     * @return string
     */
    public static function api_composeFullAddress($address)
    {
        $postal_rep = '';
        if (isset($address['postalIndex'])) $postal_rep = $address['postalIndex'];

        $region_rep = '';
        if (isset($address['region']))
            if (intval($address['region']['type']['code']) == 103)
                $region_rep = $address['region']['type']['shortName'] . '. ' . $address['region']['name'];
            else
                $region_rep = $address['region']['fullName'];

        $area_rep = '';
        if (isset($address['area'])) $area_rep = $address['area']['fullName'];

        $place_rep = '';
        if (isset($address['place']))
            $place_rep = $address['place']['type']['shortName'] . '. ' . $address['place']['name'];
//            if (intval($address['place']['type']['code']) == 605)
//                $place_rep = $address['place']['type']['shortName'] . '. ' . $address['place']['name'];
//            else
//                $place_rep = $address['place']['fullName'];

        $city_rep = '';
        if (isset($address['city']))
            $city_rep = $address['city']['type']['shortName'] . '. ' . $address['city']['name'];
//            if (intval($address['city']['type']['code']) == 401)
//                $city_rep = $address['city']['type']['shortName'] . '. ' . $address['city']['name'];
//            else
//                $city_rep = $address['city']['fullName'];

        $street_rep = '';
        if (isset($address['street'])) $street_rep = $address['street']['typeShortName'] . '. ' . $address['street']['name'];

        $house_rep = '';
        if (isset($address['house'])) $house_rep = $address['house'];

        $building_rep = '';
        if (isset($address['building'])) $building_rep = $address['building'];

        $flat_rep = '';
        if (isset($address['flat'])) $flat_rep = $address['flat'];

        $result = $postal_rep . ' ' . $region_rep;
        $result = trim($result, ', ');

        $result .= ' ' . $area_rep;
        $result = trim($result, ', ');

        $result .= ' ' . $place_rep;
        $result = trim($result, ', ');

        $result .= ', ' . $city_rep;
        $result = trim($result, ', ');

        $result .= ($city_rep != '' ? ', ' : ' ') . $street_rep;
        $result = trim($result, ', ');

        $result .= ', ' . mb_strtolower($house_rep);
        $result = trim($result, ', ');

        $result .= ', ' . mb_strtolower($building_rep);
        $result = trim($result, ', ');

        $result .= ', ' . mb_strtolower($flat_rep);
        $result = trim($result, ', ');

        return $result;
    }

    /**
     * Выполняет заполнение реквизитов юридического лица.
     * @param $model \common\models\Counteragents
     * @param $details array
     */
    public static function api_fillModelJur($model, $details)
    {
        $model->name = self::api_uppercaseFirstLetters(self::api_extractNameInQuotes($details['shortName']));
        $model->name_full = $details['shortName'];
        $address = '';
        if (isset($details['address'])) $address = self::api_composeFullAddress($details['address']);
        $model->address_j = $address;
        $model->address_p = $model->address_j;
        $model->address_m = $model->address_j;
        $model->kpp = $details['kpp'];
    }

    /**
     * Выполняет заполнение реквизитов физического лица.
     * @param $model \common\models\Counteragents
     * @param $details array
     */
    public static function api_fillModelPhys($model, $details)
    {
        $addon = ''; if (isset($details['type'])) if ($details['type']['id'] == 1) $addon = 'ИП ';

        $model->name = self::api_uppercaseFirstLetters($details['person']['surName']) . ' ' .
            mb_substr($details['person']['firstName'], 0, 1) . '. ' .
            mb_substr($details['person']['middleName'], 0, 1) . '.';
        $model->name_full = $addon . self::api_uppercaseFirstLetters($details['person']['fullName']);
    }

    /**
     * Выполняет проверку, используется ли запись в других элементах.
     * @return bool
     */
    public function checkIfUsed()
    {
        if ($this->getDocuments()->count() > 0) return true;

        return false;
    }

    /**
     * Делает выборку договоров текущего контрагента и возвращает в виде массива.
     * Применяется для вывода в виджетах Select2.
     * @return array
     */
    public function arrayMapOfContractsOfThisCounteragentForSelect2()
    {
        return ArrayHelper::map(Documents::find()->select([
            'id',
            'name' => 'CONCAT("№ ", `documents`.`doc_num`, " от ", DATE_FORMAT(`documents`.`doc_date`, "%d.%m.%Y"))'
        ])->where([
            'ca_id' => $this->id,
            'type_id' => TypesDocuments::DOCUMENT_TYPE_ДОГОВОР
        ])->orderBy('doc_date DESC')->asArray()->all(), 'id', 'name');
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
            $this->updatedBy->username . ' (' . $this->createdBy->profile->name . ')');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Documents::className(), ['id' => 'contract_id']);
    }

    /**
     * Возвращает номер и дату договора.
     * @return string
     */
    public function getContractRep()
    {
        $number = '';
        $date = '-';
        if ($this->contract != null) {
            if ($this->contract->doc_num != null && trim($this->contract->doc_num) != '')
                $number = '№ ' . trim($this->contract->doc_num);

            if ($this->contract->doc_date != null && $this->contract->doc_date != '')
                $date = ($number == '' ? '' : ' ') . 'от ' . Yii::$app->formatter->asDate($this->contract->doc_date, 'php: d.m.Y г.');
        }

        return $number . $date;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(TypesCounteragents::className(), ['id' => 'type_id']);
    }

    /**
     * Возвращает наименование типа контрагента.
     * @return string
     */
    public function getTypeName()
    {
        return $this->type == null ? '' : $this->type->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounteragentsFiles()
    {
        return $this->hasMany(CounteragentsFiles::className(), ['ca_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Documents::className(), ['ca_id' => 'id']);
    }
}
