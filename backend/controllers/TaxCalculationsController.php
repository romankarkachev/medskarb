<?php

namespace backend\controllers;

use Yii;
use common\models\TaxCalculations;
use common\models\TaxCalculationsSearch;
use common\models\Periods;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * TaxCalculationsController implements the CRUD actions for TaxCalculations model.
 */
class TaxCalculationsController extends Controller
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
     * Lists all TaxCalculations models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaxCalculationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchApplied = Yii::$app->request->get($searchModel->formName()) != null;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchApplied' => $searchApplied,
        ]);
    }

    /**
     * Creates a new TaxCalculations model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaxCalculations();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/tax-calculations', 'id' => $model->id]);
        } else {
            $period = Periods::getCurrentPeriod();
            $model->calculateTaxAmountByPeriod($period);
            $model->period_id = $period->id;

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TaxCalculations model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/tax-calculations']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TaxCalculations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/tax-calculations']);
    }

    /**
     * Finds the TaxCalculations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaxCalculations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaxCalculations::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрошенная страница не существует.');
        }
    }

    public function actionRenderCalculations($period_id)
    {
        $model = new TaxCalculations();
        $model->calculateTaxAmountByPeriod(\common\models\Periods::findOne($period_id));

        return $this->renderAjax('_calculations', ['model' => $model, 'form' => new \yii\bootstrap\ActiveForm()]);
    }
}
