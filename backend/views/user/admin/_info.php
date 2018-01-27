<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>

<?php $this->beginContent('@backend/views/user/admin/update.php', ['user' => $user]) ?>

<div class="card">
    <div class="card-block">
        <table class="table">
            <tr>
                <td><strong><?= Yii::t('user', 'Registration time') ?>:</strong></td>
                <td><?= Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$user->created_at]) ?></td>
            </tr>
            <?php if ($user->registration_ip !== null): ?>
            <tr>
                <td><strong><?= Yii::t('user', 'Registration IP') ?>:</strong></td>
                <td><?= $user->registration_ip ?></td>
            </tr>
            <?php endif ?>
            <?php if ($user->last_login_at !== null): ?>
            <tr>
                <td><strong>Последняя авторизация:</strong></td>
                <td><?= Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$user->last_login_at]) ?></td>
            </tr>
            <?php endif ?>
            <tr>
                <td><strong><?= Yii::t('user', 'Confirmation status') ?>:</strong></td>
                <?php if ($user->isConfirmed): ?>
                    <td class="text-success">
                        <?= Yii::t('user', 'Confirmed at {0, date, MMMM dd, YYYY HH:mm}', [$user->confirmed_at]) ?>
                    </td>
                <?php else: ?>
                    <td class="text-danger">Аккаунт не подтвержден</td>
                <?php endif ?>
            </tr>
            <tr>
                <td><strong><?= Yii::t('user', 'Block status') ?>:</strong></td>
                <?php if ($user->isBlocked): ?>
                <td class="text-danger">
                    <?= Yii::t('user', 'Blocked at {0, date, MMMM dd, YYYY HH:mm}', [$user->blocked_at]) ?>
                </td>
                <?php else: ?>
                <td class="text-success">Пользователь не заблокирован</td>
                <?php endif ?>
            </tr>
        </table>
    </div>
</div>
<?php $this->endContent() ?>
