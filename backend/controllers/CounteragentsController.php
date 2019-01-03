<?php

namespace backend\controllers;

use Yii;
use common\models\Counteragents;
use common\models\CounteragentsSearch;
use common\models\CounteragentsFiles;
use common\models\CounteragentsFilesSearch;
use common\models\Documents;
use common\models\TypesCounteragents;
use common\models\TypesDocuments;
use common\models\DadataAPI;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\httpclient\Client;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * CounteragentsController implements the CRUD actions for Counteragents model.
 */
class CounteragentsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['download-from-outside'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => [
                            'index', 'create', 'update', 'delete', 'summary-card', 'er',
                            'render-counteragents-info', 'render-ambiguous-counteragents-info',
                            'fetch-bank-by-bik', 'fetch-counteragents-info-by-inn-orgn', 'fetch-counteragents-info-dadata',
                            'list-for-document', 'list-of-customers', 'list-of-brokers-ru', 'list-of-brokers-lnr',
                            'upload-files', 'download', 'preview-file', 'delete-file',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-file' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Counteragents models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CounteragentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchApplied = Yii::$app->request->get($searchModel->formName()) != null;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchApplied' => $searchApplied,
        ]);
    }

    /**
     * Creates a new Counteragents model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Counteragents();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->isCreateNewContract) {
                // создадим новый договор
                $contract = new Documents();
                $contract->type_id = TypesDocuments::DOCUMENT_TYPE_ДОГОВОР;
                $contract->ca_id = $model->id;
                $contract->doc_num = 'ИЗМЕНИТЕ';
                $contract->doc_date = date('Y-m-d');
                if ($contract->save()) {
                    // привяжем успешно созданный договор к контрагенту
                    $model->contract_id = $contract->id;
                    $model->save();
                }
            }

            if (Yii::$app->request->post('redirect') != null)
                return $this->redirect(['/counteragents/update', 'id' => $model->id]);
            else
                return $this->redirect(['/counteragents']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Counteragents model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/counteragents']);
        } else {
            // файлы к контрагенту
            $searchModel = new CounteragentsFilesSearch();
            $dpFiles = $searchModel->search([$searchModel->formName() => ['ca_id' => $model->id]]);
            $dpFiles->setSort([
                'defaultOrder' => ['uploaded_at' => SORT_DESC],
            ]);
            $dpFiles->pagination = false;

            return $this->render('update', [
                'model' => $model,
                'dpFiles' => $dpFiles,
            ]);
        }
    }

    /**
     * Deletes an existing Counteragents model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->checkIfUsed())
            return $this->render('cannot_delete', [
                'model' => $model,
            ]);

        $model->delete();

        return $this->redirect(['/counteragents']);
    }

    /**
     * Finds the Counteragents model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Counteragents the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Counteragents::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрошенная страница не существует.');
        }
    }

    /**
     * Отображает страницу "Единый реестр".
     * @return mixed
     */
    public function actionEr()
    {
        return $this->render('er');
    }

    /**
     * Рендерит блок с данными контрагента.
     * @param $details array
     * @param $type_id integer
     * @return string
     */
    public function renderCounteragentsInfo($details, $type_id)
    {
        if (count($details) > 0) if (count($details) == 1) {
            $details = $details[0];
            $inn = $type_id == Counteragents::API_CA_TYPE_ЮРЛИЦО ? $details['inn'] : $details['person']['inn'];

            $model = null;
            // проверим по инн, есть ли такой контрагент у нас в базе
            $existing_ca = Counteragents::find()->where(['inn' => $inn])->one();
            if ($existing_ca == null) {
                $model = new Counteragents();
                $model->type_id = TypesCounteragents::COUNTERAGENT_TYPE_ПОСТАВЩИК;
                if ($type_id == Counteragents::API_CA_TYPE_ЮРЛИЦО)
                    Counteragents::api_fillModelJur($model, $details);
                else
                    Counteragents::api_fillModelPhys($model, $details);
                $model->inn = $inn;
                $model->ogrn = $details['ogrn'];
                if (isset($details['email'])) $model->email = strtolower($details['email']);
            }
            //var_dump($details);
            if ($type_id == Counteragents::API_CA_TYPE_ЮРЛИЦО)
                return $this->renderAjax('_er_results_j', [
                    'model' => $model,
                    'details' => $details
                ]);
            else
                return $this->renderAjax('_er_results_p', [
                    'model' => $model,
                    'details' => $details
                ]);
        }
        else {
            //616612334910
            return $this->renderAjax('_er_results_a', [
                'type_id' => $type_id,
                'details' => $details
            ]);
        }

        return '';
    }

    /**
     * Рендерит блок с данными контрагента.
     * Информация поступает из API веб-сервиса.
     * @param $type_id integer тип контрагента (1 - юрлицо, 2 - физлицо)
     * @param $field_id integer поле для поиска данных (1 - инн, 2 - огрн(ип), 3 - наименование)
     * @param $value string значение для поиска
     * @return mixed
     */
    public function actionRenderCounteragentsInfo($type_id, $field_id, $value)
    {
        return $this->renderCounteragentsInfo(Counteragents::apiFetchCounteragentsInfo($type_id, $field_id, $value), $type_id);
    }

    /**
     * Рендерит блок с данными контрагента, определенного как неоднозначный.
     * Информация поступает из API веб-сервиса.
     * @param $type_id integer тип контрагента (1 - юрлицо, 2 - физлицо)
     * @param $id integer идентификатор контрагента в базе данных API
     * @param $pid integer
     * @return mixed
     */
    public function actionRenderAmbiguousCounteragentsInfo($type_id, $id, $pid)
    {
        return $this->renderCounteragentsInfo(Counteragents::apiFetchAmbiguousCounteragentsInfo($type_id, $id, $pid), $type_id);
    }

    /**
     * Получает информацию о контрагенте по его ИНН или ОГРН.
     * @param $field_id integer поле для поиска данных (1 - инн, 2 - огрн(ип), 3 - наименование)
     * @param $value string значение для поиска
     * @return array|bool
     */
    public function actionFetchCounteragentsInfoByInnOrgn($field_id, $value)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $type_id = 0;
        // определим тип контрагента по количеству символов кода
        switch ($field_id) {
            case Counteragents::API_FIELD_ИНН:
                if (strlen($value) >= 10) {
                    if (strlen($value) == 10)
                        $type_id = Counteragents::API_CA_TYPE_ЮРЛИЦО;
                    elseif (strlen($value) == 12)
                        $type_id = Counteragents::API_CA_TYPE_ФИЗЛИЦО;
                }
                break;
            case Counteragents::API_FIELD_ОГРН:
                if (strlen($value) >= 13) {
                    if (strlen($value) == 13)
                        $type_id = Counteragents::API_CA_TYPE_ЮРЛИЦО;
                    elseif (strlen($value) == 15)
                        $type_id = Counteragents::API_CA_TYPE_ФИЗЛИЦО;
                }
                break;
        }

        if ($type_id > 0) {
            $details = Counteragents::apiFetchCounteragentsInfo($type_id, $field_id, $value);
            if (count($details) == 1) {
                $details = $details[0];
                $model = new Counteragents();
                $result = [];

                switch ($type_id) {
                    case Counteragents::API_CA_TYPE_ЮРЛИЦО:
                        Counteragents::api_fillModelJur($model, $details);
                        $result = [
                            'inn' => $details['inn'],
                            'kpp' => $details['kpp'],
                            'address' => $model->address_j,
                        ];
                        break;
                    case Counteragents::API_CA_TYPE_ФИЗЛИЦО:
                        Counteragents::api_fillModelPhys($model, $details);
                        $result = [
                            'inn' => $details['person']['inn'],
                        ];
                        break;
                }

                $result['ogrn'] = $details['ogrn'];
                $result['name'] = $model->name;
                $result['name_full'] = $model->name_full;
                $result['email'] = isset($details['email']) ? strtolower($details['email']) : '';

                return $result;

            }
        }

        return false;
    }

    /**
     * Получает подробную информацию о контрагенте через сервис dadata.ru.
     * fetch-counteragents-info-dadata
     * @param $query string ИНН или ОГРН контрагента
     * @param $specifyingValue string КПП для уточнения
     * @param $cleanDir integer
     * @return array|false
     */
    public function actionFetchCounteragentsInfoDadata($query, $specifyingValue = null, $cleanDir = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $details = DadataAPI::postRequestToApi($query, $specifyingValue);
        if (false !== $details) {
            $result = [
                'name' => $details['name']['full'],
                'name_full' => $details['name']['full_with_opf'],
                'name_short' => $details['name']['short_with_opf'],
                'inn' => $details['inn'],
                'ogrn' => $details['ogrn'],
            ];
            $result['kpp'] = $details['kpp'];
            $result['address'] = $details['address']['unrestricted_value'];
            if (isset($details['management'])) {
                // полные ФИО директора
                $result['dir_name'] = $details['management']['name'];
                if (intval($cleanDir) == true) {
                    $cleanName = DadataAPI::cleanName($result['dir_name']);
                    if (!empty($cleanName)) {
                        $result['dir_name_of'] = $cleanName['result_genitive'];
                        // сокращенные ФИО директора в именительном падеже
                        $result['dir_name_short'] = $cleanName['surname'] .
                            (!empty($cleanName['name']) ? ' ' . mb_substr($cleanName['name'], 0, 1) . '.' : '') .
                            (!empty($cleanName['patronymic']) ? ' ' . mb_substr($cleanName['patronymic'], 0, 1) . '.' : '');

                        // просклоняем сокращенные ФИО
                        //$cleanShortName = DadataAPI::cleanName($result['dir_name_short']);
                        $cleanShortName = DadataAPI::cleanName('Каркачев');
                        if (!empty($cleanShortName) && isset($cleanShortName['result_genitive'])) {
                            $result['dir_name_short_of'] = $cleanShortName['result_genitive'];
                        }
                        else {
                            // не удалось просклонять, просто берем сокращенные ФИО
                            $result['dir_name_short_of'] = $result['dir_name_short'];
                        }
                    }
                    else {
                        // не удалось просклонять, просто берем полные ФИО
                        $result['dir_name_of'] = $result['dir_name'];
                    }
                }
            }
            if (isset($details['management'])) {
                $result['dir_post'] = $details['management']['post'];
            }

            return $result;
        }

        return false;
    }

    /**
     * Получает информацию о банке по его БИК, переданномму в параметрах.
     * Информация поступает из API веб-сервиса.
     * @param $bik string БИК банка
     * @return array|bool
     */
    public function actionFetchBankByBik($bik)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl('http://www.bik-info.ru/api.html')
            ->setData(['bik' => $bik, 'type' => 'json'])
            ->send();

        if ($response->isOk) {
            if (!isset($response->data['error'])) {
                return [
                    'bank_name' => str_replace("&quot;", "&#039;", htmlspecialchars_decode($response->data['name'])) . ' Г. ' . $response->data['city'],
                    'bank_ca' => $response->data['ks'],
                ];
            }
        }

        return false;
    }

    /**
     * Функция выполняет поиск контрагента по наименованию, переданному в параметрах.
     * @param $q string
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionListForDocument($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Counteragents::find()->select([
            'id' => 'counteragents.id',
            'text' => 'counteragents.name',
        ])
            ->andFilterWhere(['like', 'counteragents.name', $q])
            ->orFilterWhere(['like', 'counteragents.name_full', $q]);

        return ['results' => $query->asArray()->all()];
    }

    /**
     * Функция выполняет отбор покупателей по наименованию, переданному в параметрах.
     * @param $q string
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionListOfCustomers($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Counteragents::find()->select([
            'id' => 'counteragents.id',
            'text' => 'counteragents.name',
            'contract_id',
            'contractRep' => 'CONCAT("№ ", `documents`.`doc_num`, " от ", DATE_FORMAT(`documents`.`doc_date`, "%d.%m.%Y"))',
        ])
            ->leftJoin('documents', 'documents.id = counteragents.contract_id')
            ->andFilterWhere(['counteragents.type_id' => TypesCounteragents::COUNTERAGENT_TYPE_ПОКУПАТЕЛЬ])
            ->andFilterWhere([
                'or',
                ['like', 'counteragents.name', $q],
                ['like', 'counteragents.name_full', $q]
            ]);

        return ['results' => $query->asArray()->all()];
    }

    /**
     * Функция выполняет отбор брокеров РФ по наименованию, переданному в параметрах.
     * @param $q string
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionListOfBrokersRu($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Counteragents::find()->select([
            'id' => 'counteragents.id',
            'text' => 'counteragents.name',
        ])
            ->andFilterWhere(['type_id' => TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_РФ])
            ->andFilterWhere([
                'or',
                ['like', 'counteragents.name', $q],
                ['like', 'counteragents.name_full', $q]
            ]);

        return ['results' => $query->asArray()->all()];
    }

    /**
     * Функция выполняет отбор брокеров ЛНР по наименованию, переданному в параметрах.
     * @param $q string
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionListOfBrokersLnr($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Counteragents::find()->select([
            'id' => 'counteragents.id',
            'text' => 'counteragents.name',
        ])
            ->andFilterWhere(['type_id' => TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_ЛНР])
            ->andFilterWhere([
                'or',
                ['like', 'counteragents.name', $q],
                ['like', 'counteragents.name_full', $q]
            ]);

        return ['results' => $query->asArray()->all()];
    }

    /**
     * Формирует и отдает краткую карточку клиента.
     */
    public function actionSummaryCard($id)
    {
        $model = $this->findModel($id);

        return $this->renderPartial('summary_card', [
            'model' => $model,
        ]);
    }

    /**
     * Загрузка файлов, перемещение их из временной папки, запись в базу данных.
     * @return mixed
     */
    public function actionUploadFiles()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $obj_id = Yii::$app->request->post('owner_id');
        $upload_path = CounteragentsFiles::getUploadsFilepath();
        if ($upload_path === false) return 'Невозможно создать папку для хранения загруженных файлов!';

        // массив загружаемых файлов
        $files = $_FILES['files'];
        // массив имен загружаемых файлов
        $filenames = $files['name'];
        if (count($filenames) > 0)
            for ($i=0; $i < count($filenames); $i++) {
                // идиотское действие, но без него
                // PHP Strict Warning: Only variables should be passed by reference
                $tmp = explode('.', basename($filenames[$i]));
                $ext = end($tmp);
                $filename = mb_strtolower(Yii::$app->security->generateRandomString() . '.' . $ext, 'utf-8');
                $filepath = $upload_path.'/'.$filename;
                if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                    $fu = new CounteragentsFiles();
                    $fu->ca_id = $obj_id;
                    $fu->ffp = $filepath;
                    $fu->fn = $filename;
                    $fu->ofn = $filenames[$i];
                    $fu->size = filesize($filepath);
                    if ($fu->validate())
                        $fu->save();
                    else return 'Загруженные данные неверны.';
                };
            };

        return [];
    }

    /**
     * Отдает на скачивание файл, на который позиционируется по идентификатору из параметров.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException если файл не будет обнаружен
     */
    public function actionDownload($id)
    {
        if (is_numeric($id)) if ($id > 0) {
            $model = CounteragentsFiles::findOne($id);
            if (file_exists($model->ffp))
                return Yii::$app->response->sendFile($model->ffp, $model->ofn);
            else
                throw new NotFoundHttpException('Файл не обнаружен.');
        };
    }

    /**
     * Отдает на скачивание файл, на который позиционируется по идентификатору из параметров.
     * @param integer $guid
     * @return mixed
     * @throws NotFoundHttpException если файл не будет обнаружен
     */
    public function actionDownloadFromOutside($guid)
    {
        $model = CounteragentsFiles::findOne(['guid' => $guid]);
        if (file_exists($model->ffp))
            return Yii::$app->response->sendFile($model->ffp, $model->ofn);
        else
            throw new NotFoundHttpException('Файл не обнаружен.');
    }

    /**
     * Выполняет предварительный показ изображения.
     * @param $guid integer идентификатор файла, который необходимо показать
     * @return bool
     */
    public function actionPreviewFile($guid)
    {
        $model = CounteragentsFiles::findOne(['guid' => $guid]);
        if ($model != null) {
            if ($model->isImage())
                return \yii\helpers\Html::img(Yii::getAlias('@uploads-ca') . '/' . $model->fn, ['width' => '100%']);
            else
                return '<iframe src="http://docs.google.com/gview?url=' . Yii::$app->urlManager->createAbsoluteUrl(['/counteragents/download-from-outside', 'guid' => $guid]) . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
        }

        return false;
    }

    /**
     * Удаляет файл, привязанный к объекту.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException если файл не будет обнаружен
     */
    public function actionDeleteFile($id)
    {
        $model = CounteragentsFiles::findOne($id);
        if ($model != null) {
            $obj_id = $model->ca_id;
            $model->delete();

            return $this->redirect(['/counteragents/update', 'id' => $obj_id]);
        }
        else
            throw new NotFoundHttpException('Файл не обнаружен.');
    }
}
