<?php

namespace backend\controllers;

use Yii;
use common\models\TaxQuarterCalculations;
use common\models\TaxQuarterCalculationsSearch;
use common\models\Periods;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * TaxQuarterCalculationsController implements the CRUD actions for TaxQuarterCalculations model.
 */
class TaxQuarterCalculationsController extends Controller
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
     * Lists all TaxQuarterCalculations models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaxQuarterCalculationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchApplied = Yii::$app->request->get($searchModel->formName()) != null;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchApplied' => $searchApplied,
        ]);
    }

    /**
     * Creates a new TaxQuarterCalculations model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaxQuarterCalculations();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) return $this->redirect(['/tax-quarter-calculations', 'id' => $model->id]);
        } else {
            $period = Periods::getCurrentPeriod();
            $model->calculateTaxAmountByPeriod($period);
            $model->period_id = $period->id;
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TaxQuarterCalculations model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/tax-quarter-calculations']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TaxQuarterCalculations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/tax-quarter-calculations']);
    }

    /**
     * Finds the TaxQuarterCalculations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaxQuarterCalculations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaxQuarterCalculations::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрошенная страница не существует.');
        }
    }

    /**
     * Рендерит поля с расчетами налога за период, переданный в параметрах.
     * @param $period_id integer идентификатор периода
     * @return mixed
     */
    public function actionRenderCalculations($period_id)
    {
        if (Yii::$app->request->isAjax && intval($period_id) > 0) {
            $model = new TaxQuarterCalculations();
            $model->period_id = $period_id;
            $model->calculateTaxAmountByPeriod(Periods::findOne($period_id));

            return $this->renderAjax('_calculations', ['model' => $model, 'form' => new \yii\bootstrap\ActiveForm()]);
        }

        return false;
    }
}
