<?php

namespace andy87\yii2\generator\console\controllers;

use Yii;
use yii\gii\CodeFile;
use yii\console\Controller;

use andy87\yii2\generator\console\components\Generator;
use andy87\yii2\generator\console\models\generator\Crud;
use andy87\yii2\generator\console\models\generator\Model;
use andy87\yii2\generator\console\models\generator\Root;

/**
 * Class GeneratorController - TEST
 *
 *      Controller
 *
 * задача:
 *      сгенерировать через консоль Model и CRUD по всем таблицам BD с фильтрацией с кастомизацией
 *
 * Возможности:
 *      1. генерация на стандартном конфиге ( по умолчанию )
 *      2. кастомизация настроек генерации через свойство: $this->config
 *      3. генерация модели с собственным кастомным шаблоном : @actionImitation()
 *
 * Настройки генерации:
 *      Настройки устанавливаются в свойствах контроллера ( $this->config & $this->default ),
 *      т.к. в консоли передавать такой объём данных не удобно.
 *      Так же никто не запрещает создать дополнительный `action` со своими настройками
 *      для генерации индивидуальных моделей и крудов. Пример : @actionCustomModel() & @actionCustomCrud()
 *      ибо создать контроллер унаследованный от GeneratorController
 *
 * @package console\controllers
 */
class GeneratorController extends Controller
{
    const ITEM_MODEL    = 'model';
    const ITEM_CRUD     = 'crud';
    /**
     *  Кастомизация настроек генерации.
     *
     *  Если настройка будет false то значение будет браться из свойства default ( $this->default ).
     *
     *  Можно переопределять значения настроек $this->config & $this->default через метод @init()
     *
     *  В значении можно вставить метку #table# - которая в последствии будет заменена
     *  на имя класа сгенерируемое стандартными средстави Yii2 на основе имени таблицы
     *  ( Например для таблицы: custom_user, будет сгенерировано имя: CustomUser )
     *
     * @var array
     */
    public $config      = [];

    /**
     *  Стандартные настройки генерации ( задаются в init() )
     *
     * @var array
     */
    public $default     = [
        'model'             => [
            'modelClass'            => '#TableName#',
            'ns'                    => "app\\models",
            'baseClass'             => "yii\\base\\Model"
        ],
        'crud'              => [
            'modelClass'            => "app\\models\\#TableName#",
            'viewPath'              => "backend\\views\\#table-name#",
            'baseControllerClass'   => "yii\\web\\Controller",
            'searchModelClass'      => "#TableName#Search",
            'controllerClass'       => "#TableName#Controller",
        ]
    ];

    /**
     *  Фильтр со списком таблиц не учавствующих в генерации
     *
     * @var array
     */
    public $filter      = [ 'migration' ];

    /**
     * Void
     */
    public function init ()
    {
        parent::init();

        foreach ( $this->default as $key => $arr )
        {
            $this->default[$key] = (object) $arr;
        }

        if ( count( $this->config ) )
        {
            foreach ( $this->config as $key => $arr )
            {
                $this->config[$key] = (object) $arr;
            }
        }
    }

    /**
     *  Генерация Model
     *
     * @param string $items         table_name|all|Foo,Bar,Core,Item
     */
    public function actionModel( $items = 'all' )
    {
        $items  = $this->getList( $items );

        $params = [
            'modelClass'        => $this->property( 'modelClass',   self::ITEM_MODEL ),
            'ns'                => $this->property( 'ns',           self::ITEM_MODEL ),
            'baseClass'         => $this->property( 'baseClass',    self::ITEM_MODEL ),
        ];

        $result = $this->common( 'Model', $items, $params, $this->filter );

        $this->resp( $result );
    }

    /**
     *  Генерация CRUD
     *
     * @param string $items         table_name|all|Foo,Bar,Core,Item
     */
    public function actionCrud( $items = 'all' )
    {
        $items  = $this->getList( $items );

        $params = [
            'modelClass'            => $this->property( 'modelClass',           self::ITEM_CRUD ),
            'viewPath'              => $this->property( 'viewPath',             self::ITEM_CRUD ),
            'baseControllerClass'   => $this->property( 'baseControllerClass',  self::ITEM_CRUD ),
            'searchModelClass'      => $this->property( 'searchModelClass',     self::ITEM_CRUD ),
            'controllerClass'       => $this->property( 'controllerClass',      self::ITEM_CRUD ),
        ];

        $result = $this->common( 'Crud', $items, $params, $this->filter );

        $this->resp( $result );
    }

