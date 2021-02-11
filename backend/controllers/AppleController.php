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
                        'actions' => [
                            'index',
                            'create-apples',
                            'ajax-create-apple',
                            'ajax-fall-apple',
                            'ajax-eat-apple'
                        ],
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
        foreach ($apples as $key => $apple) {
            if ($apple->clean()) {
                continue;
            }
            $apple->checkSpoiled();

            $formattedApples[$key]['id']                = $apple->id;
            $formattedApples[$key]['color']             = $apple->getColor();
            $formattedApples[$key]['dateOfAppearance']  = $apple->
                                                          getDateOfAppearance();
            $formattedApples[$key]['dateOfFall']        = $apple->
                                                          getDateOfFall();
            $formattedApples[$key]['status']            = $apple->getStatus();
            $formattedApples[$key]['status_int']        = $apple->
                                                          getStatusInt();
            $formattedApples[$key]['size']              = $apple->getSize();
        }
        return $this->render('apple_index', [ 'apples' => $formattedApples ]);
    }

    public function actionCreateApples() {
        $max = rand(1,9);
        
        for ($i = 0; $i <= $max; $i++) {
            $apple = new Apple();
            $apple->save();
        }
        $this->redirect(['/apple']);
    }

    public function actionAjaxCreateApple() {
        $data = Yii::$app->request->post();
        $apple = new Apple(trim($data['color']));
        $apple->save();
        $formattedApple['id']               = $apple->id;
        $formattedApple['color']            = $apple->getColor();
        $formattedApple['dateOfFall']       = $apple->getDateOfFall();
        $formattedApple['dateOfAppearance'] = $apple->getDateOfAppearance();
        $formattedApple['status']           = $apple->getStatus();
        $formattedApple['status_int']       = $apple->getStatusInt();
        $formattedApple['size']             = $apple->getSize();

        return json_encode($formattedApple);
    }

    public function actionAjaxFallApple() {
        $data = Yii::$app->request->post();
        $apple = Apple::findOne($data['id']);
        $apple->fallToGround();
        $apple->save();
        $formattedApple['id']           = $apple->id;
        $formattedApple['dateOfFall']   = $apple->getDateOfFall();
        $formattedApple['status']       = $apple->getStatus();
        $formattedApple['status_int']   = $apple->getStatusInt();

        return json_encode($formattedApple);
    }

    public function actionAjaxEatApple() {
        $data = Yii::$app->request->post();
        $apple = Apple::findOne($data['id']);
        $apple->eat($data['percent']);
        $apple->save();
        $formattedApple['id']       = $apple->id;
        $formattedApple['status']   = $apple->getStatus();
        $formattedApple['size']     = $apple->getSize();

        return json_encode($formattedApple);
    }

}
