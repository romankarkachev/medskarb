<?php

namespace backend\controllers;

use Yii;
use common\models\TaxYearCalculations;
use common\models\TaxYearCalculationsSearch;
use common\models\TaxYearCalculationsFiles;
use common\models\TaxYearCalculationsFilesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * TaxYearCalculationsController implements the CRUD actions for TaxYearCalculations model.
 */
class TaxYearCalculationsController extends Controller
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
                            'index', 'create', 'update', 'delete', 'render-calculations',
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
                ],
            ],
        ];
    }

    /**
     * Lists all TaxYearCalculations models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaxYearCalculationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchApplied = Yii::$app->request->get($searchModel->formName()) != null;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchApplied' => $searchApplied,
        ]);
    }

    /**
     * Creates a new TaxYearCalculations model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaxYearCalculations();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) return $this->redirect(['/tax-year-calculations']);
        } else {
            $year = date('Y');
            $year--;
            $model->year = $year;
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TaxYearCalculations model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) return $this->redirect(['/tax-year-calculations']);
        }

        // файлы к годовому расчету
        $searchModel = new TaxYearCalculationsFilesSearch();
        $dpFiles = $searchModel->search([$searchModel->formName() => ['tyc_id' => $model->id]]);
        $dpFiles->setSort([
            'defaultOrder' => ['uploaded_at' => SORT_DESC],
        ]);
        $dpFiles->pagination = false;

        return $this->render('update', [
            'model' => $model,
            'dpFiles' => $dpFiles,
        ]);
    }

    /**
     * Deletes an existing TaxYearCalculations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/tax-year-calculations']);
    }

    /**
     * Finds the TaxYearCalculations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaxYearCalculations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaxYearCalculations::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрошенная страница не существует.');
        }
    }

    /**
     * Рендерит поля с расчетами налога за год, переданный в параметрах.
     * @param $year integer
     * @return mixed
     */
    public function actionRenderCalculations($year)
    {
        if (Yii::$app->request->isAjax && intval($year) > 0) {
            $model = new TaxYearCalculations();
            $model->year = $year;
            $model->calculateTaxAmount($year);

            return $this->renderAjax('_calculations', ['model' => $model, 'form' => new \yii\bootstrap\ActiveForm()]);
        }

        return false;
    }

    /**
     * Загрузка файлов, перемещение их из временной папки, запись в базу данных.
     * @return mixed
     */
    public function actionUploadFiles()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $obj_id = Yii::$app->request->post('owner_id');
        $upload_path = TaxYearCalculationsFiles::getUploadsFilepath();
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
                    $fu = new TaxYearCalculationsFiles();
                    $fu->tyc_id = $obj_id;
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
            $model = TaxYearCalculationsFiles::findOne($id);
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
        $model = TaxYearCalculationsFiles::findOne(['guid' => $guid]);
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
        $model = TaxYearCalculationsFiles::findOne(['guid' => $guid]);
        if ($model != null) {
            if ($model->isImage())
                return \yii\helpers\Html::img(Yii::getAlias('@uploads-tyc') . '/' . $model->fn, ['width' => '100%']);
            else
                return '<iframe src="http://docs.google.com/gview?url=' . Yii::$app->urlManager->createAbsoluteUrl(['/tax-year-calculations/download-from-outside', 'guid' => $guid]) . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
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
        $model = TaxYearCalculationsFiles::findOne($id);
        if ($model != null) {
            $obj_id = $model->tyc_id;
            $model->delete();

            return $this->redirect(['/tax-year-calculations/update', 'id' => $obj_id]);
        }
        else
            throw new NotFoundHttpException('Файл не обнаружен.');
    }
}
