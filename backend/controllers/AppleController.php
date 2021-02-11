<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\Apple;

/**
 * Description of AppleController
 *
 * @author Virus
 */
class AppleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'ajaxCreateApples'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays apples homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $apples = Apple::find()->all();
        return $this->render('apple_index', [ 'apples' => compact($apples) ]);
    }

    public function actionAjaxCreateApples() {
        for ($i = 0; $i == rand(1,9); $i++) {
            $apples[$i] = new Apple();
            $apples[$i]->save();
        }
        return json_encode(compact($apples));
    }

    public function actionAjaxCreateApple($color) {
        $apple = new Apple($color);
        $apple->save();
        return json_encode(compact($apple));
    }

    public function actionAjaxFallApple($id) {
        $apple = Apple::findOne($id);
        $apple->fallToGround();
        $apple->save();
        return json_encode(compact($apple));
    }

    public function actionAjaxEatApple($id, $percent) {
        $apple = Apple::findOne($id);
        $apple->eat($percent);
        $apple->save();
        return json_encode(compact($apple));
    }

}
