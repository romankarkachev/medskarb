<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');

Yii::setAlias('@uploads', '@backend/../uploads');
Yii::setAlias('@uploads-ca-fs', '@backend/../uploads/ca'); // файлы контрагентов, полный путь
Yii::setAlias('@uploads-docs-fs', '@backend/../uploads/documents'); // файлы к документам, полный путь
Yii::setAlias('@uploads-deals-fs', '@backend/../uploads/deals'); // файлы к документам, полный путь
Yii::setAlias('uploads-bs-fs', '@backend/../uploads/bs'); // файлы к банковским движениям, полный путь
Yii::setAlias('uploads-tyc-fs', '@backend/../uploads/tyc'); // файлы к годовым расчетам, полный путь

Yii::setAlias('uploads-ca', '/uploads/ca/'); // файлы контрагентов, относительный путь
Yii::setAlias('uploads-docs', '/uploads/documents/'); // файлы к документам, относительный путь
Yii::setAlias('uploads-deals', '/uploads/deals/'); // файлы к сделкам, относительный путь
Yii::setAlias('uploads-bs', '/uploads/bs/'); // файлы к банковским движениям, относительный путь
Yii::setAlias('uploads-tyc', '/uploads/tyc/'); // файлы к банковским движениям, относительный путь
