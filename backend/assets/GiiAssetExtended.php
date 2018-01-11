<?php

namespace backend\assets;

use yii\gii\GiiAsset as BaseGiiAsset;

/**
 * Расширение класса.
 * @author Roman Karkachev <post@romankarkachev.ru>
 */
class GiiAssetExtended extends BaseGiiAsset
{
    public $sourcePath = '@yii/gii/assets';
    public $css = [
        '/css/bootstrap337/bootstrap.min.css',
        '/css/bootstrap337/bootstrap-theme.min.css',
        'main.css',
    ];
    public $js = [
        'gii.js',
        '/js/bootstrap337/bootstrap.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\gii\TypeAheadAsset',
    ];

    public function init()
    {
        parent::init();
        // resetting BootstrapAsset to not load own css files
        $resetBundle = [
            'css' => [],
            'js' => [],
        ];

        \Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapAsset'] = $resetBundle;
        \Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapPluginAsset'] = $resetBundle;
    }
}
