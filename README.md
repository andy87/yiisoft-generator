<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Generator</h1>
</p>

Система для генерации Model и CRUD из таблиц DB.
***Задача :*** быстро генерировать Model и CRUD из консоли.
<hr>

##### INSTALL
Добавить в `composer.json`
<small>require</small>
```
"require": {
    ...
    "andy87/yiisoft-generator" : "1.0.1"
},
```
<small>repositories</small>
```
"repositories": [
    ...,
    {
        "type"                  : "package",
        "package"               : {
            "name"                  : "andy87/yiisoft-generator",
            "version"               : "1.0.1",
            "source"                : {
                "type"                  : "git",
                "reference"             : "master",
                "url"                   : "https://github.com/andy87/yiisoft-generator"
            },
            "autoload": {
                "psr-4": {
                    "andy87\\yii2\\generator\\console\\components\\": "src/console/components",
                    "andy87\\yii2\\generator\\console\\controllers\\": "src/console/controllers",
                    "andy87\\yii2\\generator\\console\\models\\generator\\": "src/console/models/generator"
                }
            }
        }
    }
]
```

Создать файл `console/controllers/GenerateController.php`
с настройками генерации, к примеру:
```
<?php

namespace console\controllers;

use andy87\yii2\generator\console\controllers\GeneratorController;

class GenerateController extends GeneratorController
{
    public $config = [
        'model'         => [
            'modelClass'            => '#TableName#',
            'ns'                    => "common\\models\\items\\source",
            'baseClass'             => "common\\models\\core\\BaseModel" //Default: yii\db\ActiveRecord
        ],
        'crud'          => [
            'modelClass'            => "common\\models\\items\\source\\#TableName#",
            'viewPath'              => 'backend\views\source\#table-name#',
            'baseControllerClass'   => "backend\\controllers\\core\\BackendController",
            'searchModelClass'      => 'backend\models\search\#TableName#Search',
            'controllerClass'       => 'backend\controllers\source\#TableName#Controller',
        ]
    ];

    // php yii generate/items
    // генерация файлов по шаблону @common/tpl/custom.php
    public function actionItems( $items = 'all' )
    {
        $data   = (object) [
            'ns'                => 'common\\models\\items',
            'modelClass'        => '#TableName#',
            'baseClass'         => 'common\\models\\items\\source\\#TableName#',
        ];

        $this->createCustomModel($items, $data, 'default');
    }
}
```
При использовании родительского класса требуется унаследоваться от ActiveRecord
Создать файл `common/models/core/BaseModel`
с настройками генерации, к примеру:

Принеобходимости генерации кастомных файлов(в примере actionItems ), создать дирректорию
`console/tpl/`
с шаблнами для генерации, для примера потребуется сделать файл  `\console\tpl\default.php`
Пример контента файла:
```
<?php
/**
 * @var string $ns
 * @var string $modelClass
 * @var string $baseClass
 */
?>
<?= "<?php\r\n" ?>

namespace <?=$ns?>;

/**
* Class $modelClass
*
*      Type
*/
class <?=$modelClass?> extends <?=$baseClass?>
{

}
```
Примеров других файлов: `\vendor\andy87\yiisoft-generator\src\tpl\*.php`

<br>

## Примеры консольных команд.
<br>

#### для генерации **Model**

- **`php yii generate/model`**
генереция ***Model*** для всех таблиц

- **`php yii generate/model user`**
генереция ***Model*** для таблицы *`user`*

- **`php yii generate/model user,news,blog`**
генереция ***Model*** для таблицы *`user,news,blog`*

<br>

#### для генерации **СRUD**

- **`php yii generate/crud`**
генереция ***СRUD*** для всех таблиц

- **`php yii generate/crud user`**
генереция ***СRUD*** для таблицы *`user`*

- **`php yii generate/crud user,news,blog`**
генереция ***СRUD*** для таблицы *`user,news,blog`*



# ERRORS

Если вылетает ошибка вида:
```
Exception 'Error' with message 'Call to undefined method common\models\items\source\ModelName::primaryKey()'

in ...\vendor\yiisoft\yii2-gii\src\generators\crud\default\controller.php:22
```
Значит модель `common\models\items\source\ModelName` наследуется не от `ActiveRecord`
