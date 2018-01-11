<?php

namespace backend\controllers;

use common\models\FinanceSearch;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use common\models\User;
use dektrium\user\models\UserSearch;
use dektrium\user\controllers\AdminController as BaseAdminController;

class UsersController extends BaseAdminController
{
    /**
     * Отображает список пользователей.
     * @inheritdoc
     */
    public function actionIndex()
    {
        Url::remember('', 'actions-redirect');
        $searchModel  = \Yii::createObject(UserSearch::className());
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        $searchApplied = Yii::$app->request->get($searchModel->formName()) != null;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'searchApplied' => $searchApplied,
        ]);
    }

    /**
     * Создает пользователя
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var $user \common\models\User */
        $user = \Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
        ]);
        $event = $this->getUserEvent($user);

        $this->performAjaxValidation($user);

        $this->trigger(self::EVENT_BEFORE_CREATE, $event);

        if ($user->load(\Yii::$app->request->post()) && $user->create()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been created'));
            $this->trigger(self::EVENT_AFTER_CREATE, $event);
            return $this->redirect(['/users']);
        }

        return $this->render('create', [
            'user' => $user,
        ]);
    }

    /**
     * Shows information about user.
     *
     * @param int $id
     *
     * @return string
     */
    public function actionInfo($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);

        return $this->render('_info', [
            'user' => $user,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($id == \Yii::$app->user->getId()) {
            \Yii::$app->getSession()->setFlash('danger', \Yii::t('user', 'You can not remove your own account'));
        } else {
            $model = $this->findModel($id);

            if ($model->checkIfUsed())
                return $this->render('@backend/views/common/cannot_delete', [
                    'details' => [
                        'breadcrumbs' => ['label' => 'Пользователи', 'url' => ['/users']],
                        'modelRep' => $model->profile->name,
                        'buttonCaption' => 'Пользователи',
                        'buttonUrl' => ['/users'],
                    ],
                ]);

            $event = $this->getUserEvent($model);
            $this->trigger(self::EVENT_BEFORE_DELETE, $event);
            $model->delete();
            $this->trigger(self::EVENT_AFTER_DELETE, $event);
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been deleted'));
        }

        return $this->redirect(['index']);
    }

    /**
     * @inheritdoc
     */
    protected function findModel($id)
    {
        $user = $this->finder->findUserById($id);
        if ($user === null) {
            throw new NotFoundHttpException('Запрошенная страница не существует');
        }

        return $user;
    }
}
