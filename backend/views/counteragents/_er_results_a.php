<?php
/* @var $this yii\web\View */
/* @var $type_id integer */
/* @var $details array */
?>
<div class="card">
    <div class="card-block">
        <h3 class="card-title">Неоднозначная идентификация</h3>
        <p class="card-text">Контрагент не идентифицирован однозначно. Выберите подходящую запись.</p>
        <table id="table-ambiguous" class="table table-hover">
            <thead>
            <tr>
                <th class="text-center">ОГРН</th>
                <th class="text-center">Регистрация</th>
                <th>Наименование</th>
                <th class="text-center" width="60"><i class="fa fa-bars" aria-hidden="true"></i></th>
            </tr>
            </thead>
            <tbody>
            <?php
                foreach ($details as $record) {
                    $company_id = isset($record['person']['id']) ? $record['person']['id'] : $record['id'];
                    $row_id = $record['id'];
                    $row_ogrn = $record['ogrn'];
                    $row_regdate = date('d.m.Y', strtotime($record['ogrnDate']));
                    $row_name = isset($record['person']['fullName']) ? $record['person']['fullName'] : $record['name'];
                    if (isset($record['closeInfo']['date']))
                        $row_name .= ' <span class="text-muted">Закрыто с ' . date('d.m.Y', strtotime($record['closeInfo']['date'])) . '</span>';
            ?>
                <tr data-id="<?= $company_id ?>" data-pid="<?= $row_id ?>" data-type="<?= $type_id ?>">
                    <td width="130" class="text-center"><?= $row_ogrn ?></td>
                    <td width="110" class="text-center"><?= $row_regdate ?></td>
                    <td><?= $row_name ?></td>
                    <td class="text-center"><?= isset($record['closeInfo']) ? '<i class="fa fa-window-close text-danger" aria-hidden="true" title="Предприятие закрыто"></i>' : '' ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$url = \yii\helpers\Url::to(['/counteragents/render-ambiguous-counteragents-info']);
$this->registerJs(<<<JS
$("#table-ambiguous tbody tr").css("cursor", "pointer");
$("#table-ambiguous tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    var pid = $(this).closest("tr").data("pid");
    var type = $(this).closest("tr").data("type");
    if (e.target == this && id && type) {
        $("#search-results").html("<p class=\"text-primary text-center\"><i class=\"fa fa-spinner fa-pulse fa-fw fa-2x\"></i></p>");
        $("#search-results").load("$url?type_id=" + type + "&id=" + id + "&pid=" + pid);
    }
});
JS
, \yii\web\View::POS_READY);
?>
