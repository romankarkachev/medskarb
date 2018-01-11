<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use romankarkachev\coreui\widgets\Alert;
use romankarkachev\coreui\widgets\Sidebar;
use romankarkachev\coreui\widgets\Breadcrumbs;
use backend\assets\AppAsset;
use common\models\User;

AppAsset::register($this);

romankarkachev\coreui\CoreUIAsset::register($this);

\hiqdev\assets\icheck\iCheckAsset::register($this);

$items = User::prepareUserSidebarMenu();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?= $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/images/favicon.png']) ?>
    <?php $this->head() ?>
</head>
<body class="app header-fixed sidebar-fixed">
<?php $this->beginBody() ?>
<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button"><i class="fa fa-bars" aria-hidden="true"></i></button>
    <a class="navbar-brand" href="#"></a>
    <ul class="nav navbar-nav d-md-down-none">
        <li class="nav-item">
            <a class="nav-link navbar-toggler sidebar-toggler" href="#"><i class="fa fa-bars" aria-hidden="true"></i></a>
        </li>
    </ul>
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item">
            <?= Html::a('<i class="icon-user"></i>', ['/profile'], ['class' => 'nav-link', 'title' => 'Профиль']) ?>

        </li>
        <li class="nav-item">
            <?= Html::a('<i class="icon-logout"></i>', ['/logout'], ['class' => 'nav-link', 'title' => 'Выйти из системы', 'data-method' => 'post']) ?>

        </li>
    </ul>
</header>
<div class="app-body">
    <div class="sidebar">
        <nav class="sidebar-nav">
            <?= Sidebar::widget([
                'options' => ['id' => 'side-menu', 'class' => 'nav'],
                'encodeLabels' => false,
                'items' => $items,
            ]) ?>

        </nav>
    </div>
    <main class="main">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => '<i class="fa fa-home"></i>',
                'url' => Yii::$app->homeUrl,
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'linksAtRight' => isset($this->params['breadcrumbsRight']) ? $this->params['breadcrumbsRight'] : [],
            'encodeLabels' => false,
        ]) ?>

        <div class="container-fluid">
            <?= Alert::widget() ?>

            <?= $content ?>

        </div>
    </main>
</div>
<footer class="app-footer">
    &copy; <?= date('Y') ?> <?= Html::a(Yii::$app->name, ['/']) ?>

    <span class="float-right">Вы авторизованы как <?= Yii::$app->user->identity->username . (Yii::$app->user->identity->profile->name == null || Yii::$app->user->identity->profile->name == '' ? '' : ' (' . Yii::$app->user->identity->profile->name) . ')' ?>.</span>
</footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
