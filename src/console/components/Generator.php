<?php

namespace andy87\yii2\generator\console\components;

use Yii;
use yii\base\Component;

use andy87\yii2\generator\console\models\generator\Root;
use andy87\yii2\generator\console\models\generator\Model;
use andy87\yii2\generator\console\models\generator\Crud;

/**
 * Class Generator
 *
 *      Component
 *
 * @package console\components
 */
class Generator extends Component
{
    const TableNameCamelCase    = '#TableName#';
    const TableNameKebabCase    = '#table-name#';
    const TableNameSnakeCase    = '#table_name#';

    /**
     *  генерация одного элемента Model
     *
     * @param $tableName string ( all || tableName )
     * @param $ns string
     * @param $modelClass string
     * @param $baseClass string
     *
     * @return bool
     */
    public static function generateModel( $tableName, $ns, $modelClass, $baseClass )
    {
        $data   = (object) [
            'ns'                    => $ns,
            'modelClass'            => $modelClass,
            'baseClass'             => $baseClass,
        ];

        $items  = ( $tableName === 'all' ) ? Root::getAllTables() : [ $tableName ];

        return self::common( 'Model', $items, $data );
    }

    /**
     *  генерация одного элемента CRUD
     *
     * @param $tableName string ( all || tableName )
     * @param $modelClass string
     * @param $viewPath string
     * @param $baseControllerClass string
     * @param $searchModelClass string
     * @param $controllerClass string
     *
     * @return array
     */
    public static function generateCrud( $tableName, $modelClass, $viewPath, $baseControllerClass, $searchModelClass, $controllerClass )
    {
        $data   = (object) [
            'modelClass'            => $modelClass,
            'viewPath'              => $viewPath,
            'baseControllerClass'   => $baseControllerClass,
            'searchModelClass'      => $searchModelClass,
            'controllerClass'       => $controllerClass,
        ];

        $items  = ( $tableName === 'all' ) ? Root::getAllTables() : [ $tableName ];

        return self::common( 'Crud', $items, $data );
    }

    /**
     *  Общий код цикличного создания файлов
     *
     *  Используется в:
     *          \console\controllers\GeneratorController
     *              actionModel()
     *              actionCrud()
     *
     * @param string $model
     * @param array $items
     * @param object $data
     * @param array $filter
     *
     * @return array
     */
    public static function common( $model, $items, $data, $filter = [] )
    {
        $resp = [];

        $generator  = "andy87\\yii2\\console\\models\\generator\\{$model}";

        foreach ( $items as $tableName )
        {
            if ( in_array( $tableName, $filter ) ) continue;

            /**
             * @var Model|Crud $generator
             */
            $generator  = new $generator();

            $resp[] = $generator->create( $tableName, $data );
        }

        return $resp;
    }

    /**
     *  Ядро замены меток на название таблицы в выбранном формате ( метка в строке )
     *
     *  Пример:
     *      Заменит /view/#table-name#/post на /view/custom-user/post
     *      Заменит #TableName#Controller на CustomUserController
     *      Заменит #table_name# на custom_user
     *
     * @param object $data      набор данных для генерации файла
     * @param string $name      User|custom__user|HaRd__taBLE__name
     *
     * @return mixed
     */
    public static function insertTableCase( $data, $name )
    {
        $replace = [];

        $replace[ Generator::TableNameCamelCase ] = self::generateCaseCamel( $name );
        $replace[ Generator::TableNameKebabCase ] = self::generateCaseKebab( $name );
        $replace[ Generator::TableNameSnakeCase ] = self::generateCaseSnake( $name );

        $from   = array_keys($replace);
        $to     = array_values($replace);

        foreach( $data as $key => $val )
        {
            $data->{$key} = str_replace( $from, $to, $val );
        }

        return $data;
    }

    /**
     *  генерация в формае Camel Case из имени таблицы
     *
     * @param string $name      User|custom__user|HaRd__taBLE__name
     *
     * @return string
     */
    public static function generateCaseCamel( $name = '' )
    {
        if ( strpos( $name, '_') !== -1 )
        {
            $data   = explode('_', $name);

            $resp   = '';

            foreach( $data as $item )
            {
                if ( !strlen($item) ) continue;

                $resp .= ucfirst($item);
            }

        } else {

            $resp   = ucfirst($name);
        }

        return $resp;
    }

    /**
     *  генерация в формае Kebab Case из имени таблицы
     *
     * @param string $name      User|custom__user|HaRd__taBLE__name
     *
     * @return string
     */
    public static function generateCaseKebab( $name = '' )
    {
        if ( strpos( $name, '_') !== -1 )
        {
            $data   = explode('_', $name);

            foreach( $data as $i => $item )
            {
                if ( !strlen($item) ) continue;

                $data[ $i ] = mb_strtolower($item);
            }

            $resp   = implode('-', $data);

        } else {

            $resp   = mb_strtolower($name);
        }

        return $resp;
    }

    /**
     *  генерация в формае Snake Case из имени таблицы
     *
     * @param string $name      User|custom__user|HaRd__taBLE__name
     *
     * @return string
     */
    public static function generateCaseSnake( $name = '' )
    {
        if ( strpos( $name, '_') !== -1 )
        {
            $data   = explode('_', $name);

            foreach( $data as $i => $item )
            {
                if ( !strlen($item) ) continue;

                $data[ $i ] = mb_strtolower($item);
            }

            $resp   = implode('_', $data);

        } else {

            $resp   = mb_strtolower($name);
        }

        return $resp;
    }
}