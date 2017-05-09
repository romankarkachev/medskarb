<?php

namespace backend\controllers;

use common\models\DealsDocuments;
use common\models\Documents;
use common\models\TypesDocuments;
use Yii;
use common\models\Deals;
use common\models\DealsSearch;
use common\models\DealsFiles;
use common\models\DealsFilesSearch;
use common\models\Counteragents;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * DealsController implements the CRUD actions for Deals model.
 */
class DealsController extends Controller
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
                    'index', 'create', 'update', 'delete', 'delete-document',
                    'compose-contract-field', 'compose-amount-used-fields',
                    'add-documents-through-select', 'render-unattached-documents',
                    'upload-files', 'download', 'delete-file',
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
                    'delete-document' => ['POST'],
                    'add-documents-through-select' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Deals models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DealsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchApplied = Yii::$app->request->get($searchModel->formName()) != null;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchApplied' => $searchApplied,
        ]);
    }

    /**
     * Отображает документы, присоединенные к сделке.
     * Используется только для работы pjax в форме сделки.
     * @param $id integer идентификатор сделки
     * @return mixed
     */
    public function actionDealDocuments($id)
    {
        $model = $this->findModel($id);

        $dataProviders = $model->collectDocumentsDataProviders();

        return $this->render('_documents', [
            'model' => $model,
            'dpDocumentsRecepit' => $dataProviders['dpDocumentsRecepit'],
            'dpDocumentsExpense' => $dataProviders['dpDocumentsExpense'],
            'dpDocumentsBrokerRu' => $dataProviders['dpDocumentsBrokerRu'],
            'dpDocumentsBrokerLnr' => $dataProviders['dpDocumentsBrokerLnr'],
        ]);
    }

    /**
     * Creates a new Deals model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Deals();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/deals/update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'contracts' => [],
            ]);
        }
    }

    /**
     * Updates an existing Deals model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/deals']);
        } else {
            // договоры с контрагентом
            $contracts = $model->customer->arrayMapOfContractsOfThisCounteragentForSelect2();

            // использовано средств по договору
            $amountUsed = $model->contract->getAmountUsed();

            // документы к сделке
            $dataProviders = $model->collectDocumentsDataProviders();

            // файлы к сделке и связанным объектам
            // соберем идентификаторы привязанных документов в массив
            $attachedDocsIds = [];
            foreach ($dataProviders as $attachedDocs) {
                foreach ($attachedDocs->getModels() as $deal_doc_relation) {
                    $attachedDocsIds[] = $deal_doc_relation->doc_id;
                }
            }
            $dpFiles = $model->collectFilesFromAllRelatedObjects($attachedDocsIds);

            // свободные (не привязанные ни к одной сделке) документы
            $dpDocumentsUnattached = Deals::collectUnattachedDocuments();

            return $this->render('update', [
                'model' => $model,
                'contracts' => $contracts,
                'amountUsed' => $amountUsed,
                'dpFiles' => $dpFiles,
                'dpDocumentsUnattached' => $dpDocumentsUnattached,
                'dpDocumentsRecepit' => $dataProviders['dpDocumentsRecepit'],
                'dpDocumentsExpense' => $dataProviders['dpDocumentsExpense'],
                'dpDocumentsBrokerRu' => $dataProviders['dpDocumentsBrokerRu'],
                'dpDocumentsBrokerLnr' => $dataProviders['dpDocumentsBrokerLnr'],
            ]);
        }
    }

    /**
     * Deletes an existing Deals model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/deals']);
    }

    /**
     * Finds the Deals model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Deals the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Deals::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрошенная страница не существует.');
        }
    }

    /**
     * Возвращает блок с не привязанными ни к одной сделке документами.
     * @return string
     */
    public function renderUnattachedDocuments()
    {
        return $this->renderPartial('_gw_unattached_documents', [
            'dpDocumentsUnattached' => Deals::collectUnattachedDocuments(),
        ]);
    }

    /**
     * Рендерит секцию с не привязанными ни к одной сделке документами.
     * @return string
     */
    public function actionRenderUnattachedDocuments()
    {
        return $this->renderUnattachedDocuments();
    }

    /**
     * Добавляет переданные в параметрах идентификаторы документов в сделку.
     * @return mixed
     */
    public function actionAddDocumentsThroughSelect()
    {
        // идентификатор сделки
        $deal_id = intval(Yii::$app->request->post('deal_id'));
        $deal = Deals::findOne($deal_id);
        if ($deal != null) {
            // идентификаторы документов в виде массига (дешего, массиг, пиго)
            $ids = Yii::$app->request->post('ids');
            if (count($ids) > 0)
                foreach ($ids as $row) {
                    $dd = new DealsDocuments();
                    $dd->deal_id = $deal_id;
                    $dd->doc_id = $row;
                    $dd->save();
                }
        }

        return $this->renderUnattachedDocuments();
    }

    /**
     * Удаляет документ из сделки.
     * @param $id integer идентификатор привязки
     * @return mixed
     */
    public function actionDeleteDocument($id, $type_id)
    {
        if (Yii::$app->request->isPjax) {
            $model = DealsDocuments::findOne($id);
            if ($model != null) {
                $deal = $model->deal;
                $model->delete();

                // документы к сделке
                $dataProviders = $deal->collectDocumentsDataProviders();

                switch ($type_id) {
                    case TypesDocuments::DOCUMENT_TYPE_ПРИХОДНАЯ_НАКЛАДНАЯ:
                        return $this->renderPartial('_gw_receipts', [
                            'model' => $deal,
                            'dpDocumentsRecepit' => $dataProviders['dpDocumentsRecepit'],
                        ]);
                    case TypesDocuments::DOCUMENT_TYPE_РАСХОДНАЯ_НАКЛАДНАЯ:
                        return $this->renderPartial('_gw_expenses', [
                            'model' => $deal,
                            'dpDocumentsExpense' => $dataProviders['dpDocumentsExpense'],
                        ]);
                    case 4:
                        return $this->renderPartial('_gw_docs_broker_ru', [
                            'model' => $deal,
                            'dpDocumentsBrokerRu' => $dataProviders['dpDocumentsBrokerRu'],
                        ]);
                    case 5:
                        return $this->renderPartial('_gw_docs_broker_lnr', [
                            'model' => $deal,
                            'dpDocumentsBrokerLnr' => $dataProviders['dpDocumentsBrokerLnr'],
                        ]);
                }
            }
        }

        return false;
    }

    /**
     * Отображает поле Договор при его запросе из карточки сделки.
     * @param $customer_id integer идентификатор контрагента, договоры которого будут извлекаться
     * @return mixed
     */
    public function actionComposeContractField($customer_id)
    {
        if (Yii::$app->request->isAjax) {
            $counteragent = Counteragents::findOne($customer_id);
            if ($counteragent != null) {
                // просто новая сделка
                $model = new Deals();
                // установим договор в значение текущего основного договора контрагента,
                // чтобы select2 сразу выбрал его
                $model->contract_id = $counteragent->contract_id;

                $contracts = $counteragent->arrayMapOfContractsOfThisCounteragentForSelect2();

                return $this->renderAjax('_contract', [
                    'model' => $model,
                    'form' => ActiveForm::begin(),
                    'contracts' => $contracts,
                ]);
            }
        }
    }

    /**
     * Отображает поле Использовано средств по договору при его запросе из карточки сделки.
     * @param $contract_id integer идентификатор договора
     * @return mixed
     */
    public function actionComposeAmountUsedFields($contract_id)
    {
        if (Yii::$app->request->isAjax) {
            $contract = Documents::findOne($contract_id);
            if ($contract != null) {
                return $this->renderAjax('_contract_amount_used', [
                    'contract' => $contract,
                ]);
            }
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
        $upload_path = DealsFiles::getUploadsFilepath();
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
                    $fu = new DealsFiles();
                    $fu->deal_id = $obj_id;
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
            $model = DealsFiles::findOne($id);
            if (file_exists($model->ffp))
                return Yii::$app->response->sendFile($model->ffp, $model->ofn);
            else
                throw new NotFoundHttpException('Файл не обнаружен.');
        };
    }

    /**
     * Удаляет файл, привязанный к объекту.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException если файл не будет обнаружен
     */
    public function actionDeleteFile($id)
    {
        $model = DealsFiles::findOne($id);
        if ($model != null) {
            $obj_id = $model->deal_id;
            $model->delete();

            return $this->redirect(['/deals/update', 'id' => $obj_id]);
        }
        else
            throw new NotFoundHttpException('Файл не обнаружен.');
    }
}
