<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Days;
use app\models\Forecasts;
use \PhpQuery\PhpQuery;
use yii\web\HttpException;

class SiteController extends Controller
{
    /**
     * @inheritdoc
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
     * @inheritdoc
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
        try {
            $client = new \GuzzleHttp\Client();

            $res = $client->request('GET', "https://yandex.ru/pogoda/saint-petersburg/details");//для примера поменяем город

            if ($res->getStatusCode() == 200) {
                // 200
                // 'application/json; charset=utf8'
                $body = $res->getBody();

                $document = \PhpQuery\PhpQuery::newDocumentHTML($body);
                PhpQuery::use_function(__NAMESPACE__);

                $dayCollection = [];
                $forecastCollection = [];
                $elements = $document->find(".forecast-detailed__day");
                foreach ($elements as $element) {
                    $day = new Days();
                    $pq = pq($element);
                    $day->weekday = $pq->find('.forecast-detailed__weekday')->text(); //'forecast-detailed__weekday'
                    $day->dayMonth = $pq->find('.forecast-detailed__day-number span:first')->text(); //forecast-detailed__day-month
                    $pq->find('.forecast-detailed__day-number span')->remove();
                    $day->dayNumber = $pq->find('.forecast-detailed__day-number')->text(); //'forecast-detailed__day-number'
                    $day->dayYear = date('Y');//в основном;) Алгоритмы проверки граничных условий в примере упускаю, нет времени
                    if ($day2 = Days::find()->where([
                        'dayMonth' => $day->dayMonth,
                        'dayNumber' => $day->dayNumber,
                        'dayYear' => $day->dayYear,
                    ])->one()
                    )
                        $day = $day2;
                    $day->save();
                    $dayCollection[] = $day;
                }

                $forecastElements = $document->find('.weather-table');
                foreach ($forecastElements as $forecastRows) {
                    $forecastRow = pq($forecastRows)->find('.weather-table__row');
                    $forecastDayRack = [];
                    foreach ($forecastRow as $forecastDayPart) {
                        $pq = \PhpQuery\pq($forecastDayPart);

                        $forecast = new Forecasts();
                        $forecast->dayPart = $pq->find('.weather-table__daypart')->text();
                        $forecast->temp = $pq->find('.weather-table__temp')->text();
                        $forecast->condition = $pq->find('.weather-table__body-cell_type_condition .weather-table__value')->text();
                        $forecast->airPressure = $pq->find('.weather-table__body-cell_type_air-pressure .weather-table__value')->text();
                        $forecast->airPressure = $pq->find('.weather-table__body-cell_type_humidity .weather-table__value')->text();
                        $forecast->wind = $pq->find('.weather-table__body-cell_type_wind abbr')->text();
                        $forecast->windSpeed = $pq->find('.wind-speed')->text();
                        $forecastDayRack[$forecast->dayPart] = $forecast;
                    }

                    $forecastCollection[] = $forecastDayRack;
                }

                foreach ($forecastCollection as $index => $item) {
                    foreach ($item as $value) {
                        /** @var Forecasts $value */
                        $value->dayId = $dayCollection[$index]->id;
                        if ($forecast2 = Forecasts::find()->where([
                            'dayId' => $value->dayId,
                            'dayPart' => $value->dayPart,
                        ])->one()
                        ) {
                            $forecast2->setAttributes($value->getAttributes());
                            $forecast2->save();
                        } else
                            $value->save();
                    }
                }
//                'weather-table__row'
//                'weather-table'
//                  'weather-table__body-cell weather-table__body-cell_type_daypart'
//                    'weather-table__daypart'
//                    'weather-table__temp'
//                  'weather-table__body-cell weather-table__body-cell_type_condition'
//                    'weather-table__value'
//                  'weather-table__body-cell weather-table__body-cell_type_air-pressure'
//                    'weather-table__value'
//                  'weather-table__body-cell weather-table__body-cell_type_humidity'
//                    'weather-table__value'
//                  'weather-table__body-cell weather-table__body-cell_type_wind'
//                    'weather-table__value'
//                      <abbr class=" icon-abbr" title="Ветер: северный">С</abbr>
//                      'weather-table__wind'
//                         'wind-speed'


                return $this->render('index', ['dayCollection' => $dayCollection, 'forecastCollection' => $forecastCollection]);
            } else
                throw new HttpException(404, 'Погодные данные не найдены');


        } catch (\Exception $e) {
            throw new HttpException(500, $response ?? null, $e->statusCode ?? null);
        }

    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
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
}
