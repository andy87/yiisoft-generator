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
```
<?php

namespace console\controllers;

use andy87\yii2\generator\console\controllers\GeneratorController;

<?php

namespace console\controllers;

use andy87\yii2\generator\console\controllers\GeneratorController;

class GenerateController extends GeneratorController
{
    public $config = [
        'model'         => (object) [
            'modelClass'            => '#TableName#',
            'ns'                    => "common\\models\\items\\source",
            'baseClass'             => "common\\models\\core\\BaseModel"
        ],
        'crud'          => (object) [
            'modelClass'            => "common\\models\\items\\source\\#TableName#",
            'viewPath'              => 'backend\views\source\#table-name#',
            'baseControllerClass'   => "backend\\controllers\\core\\BackendController",
            'searchModelClass'      => 'backend\models\search\#TableName#Search',
            'controllerClass'       => 'backend\controllers\source\#TableName#Controller',
        ]
    ];
}
```
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

