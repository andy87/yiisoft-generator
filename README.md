<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Generator</h1>
    Система для генерации Model и CRUD
    <hr>
</p>

composer.json  

```
"require": {
    ...
    "andy87/yiisoft-console-generator" : "1.0.1"
},
...
"repositories": [
    ...,
    {
        "type"                  : "package",
        "package"               : {
            "name"                  : "andy87/yiisoft-console-generator",
            "version"               : "1.0.1",
            "source"                : {
                "type"                  : "git",
                "reference"             : "master",
                "url"                   : "https://github.com/andy87/yiisoft-console-generator"
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

<br>

Примеры консольных команд.  

<br>

для генерации **Model**  

- `php yii generate/model`  
генереция Model для всех таблиц  

- `php yii generate/model user`  
генереция Model для таблицы user  

- `php yii generate/model user,news,blog`  
генереция Model для таблицы user,news,blog  

<br>

для генерации **СRUD**  

- `php yii generate/crud`  
генереция СRUD для всех таблиц  

- `php yii generate/crud user`  
генереция СRUD для таблицы user  

- `php yii generate/crud user,news,blog`  
генереция СRUD для таблицы user,news,blog  

