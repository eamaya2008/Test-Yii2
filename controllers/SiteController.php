<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\FormAlumnos;
use app\models\Alumnos;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPanel()
    {
        $this->layout='//second';
        return $this->render('cPanel');
    }

    public function actionSbsettings()
    {
        $this->layout='//second';
        return $this->render('sbSettings');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(array('site/panel'));
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionHola($target = 'Mundo')
    {
        return $this->render('hola', ['target' => $target]);
    }

    public function actionCreate()
    {
        $model=new FormAlumnos;
        $msg=null;
        if($model->load(Yii::$app->request->post()))
        {
            if($model->validate())
            {
                $alumno=new Alumnos;
                $alumno->nombre=$model->nombre;
                $alumno->apellidos=$model->apellidos;
                $alumno->clase=$model->clase;
                $alumno->nota_final=$model->nota_final;

                if($alumno->insert())
                {
                    $msg="Enhorabuena registro guardado correctamente";
                    $model->nombre=null;
                    $model->apellidos=null;
                    $model->clase=null;
                    $model->nota_final=null;
                }
                else
                {
                    $msg="Ha ocurrido un error al insertar el registro";
                }
            }
            else
            {
                $model->getErrors();
            }            
        }
        return $this->render("create", ['model'=> $model,'msg'=>$msg]);
    }

}