    /**
     *  Общий код цикличного создания файлов
     *
     *  Используется в:
     *      \console\controllers\GeneratorController
     *          @actionModel()
     *          @actionCrud()
     *
     * @param string $model Model|Crud
     * @param array $items
     * @param array $params
     * @param array $filter
     *
     * @return array
     */
    public function common( $model, $items, $params = [], $filter = [] )
    {
        $resp   = [];

        $generator  = "andy87\\yii2\\generator\\console\\models\\generator\\{$model}";

        foreach ( $items as $index => $tableName )
        {
            $data = (object) array_merge([], $params );

            if ( $model == 'Model' ) $data->tableName = $tableName;

            if ( in_array( $tableName, $filter ) ) continue;

            /**
             * @var Model|Crud $generator
             */
            $generator  = new $generator();

            $resp[ $tableName ] = $generator->create( $tableName, $data );
        }

       return $resp;
    }

    /**
     *  Генерация списка контроллеров
     *
     * задача:
     *      Сгенерировать через консоль контроллеры по списку с возможностью кастомиации настроек
     *
     * @param string $items         table_name|all|Foo,Bar,Core,Item
     * @param string $ns            frontend\\controllers
     * @param string $baseClass     yii\\base\\Controller
     * @param string $template      custom
     */
    public function actionController(
        $items      = '',
        $ns         = 'frontend\\controllers',
        $baseClass  = 'yii\\base\\Controller',
        $template   = 'default'
    )
    {
        $data   = [
            'modelClass'        => Generator::TableNameCamelCase,
            'ns'                => $ns,
            'baseClass'         => $baseClass,
        ];

        $items  = $this->getList( $items );

        $result = $this->createTemplate( $items, $data, $template );

        $this->resp( $result );
    }

    /**
     *  Генерация модели с собственным шаблоном
     *
     * задача:
     *      Сгенерировать через консоль модель с собственным шаблоном и настройками
     *
     * @param string $items         table_name|all|Foo,Bar,Core,Item
     * @param string $ns            app\\models\\items\\source
     * @param string $baseClass     app\\models\\#table_name#\\Common
     * @param string $template      small
     */
    public function actionImitation(
        $items      = 'all',
        $ns         = "app\\models",
        $baseClass  = "yii\\base\\Model",
        $template   = 'item'
    )
    {
        $result = null;

        $data   = [
            'modelClass'        => Generator::TableNameCamelCase,
            'ns'                => $ns,
            'baseClass'         => $baseClass,
        ];

        $result = $this->createTemplate( $items, $data, $template );

        $this->resp( $result );
    }

    /**
     *  Генерация одной Model с уникальными настройками
     *
     * задача:
     *      Сгенерировать через консоль модель с собственными настройками
     *
     * @return bool
     */
    public function actionCustomModel()
    {
        return Generator::generateModel(
            'User',
            "backend\\models\\self",
            "_CamelCase_",
            "backend\\models\\Core"
        );
    }

    /**
     *  Генерация одного CRUD с уникальными настройками
     *
     * задача:
     *      Сгенерировать через консоль CRUD с собственными настройками
     *
     * @return array
     */
    public function actionCustomCrud()
    {
        return Generator::generateCrud(
            'User',
            "backend\\models\\self\\CustomUser",
            "backend\\views\\_KebabCase_",
            "yii\\web\\Controller",
            "_CamelCase_Search",
            "_CamelCase_Controller"
        );
    }

    /**
     *  Цикл создания файлов по шаблону
     *
     *  Используется в:
     *      @actionImitation()
     *      @actionController()
     *
     * @param string $items         table_name|all|Foo,Bar,Core,Item
     * @param array $data
     * @param string $template
     *
     * @return array
     */
    public function createTemplate( $items, $data, $template = 'item' )
    {
        $result = [];

        $model  = new Model();

        $items  = $this->getList( $items );

        foreach ( $items as $tableName )
        {
            $params = (object) array_merge( [], $data );

            $params = Generator::insertTableCase( (object) $params, $tableName );

            $params->content = $this->classTemplate( $params, $template );

            $path   = Yii::getAlias('@' . str_replace(['\\\\','\\'],'/', $params->ns ) . '\#TableName#.php' );

            $file   = new CodeFile( $path, $params->content );

            $file   = [ Generator::insertTableCase( $file, $tableName ) ];

            $result[ $tableName ] = $model->generate( $file );
        }

        return $result;
    }

