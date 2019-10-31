<?php

use andy87\yii2\generator\console\components\Generator;

use andy87\yii2\generator\console\controllers\GeneratorController;

use andy87\yii2\generator\console\models\generator\File;
use andy87\yii2\generator\console\models\generator\Root;
use andy87\yii2\generator\console\models\generator\Crud;
use andy87\yii2\generator\console\models\generator\Model;

class_alias(Generator::class, 'andy87\yii2\generator\console\components');

class_alias(GeneratorController::class, 'andy87\yii2\generator\console\controllers');

class_alias(File::class, 'andy87\yii2\generator\console\models\generator');
class_alias(Root::class, 'andy87\yii2\generator\console\models\generator');
class_alias(Crud::class, 'andy87\yii2\generator\console\models\generator');
class_alias(Model::class, 'andy87\yii2\generator\console\models\generator');
