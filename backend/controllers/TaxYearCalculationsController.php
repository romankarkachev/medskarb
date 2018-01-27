<?php

namespace backend\controllers;

use Yii;
use common\models\TaxYearCalculations;
use common\models\TaxYearCalculationsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
                        'actions' => ['index', 'create', 'update', 'delete', 'render-calculations'],
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/tax-year-calculations']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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
}