    /**
     * @param string $items
     * @param object $data
     * @param string $template
     *
     * @return array
     */
    public function createCustomModel( $items, $data, $template )
    {
        $result = [];

        $model  = new Model();

        $items  = $this->getList( $items );

        foreach ( $items as $tableName )
        {
            $params = Generator::insertTableCase( $data, $tableName );

            $params->content = $this->classTemplateCore( $params, $template );

            $path   = Yii::getAlias('@' . str_replace('\\\\','/', $params->ns ) . "\\{$tableName}.php" );

            $file   = new CodeFile( $path, $params->content );

            $file   = [ Generator::insertTableCase( $file, $tableName ) ];

            $result[ $tableName ] = $model->generate( $file );
        }

        return $result;
    }

    public function classTemplateCore( $data, $path )
    {
        $resp = false;

        if ( file_exists($path) )
        {
            if ( is_object($data) ) $data = (array) $data;

            $resp = Yii::$app->view->renderFile( $path, $data );
        }

        return $resp;
    }

    /**
     *  Шаблон файла для генерации Models и Controllers.
     *      Находится тут потому что все настройки генерации должны быть в контроллере.
     *      Если надо будет изменить шаблон генерации : изменяется в этом месте.
     *
     *  Возможности:
     *      Через агумент @template выбирается шаблон для генерации.
     *      Легко добавляется собственный шаблон.
     *
     *  Используется в:
     *      @createTemplate()
     *
     * @param object $data
     * @param string $template
     *
     * @return string
     */
    public function classTemplate( $data, $template = 'default' )
    {
        switch($template)
        {
            case 'item':
                $tpl = <<<PHP
<?php

namespace {$data->ns};

use common\models\source\\{$data->modelClass} as Source;

class {$data->modelClass} extends Source
{

}
PHP;
            break;

            case 'small':
                $tpl = <<<PHP
<?php

namespace {$data->ns};

class {$data->modelClass} extends {$data->baseClass}
{

}
PHP;
            break;


            case 'default':
                $tpl = <<<PHP
<?php

namespace {$data->ns};

use Yii;

/**
 * {$data->modelClass} form
 * @property bool \$value
 */
class {$data->modelClass} extends {$data->baseClass}
{
    public \$value = false;
    /**
     * @param array \$a
     * @return string
     */
    public function foo( \$a = [] )
    {
        return 'ping';
    }

    /**
     * @param int \$b
     * @return bool
     */
    public static function bar( \$b = 0)
    {
        return false;
    }
}
PHP;
                break;

            default:
                $tpl = "<?php";
        }

        $tpl = str_replace('\\\\', '\\', $tpl );

        return $tpl;

    }

    /**
     *  Получение списка для генерации
     *  Варианты предоставления списка:
     *      - один элемент
     *      - перечисление элементов
     *      - все элементы из таблицы.
     *
     *  Возможности:
     *      позволяет через значение @all получить список всех таблиц из @BD
     *
     *  Используется в:
     *      @actionModel()
     *      @actionCrud()
     *      @actionImitation()
     *      @actionController()
     *      @createTemplate()
     *
     * @param string $value     table_name|all|Foo,Bar,Core,Item
     * @return array
     */
    public function getList( $value = 'all' )
    {
        $tables = ( $value == 'all' )  ? Root::getAllTables() : false;

        $names  = ( !$tables )
            ? ( ( strpos($value, ',') !== false ) ? explode(',', $value ) : [ $value ] )
            : $tables ;

        return $names;
    }

    /**
     *  Получение настроек.
     *      Определение наличия кастомных настроек с подстановкой стандартных значений
     *      в случае отсутствия кастомных.
     *
     * @param string $key
     * @param string $type
     *
     * @return string
     */
    public function property( $key, $type = self::ITEM_MODEL )
    {
        $config     = (object) $this->config[ $type ];
        $default    = (object) $this->default[ $type ];

        $resp       = ( $config->{$key} AND strlen($config->{$key}) )
            ? $config->{$key}
            : $default->{$key};

        return $resp;
    }

    /**
     *  Ass
     *
     * @param $result
     */
    public function resp( $result )
    {
        foreach ( $result as $tableName => $files )
        {
            echo "\r\n\r\n  Table   `{$tableName}`";

            foreach ( $files as $file )
            {
                echo "\r\n   Path : {$file['path']}\r\n Status - {$file['status']}";
            }
        }
    }


    public function dev( $obj )
    {
        $this->resp($obj);

        exit();
    }
}