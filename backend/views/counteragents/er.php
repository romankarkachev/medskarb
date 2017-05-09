<?php

use yii\helpers\Html;
use kartik\select2\Select2;

/* @var $this yii\web\View */

$this->title = 'Поиск по Единому реестру | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Контрагенты', 'url' => ['/counteragents']];
$this->params['breadcrumbs'][] = 'Поиск по Единому реестру';
?>
<div class="counteragents-er">
    <div class="card text-center">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#rj" role="tab" aria-controls="home">Юридические лица</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#rp" role="tab" aria-controls="profile">Физические лица</a>
                </li>
            </ul>
        </div>
        <div class="card-block">
            <div class="tab-content">
                <div class="tab-pane active" id="rj" role="tabpanel">
                    <h4 class="card-title">Поиск по реестру юридических лиц</h4>
                    <p class="card-text">Выберите поле для поиска, введите искомое значение и нажмите кнопку.</p>
                    <p>
                        <div class="row">
                            <div class="col-md-2 offset-md-3">
                                <?= Select2::widget([
                                    'id' => 'counteragentserj-field_id',
                                    'name' => 'CounteragentsEr[field_id]',
                                    'data' => [
                                        '1' => 'ИНН',
                                        '2' => 'ОГРН',
                                        '3' => 'Наименование',
                                    ],
                                    'theme' => Select2::THEME_BOOTSTRAP,
                                    'options' => ['placeholder' => 'Поле для поиска'],
                                    'hideSearch' => true,
                                ]) ?>

                            </div>
                            <div class="col-md-4">
                                <?= Html::input('text', 'CounteragentsEr[value]', '', ['id' => 'counteragentserj-value', 'class' => 'form-control', 'placeholder' => 'Введите ИНН, ОГРН или наименование']) ?>

                            </div>
                        </div>
                    </p>
                    <?= Html::a('<i class="fa fa-search" aria-hidden="true"></i> Найти', '#', ['id' => 'btnSearchJur', 'class' => 'btn btn-primary']) ?>

                </div>
                <div class="tab-pane" id="rp" role="tabpanel">
                    <h4 class="card-title">Поиск по реестру физических лиц</h4>
                    <p class="card-text">Выберите поле для поиска, введите искомое значение и нажмите кнопку.</p>
                    <div class="row">
                        <div class="col-md-2 offset-md-3">
                            <?= Select2::widget([
                                'id' => 'counteragentserp-field_id',
                                'name' => 'CounteragentsEr[field_id]',
                                'data' => [
                                    '1' => 'ИНН',
                                    '2' => 'ОГРН',
                                ],
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => ['placeholder' => 'Поле для поиска'],
                                'hideSearch' => true,
                            ]) ?>

                        </div>
                        <div class="col-md-4">
                            <?= Html::input('text', 'CounteragentsEr[value]', '', ['id' => 'counteragentserp-value', 'class' => 'form-control', 'placeholder' => 'Введите ИНН, ОГРН или наименование']) ?>

                        </div>
                    </div>
                    <p></p>
                    <?= Html::a('<i class="fa fa-search" aria-hidden="true"></i> Найти', '#', ['id' => 'btnSearchPhys', 'class' => 'btn btn-primary']) ?>

                </div>
            </div>
        </div>
    </div>
    <div id="search-results"></div>
</div>
<?php
$url = \yii\helpers\Url::to(['/counteragents/render-counteragents-info']);
$ca_type_jur = \common\models\Counteragents::API_CA_TYPE_ЮРЛИЦО;
$ca_type_phys = \common\models\Counteragents::API_CA_TYPE_ФИЗЛИЦО;
$this->registerJs(<<<JS
// Функция-обработчик щелчка по кнопке Найти юрлицо.
//
function btnSearchJurOnClick() {
    field_id = $("#counteragentserj-field_id").val();
    value = $("#counteragentserj-value").val();
    if (field_id != "" && value != "") {
        $("#search-results").html("<p class=\"text-primary text-center\"><i class=\"fa fa-spinner fa-pulse fa-fw fa-2x\"></i></p>");
        $("#search-results").load("$url?type_id=$ca_type_jur&field_id=" + field_id + "&value=" + value);        
    }
} // btnSearchJurOnClick()

// Функция-обработчик щелчка по кнопке Найти физлицо.
//
function btnSearchPhysOnClick() {
    field_id = $("#counteragentserp-field_id").val();
    value = $("#counteragentserp-value").val();
    if (field_id != "" && value != "") {
        $("#search-results").html("<p class=\"text-primary text-center\"><i class=\"fa fa-spinner fa-pulse fa-fw fa-2x\"></i></p>");
        $("#search-results").load("$url?type_id=$ca_type_phys&field_id=" + field_id + "&value=" + value);        
    }
} // btnSearchPhysOnClick()

$(document).on("click", "#btnSearchJur", btnSearchJurOnClick);
$(document).on("click", "#btnSearchPhys", btnSearchPhysOnClick);
JS
, \yii\web\View::POS_READY);
?>
