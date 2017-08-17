<?php

namespace backend\controllers;

use backend\models\BankStatementsClear;
use Yii;
use common\models\BankStatements;
use common\models\BankStatementsSearch;
use backend\models\BankStatementsImport;
use common\models\BankStatementsFiles;
use common\models\Counteragents;
use common\models\Periods;
use common\models\SkipBankRecords;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use moonland\phpexcel\Excel;

/**
 * BankStatementsController implements the CRUD actions for BankStatements model.
 */
class BankStatementsController extends Controller
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
                    'index', 'import', 'summary-card', 'set-counteragent', 'toggle-active',
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
                    'set-counteragent' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all BankStatements models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankStatementsSearch();
        $searchApplied = Yii::$app->request->get($searchModel->formName()) != null;

        $period_param = [];
        if (!$searchApplied) {
            // если не применяется отбор, установим параметр отбора Период программно,
            // чтобы не отображать слишком уж много записей
            $period = Periods::getCurrentPeriod();
            if ($period != null) $period_param = [
                $searchModel->formName() => [
                    'period_id' => $period->id
                ]
            ];
        }

        $dataProvider = $searchModel->search(ArrayHelper::merge($period_param, Yii::$app->request->queryParams));

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchApplied' => $searchApplied,
        ]);
    }

    /**
     * Creates a new BankStatements model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BankStatements();

        if ($model->load(Yii::$app->request->post())) {
            $model->scenario = 'create_manual';
            $model->type = BankStatements::TYPE_MANUAL;
            $model->is_active = true;
            $model->inn = $model->ca->inn;

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->validate()) {
                $error_loading_file = false;
                if ($model->imageFile != null) {
                    $upload_path = BankStatementsFiles::getUploadsFilepath();
                    if ($upload_path === false) {
                        $error_loading_file = true;
                        Yii::$app->getSession()->setFlash('error', 'Невозможно создать папку для хранения загруженных файлов!');
                    }
                    else {
                        // сохраняем предоставленный файл и делаем об этом запись в базе
                        $filename = mb_strtolower(Yii::$app->security->generateRandomString() . '.' . $model->imageFile->extension, 'utf-8');
                        $filepath = $upload_path . '/' . $filename;

                        if ($model->imageFile->saveAs($filepath)) {
                            $fu = new BankStatementsFiles();
                            $fu->bs_id = $model->id;
                            $fu->ffp = $filepath;
                            $fu->fn = $filename;
                            $fu->ofn = $model->imageFile->baseName . '.' . $model->imageFile->extension;
                            $fu->size = filesize($filepath);
                        }
                        else
                            $error_loading_file = true;
                    }
                }

                if (!$error_loading_file && $model->save()) {
                    if ($fu != null) {
                        $fu->bs_id = $model->id;
                        if ($fu->validate())
                            $fu->save();
                        else {
                            Yii::$app->getSession()->setFlash('error', 'Загруженные данные неверны.');
                            return $this->redirect(['/bank-statements/update' . $model->id]);
                        }
                    }
                    return $this->redirect(['/bank-statements']);
                }
            }
        }

        $model->scenario = 'create_manual';
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the BankStatements model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BankStatements the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BankStatements::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрошенная страница не существует.');
        }
    }

    /**
     * Формирует и отдает краткую карточку клиента.
     */
    public function actionSummaryCard($id)
    {
        $model = $this->findModel($id);

        return $this->renderAjax('summary_card', [
            'model' => $model,
        ]);
    }

    /**
     * Выполняет сохранение выбранного значения в поле Контрагент.
     * Применяется, когда при импорте не был автоматически идентифицирован контрагент.
     * @return bool
     */
    public function actionSetCounteragent()
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $bs_id = intval(Yii::$app->request->get('bs_id'));
            $ca_id = intval(Yii::$app->request->get('ca_id'));
            if ($bs_id > 0 && $ca_id > 0) {
                // если все необходимые параметры переданы
                $bs = BankStatements::findOne($bs_id);
                $ca = Counteragents::findOne($ca_id);
                if ($bs != null && $ca != null) {
                    // если движение и контрагент идентифицированы
                    $bs->ca_id = $ca_id;
                    $bs->inn = $bs->ca->inn;
                    return $bs->save(false);
                }
            }
        }

        return false;
    }

    /**
     * Переключает активность банковского движения.
     */
    public function actionToggleActive()
    {
        if (Yii::$app->request->isPost) {
            $bs_id = intval(Yii::$app->request->get('bs_id'));
            if ($bs_id > 0) {
                $bs = BankStatements::findOne($bs_id);
                if ($bs != null) {
                    // если движение идентифицировано
                    $bs->is_active = !$bs->is_active;
                    return $bs->save(false);
                }
            }
        }
    }

    /**
     * Импорт из Excel банковской выписки.
     * @return mixed
     */
    public function actionImport()
    {
        $model = new BankStatementsImport();

        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            if ($model->importFile == null)
                return $this->render('import', [
                    'model' => $model,
                ]);

            $filename = Yii::getAlias('@uploads') . '/' . Yii::$app->security->generateRandomString() . '.' . $model->importFile->extension;
            if ($model->upload($filename)) {
                $model->load(Yii::$app->request->post());
                // если файл удалось успешно загрузить на сервер
                // выбираем все данные из файла в массив
                $data = Excel::import($filename, [
                    'setFirstRecordAsKeys' => false,
                ]);
                if (count($data) > 0) {
                    // если удалось прочитать, сразу удаляем файл
                    unlink($filename);

                    // список значений, которые не принимаются к расчету
                    $excludes = SkipBankRecords::find()->select('substring')->column();

                    // инн контрагентов
                    $inns = Counteragents::find()->select('id, inn')->asArray()->all();

                    // перебираем массив и создаем новые элементы
                    $result = []; // массив для элементов при предварительном просмотре
                    $errors_import = array(); // массив для ошибок при импорте
                    $row_number = 1; // 0-я строка - это заголовок
                    foreach ($data as $row) {
                        // проверяем обязательные поля
                        $date = trim($row['B']);
                        // если достигнут конец файла, то заканчиваем процедуру
                        if ($date == '') break;

                        // назначение платежа
                        $description = $row['Y'];

                        // проверим, не встречаются ли слова, которые исключают движение из расчета
                        $is_active = BankStatementsImport::CheckIfExcludes($excludes, $description);

                        // преобразуем дату
                        $bank_date = BankStatementsImport::normalizeDate($date);

                        // проверим, входит ли дата в выбранный период)
                        $date_timestamp = strtotime($bank_date . ' 00:00:00');
                        if ($date_timestamp < $model->period->start || $date_timestamp > $model->period->end) {
                            $errors_import[] = 'Строка '.$row_number.' пропущена из-за несоответствия даты периоду!';
                            $row_number++;
                            continue;
                        }

                        $bank_dt = trim($row['D']);
                        $bank_kt = trim($row['H']);

                        // определим инн
                        $inn = BankStatementsImport::DetermineInn($bank_kt);

                        // определим контрагента
                        $ca_id = BankStatementsImport::DetermineCounteragent($inns, $inn);

                        // преобразуем сумму Дт
                        $amount_dt = BankStatementsImport::normalizeAmount($row['L']);

                        // преобразуем сумму Кт
                        $amount_kt = BankStatementsImport::normalizeAmount($row['P']);

                        // проверим, указана ли хотя бы одна сумма
                        if ($amount_dt == 0 && $amount_kt == 0) {
                            $errors_import[] = 'В строке '.$row_number.' не удалось определить сумму платежа!';
                            $row_number++;
                            continue;
                        }

                        $doc_num = intval(trim($row['R']));
                        if ($doc_num == 0) $errors_import[] = 'В строке '.$row_number.' определен номер платежного поручения!';

                        $new_record = new BankStatements();
                        $new_record->period_id = $model->period_id;
                        $new_record->type = BankStatements::TYPE_AUTO;
                        $new_record->ca_id = $ca_id;
                        $new_record->is_active = $is_active;
                        $new_record->bank_date = $bank_date;
                        $new_record->bank_dt = $bank_dt;
                        $new_record->bank_kt = $bank_kt;
                        $new_record->bank_amount_dt = $amount_dt;
                        $new_record->bank_amount_kt = $amount_kt;
                        $new_record->bank_bik_name = $row['U'];
                        $new_record->bank_doc_num = strval($doc_num);
                        $new_record->bank_description = $description;
                        $new_record->inn = $inn;

                        if ($model->is_preview)
                            // если предварительный просмотр, то добавляем в массив
                            $result[] = $new_record;
                        else if (!$new_record->save(false)) {
                            // иначе сразу сохраняем движение в базу
                            $details = '';
                            foreach ($new_record->errors as $error)
                                foreach ($error as $detail)
                                    $details .= '<p>'.$detail.'</p>';
                            $errors_import[] = 'В строке '.$row_number.' не удалось сохранить новый элемент.'.$details;
                        }

                        $row_number++;
                    }; // foreach

                    // зафиксируем ошибки, чтобы показать
                    if (count($errors_import) > 0) {
                        $errors = '';
                        foreach ($errors_import as $error)
                            $errors .= '<p>'.$error.'</p>';
                        Yii::$app->getSession()->setFlash('error', $errors);
                    };

                    if ($model->is_preview) {
                        // если пользователь выполняет предварительный просмотр
                        $searchModel = new BankStatementsSearch();
                        $dataProvider = new ArrayDataProvider([
                            'modelClass' => 'common\models\BankStatements',
                            'allModels' => $result,
                            'key' => 'id', // поле, которое заменяет primary key
                            'pagination' => [
                                'pageSize' => false,
                            ],
                            'sort' => [
                                'defaultOrder' => ['bank_date' => SORT_ASC],
                                'attributes' => [
                                    'bank_date',
                                ],
                            ],
                        ]);

                        return $this->render('import', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                }; // count > 0

                 return $this->redirect(['/bank-statements']);
            }
        };

        // подставим предыдущий период
        $period = Periods::getPreviousPeriod();
        if ($period != null) $model->period_id = $period->id;

        return $this->render('import', [
            'model' => $model,
        ]);
    }

    /**
     * Очистка банковских движений (удаление всех записей за выбранный период).
     * @return mixed
     */
    public function actionClear()
    {
        $model = new BankStatementsClear();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            BankStatements::deleteAll(['period_id' => $model->period_id, 'type' => BankStatements::TYPE_AUTO]);

            return $this->redirect(['/bank-statements']);
        }

        return $this->render('clear', [
            'model' => $model,
        ]);
    }
}
