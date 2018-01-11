<?php

namespace backend\controllers;

use common\models\Profile;
use common\models\User;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use dektrium\user\models\SettingsForm;
use dektrium\user\controllers\SettingsController as BaseSettingsController;

/**
 * Расширение класса.
 * @author Roman Karkachev <post@romankarkachev.ru>
 */
class UserSettingsController extends BaseSettingsController
{

    /**
     * Отображает форму с профилем пользователя.
     * @return mixed
     */
    public function actionProfile()
    {
        $model = $this->finder->findProfileById(\Yii::$app->user->identity->getId());

        if ($model == null) {
            $model = \Yii::createObject(Profile::className());
            $model->link('user', \Yii::$app->user->identity);
        }

        $event = $this->getProfileEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Your profile has been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('/user/settings/profile', [
            'model' => $model,
            'username' => User::getCurrentUserFullRepresentation(),
        ]);
    }

    /**
     * Отображает страницу аккаунта пользователя, где он может поменять почту, логин, пароль.
     * @return mixed
     */
    public function actionAccount()
    {
        /* @var $model SettingsForm */
        $model = \Yii::createObject(SettingsForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_ACCOUNT_UPDATE, $event);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account details have been updated'));
            $this->trigger(self::EVENT_AFTER_ACCOUNT_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('/user/settings/account', [
            'model' => $model,
            'username' => User::getCurrentUserFullRepresentation(),
        ]);
    }

    /**
     * Disconnects a network account from user.
     *
     * @param int $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDisconnect($id)
    {
        $account = $this->finder->findAccount()->byId($id)->one();

        if ($account === null) {
            throw new NotFoundHttpException();
        }
        if ($account->user_id != \Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $event = $this->getConnectEvent($account, $account->user);

        $this->trigger(self::EVENT_BEFORE_DISCONNECT, $event);
        $account->delete();
        $this->trigger(self::EVENT_AFTER_DISCONNECT, $event);

        return $this->redirect(['/profile']);
    }
}
