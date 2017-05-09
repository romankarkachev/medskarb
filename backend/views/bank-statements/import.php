<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\Periods;

/* @var $this yii\web\View */
/* @var $model backend\models\BankStatementsImport */

$this->title = 'Импорт движений по банку за период | '.Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Банковские движения', 'url' => ['/bank-statements']];
$this->params['breadcrumbs'][] = 'Импорт';
?>
<div class="bank-statements-import">
    <div class="card card-accent-primary">
        <div class="card-header">Примечание</div>
        <div class="card-block">
            <p>Выписка за период (например, I квартал 2017 г.), формат выписки - Расширенная</p>
            <p><strong>Обратите также внимание</strong>, что файл импорта, который Вы предоставляете, должен содержать только один лист в книге. В противном случае импорт не может быть выполнен.</p>
            <p><strong>Примеры:</strong></p>
            <?= Html::a(Html::img('/images/how-to-export-data-from-sber.jpg', ['width' => 140]), '/images/how-to-export-data-from-sber.jpg', ['rel' => 'fancybox']); ?>

            <?= Html::a(Html::img('/images/bank-statements-example.jpg', ['width' => 140]), '/images/bank-statements-example.jpg', ['rel' => 'fancybox']); ?>

        </div>
    </div>
    <div class="card">
        <div class="card-block">
            <?php $form = ActiveForm::begin() ?>

            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'period_id')->widget(Select2::className(), [
                        'data' => Periods::arrayMapForSelect2(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                    ]) ?>

                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'importFile')->fileInput(['style' => 'display: block;']) ?>

                </div>
                <div class="col-md-3">
                    <label for="<?= strtolower($model->formName()) ?>-is_preview" class="control-label"><?= $model->attributeLabels()['is_preview'] ?></label>
                    <?= $form->field($model, 'is_preview')->checkbox()->label(false) ?>

                </div>
            </div>
            <div class="form-group">
                <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Банковские движения', ['/bank-statements'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

                <?= Html::submitButton('<i class="fa fa-cloud-upload" aria-hidden="true"></i> Выполнить', ['class' => 'btn btn-success btn-lg']) ?>

            </div>
            <?php ActiveForm::end() ?>

        </div>
    </div>
    <?php if (isset($dataProvider)): ?>
    <?= $this->render('preview', ['dataProvider' => $dataProvider]) ?>
    <?php endif; ?>
</div>
<?php
newerton\fancybox\FancyBox::widget([
    'target' => 'a[rel=fancybox]',
    'helpers' => true,
    'mouse' => true,
    'config' => [
        'maxWidth' => '90%',
        'maxHeight' => '90%',
        'playSpeed' => 7000,
        'padding' => 0,
        'fitToView' => false,
        'width' => '70%',
        'height' => '70%',
        'autoSize' => false,
        'closeClick' => false,
        'openEffect' => 'elastic',
        'closeEffect' => 'elastic',
        'prevEffect' => 'elastic',
        'nextEffect' => 'elastic',
        'closeBtn' => false,
        'openOpacity' => true,
        'helpers' => [
            'title' => ['type' => 'float'],
            'buttons' => [],
            'thumbs' => ['width' => 68, 'height' => 50],
            'overlay' => [
                'css' => [
                    'background' => 'rgba(0, 0, 0, 0.8)'
                ]
            ]
        ],
    ]
]);

$this->registerJs(<<<JS
$('input').iCheck({
    checkboxClass: 'icheckbox_square-green',
});
JS
, yii\web\View::POS_READY);
?>
