<?php

namespace backend\controllers;

use common\models\Deals;
use common\models\DealsDocuments;
use common\models\DocumentsFilesSearch;
use common\models\Settings;
use common\models\TypesCounteragents;
use common\models\TypesDocuments;
use Yii;
use common\models\Documents;
use common\models\DocumentsSearch;
use common\models\DocumentsFiles;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * DocumentsController implements the CRUD actions for Documents model.
 */
class DocumentsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    // при добавлении действия продублировать при необходимости в UrlManager в common\config\main.php
                    'index', 'create', 'update', 'delete',
                    'contracts', 'receipts', 'expenses', 'broker-ru', 'broker-lnr',
                    'upload-files', 'download', 'preview-file', 'delete-file',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['root'],
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
     * Отображает страницу со списком документов с возможностью отбора, если передано значение в параметрах.
     * @param $action_id string идентификатор действия, например, contracts
     * @return mixed
     */
    public function actionIndex($action_id = null)
    {
        $searchModel = new DocumentsSearch();

        $conditions = [];
        $type_id = null;
        $final_bc = ''; // последняя хлебная крошка
        $documentSettings = Documents::fetchDocumentsSettings($searchModel->formName());
        if (isset($documentSettings[$action_id])) {
            $conditions = $documentSettings[$action_id]['searchConditions'];
            $type_id = $documentSettings[$action_id]['type_id'];
            $final_bc = $documentSettings[$action_id]['final_bc'];
        }

        $dataProvider = $searchModel->search(ArrayHelper::merge(
            $conditions,
            Yii::$app->request->queryParams
        ), 'documents/' . $this->action->id);

        $searchApplied = Yii::$app->request->get($searchModel->formName()) != null;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchApplied' => $searchApplied,
            'action_id' => $action_id,
            'type_id' => $type_id,
            'final_bc' => $final_bc,
        ]);
    }

    /**
     * Creates a new Documents model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Documents();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->includeInDeal_id != null) {
                // пользователь пожелал добавить документ сразу в сделку
                $deal = Deals::findOne($model->includeInDeal_id);
                if ($deal != null) {
                    $relation = new DealsDocuments();
                    $relation->doc_id = $model->id;
                    $relation->deal_id = $deal->id;
                    $relation->save();
                }
            }

            $action_id = null;
            $ds = $model->getDocumentsListUrlByType();
            if (is_array($ds)) if (count($ds) == 2) {
                $action_id = $ds['url'];
            }

            if ($action_id == null)
                return $this->redirect(['/documents']);
            else
                return $this->redirect(['/documents/' . $action_id]);
        } else {
            $action_id = null;
            $final_bc = 'Документы'; // последняя хлебная крошка
            if (Yii::$app->request->isPost) {
                // если в POST-параметрах пришел тип документа, то установим его
                $action_id = Yii::$app->request->post('action_id');
                $documentSettings = Documents::fetchDocumentsSettings();
                if (isset($documentSettings[$action_id])) {
                    $model->type_id = $documentSettings[$action_id]['type_id'];
                    $final_bc = $documentSettings[$action_id]['final_bc'];

                    $settings = Settings::findOne(1);
                    if ($settings != null) {
                        switch ($model->type_id) {
                            case TypesDocuments::DOCUMENT_TYPE_РАСХОДНАЯ_НАКЛАДНАЯ:
                                // установим основного покупателя
                                $model->ca_id = $settings->default_buyer_id;
                                break;
                            case TypesDocuments::DOCUMENT_TYPE_АКТ_ВЫПОЛНЕННЫХ_РАБОТ:
                                if (isset($documentSettings[$action_id]['ca_type_id'])) {
                                    if ($documentSettings[$action_id]['ca_type_id'] == TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_РФ)
                                        $model->ca_id = $settings->default_broker_ru;
                                    elseif ($documentSettings[$action_id]['ca_type_id'] == TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_ЛНР)
                                        $model->ca_id = $settings->default_broker_lnr;
                                }
                                break;
                        }
                    }
                }
            }

            return $this->render('create', [
                'model' => $model,
                'action_id' => $action_id,
                'final_bc' => $final_bc,
            ]);
        }
    }

    /**
     * Updates an existing Documents model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $action_id = null;
        $final_bc = 'Документы'; // последняя хлебная крошка
        $ds = $model->getDocumentsListUrlByType();
        if (is_array($ds)) if (count($ds) == 2) {
            $action_id = $ds['url'];
            $final_bc = $ds['bc'];
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($action_id == null)
                return $this->redirect(['/documents']);
            else
                return $this->redirect(['/documents/' . $action_id]);
        } else {
            // файлы к документу
            $searchModel = new DocumentsFilesSearch();
            $dpFiles = $searchModel->search([$searchModel->formName() => ['doc_id' => $model->id]]);
            $dpFiles->setSort([
                'defaultOrder' => ['uploaded_at' => SORT_DESC],
            ]);
            $dpFiles->pagination = false;

            return $this->render('update', [
                'model' => $model,
                'dpFiles' => $dpFiles,
                'action_id' => $action_id,
                'final_bc' => $final_bc,
            ]);
        }
    }

    /**
     * Deletes an existing Documents model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $action_id = null;
        $ds = $model->getDocumentsListUrlByType();
        if (is_array($ds)) if (count($ds) == 2) {
            $action_id = $ds['url'];
        }

        $model->delete();

        if ($action_id == null)
            return $this->redirect(['/documents']);
        else
            return $this->redirect(['/documents/' . $action_id]);
    }

    /**
     * Finds the Documents model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Documents the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Documents::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрошенная страница не существует.');
        }
    }

    /**
     * Загрузка файлов, перемещение их из временной папки, запись в базу данных.
     * @return mixed
     */
    public function actionUploadFiles()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $obj_id = Yii::$app->request->post('owner_id');
        $upload_path = DocumentsFiles::getUploadsFilepath();
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
                    $fu = new DocumentsFiles();
                    $fu->doc_id = $obj_id;
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
            $model = DocumentsFiles::findOne($id);
            if (file_exists($model->ffp))
                return Yii::$app->response->sendFile($model->ffp, $model->ofn);
            else
                throw new NotFoundHttpException('Файл не обнаружен.');
        };
    }

    /**
     * Выполняет предварительный показ изображения.
     * @param $id integer идентификатор файла, который необходимо показать
     * @return bool
     */
    public function actionPreviewFile($id)
    {
        $model = DocumentsFiles::findOne($id);
        if ($model != null) {
            if ($model->isImage())
                return \yii\helpers\Html::img(Yii::getAlias('@uploads-docs') . '/' . $model->fn, ['width' => '100%']);
            else
                return '<iframe src="http://docs.google.com/gview?url=' . Yii::$app->urlManager->createAbsoluteUrl(['/documents/download-file', 'id' => $id]) . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
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
        $model = DocumentsFiles::findOne($id);
        if ($model != null) {
            $obj_id = $model->doc_id;
            $model->delete();

            return $this->redirect(['/documents/update', 'id' => $obj_id]);
        }
        else
            throw new NotFoundHttpException('Файл не обнаружен.');
    }
}
