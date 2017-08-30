<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use romankarkachev\coreui\widgets\Alert;
use romankarkachev\coreui\widgets\Sidebar;
use romankarkachev\coreui\widgets\Breadcrumbs;
use backend\assets\AppAsset;

AppAsset::register($this);

romankarkachev\coreui\CoreUIAsset::register($this);

\hiqdev\assets\icheck\iCheckAsset::register($this);

$items = [
    ['label' => 'Контрагенты', 'icon' => 'fa fa-address-book-o', 'url' => ['/counteragents']],
    ['label' => 'Сделки', 'icon' => 'fa fa-handshake-o', 'url' => ['/deals']],
    ['label' => '<li class="nav-title">Документы</li>'],
    ['label' => 'Прих. накл.', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/receipts']],
    ['label' => 'Расх. накл.', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/expenses']],
    ['label' => 'Акты брокера РФ', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/broker-ru']],
    ['label' => 'Акты брокера ЛНР', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/broker-lnr']],
    ['label' => 'Договоры', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/contracts']],
    ['label' => 'Все', 'icon' => 'fa fa-folder-open', 'url' => ['/documents']],
    ['label' => '<li class="nav-title">Налогообложение</li>'],
    ['label' => 'Банковские движения', 'icon' => 'fa fa-bank', 'url' => ['/bank-statements']],
    ['label' => 'Расчеты налога', 'icon' => 'fa fa-balance-scale', 'url' => ['/tax-calculations']],
    [
        'label' => 'Справочники',
        'url' => '#',
        'items' => [
            ['label' => 'Периоды', 'icon' => 'fa fa-calendar', 'url' => ['/periods']],
            ['label' => 'Игнор для банка', 'icon' => 'fa fa-bank', 'url' => ['/skip-bank-records']],
        ],
    ],
];
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
