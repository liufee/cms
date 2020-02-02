<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-10-08 22:17
 */

namespace backend\components\gii\crud;

use Yii;
use ReflectionClass;
use yii\gii\CodeFile;
use yii\helpers\StringHelper;

class Generator extends \yii\gii\generators\crud\Generator
{
    /**
     * @param string $attribute
     * @return string
     */
    public function generateActiveSearchField($attribute)
    {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false) {
            return "\$form->field(\$model, '$attribute', ['labelOptions'=>['class'=>'col-sm-4 control-label'], 'size'=>8, 'options'=>['class'=>'col-sm-3']])";
        }

        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        }

        return "\$form->field(\$model, '$attribute', ['labelOptions'=>['class'=>'col-sm-4 control-label'], 'size'=>8, 'options'=>['class'=>'col-sm-3']])";
    }

    public function formView()
    {
        $class = new ReflectionClass(\yii\gii\generators\crud\Generator::className());

        return dirname($class->getFileName()) . '/form.php';
    }

    public function generate()
    {
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');

        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $modelClass = StringHelper::basename($this->modelClass);

        $files[] = new CodeFile(Yii::getAlias("@common/services/") . $modelClass . 'ServiceInterface.php', $this->render("serviceInterface.php"));

        $files[] = new CodeFile(Yii::getAlias("@common/services/") . $modelClass . 'Service.php', $this->render("service.php"));

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        return $files;
    }

}