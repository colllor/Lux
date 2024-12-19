<?php

namespace app\controllers;
use app\models\Users;
use app\models\LoginForm;
use Yii;

class UsersController extends RequestController
{
    public $modelClass = 'app\models\Users';

    public function actionCreate()
    {
        $data=Yii::$app->request->post();
        $user=new Users();
        $user->load($data, '');

        if (!$user->validate()) {
            return $this->validation($user);
        }

        $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password);;
        $user->save();

        if (!$user->save()) {
            return $this->send(500, $user->getErrors());
        }

        return $this->send(204, null);
    } 

    public function actionLogin(){
        $data=Yii::$app->request->post();
        $login_data=new LoginForm();
        $login_data->load($data, '');

        if (!$login_data->validate()) {
            return $this->validation($login_data);
        }

        $user=Users::find()->where(['email'=>$login_data->email])->one();

        if (!is_null($user)) {
            if (Yii::$app->getSecurity()->validatePassword($login_data->password, $user->password)) {
                $token = Yii::$app->getSecurity()->generateRandomString();
                $user->token = $token;
                $user->save(false);
                $data = ['data' => ['token' => $token]];

                return $this->send(200, $data);
            }
        }

        return $this->send(401, [
            'error'=>[
                'code'=>401, 
                'message'=>'Unauthorized', 
                'errors'=>[
                    'email'=>'Неверный логин или пароль'
                ]
            ]
        ]);
    }

    public function actionView() {    
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $data = [
                'data' => [
                    'user' => [
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        ]
                    ]
                ];

            return $this->send(200, $data);
        } 

        return $this->send(401, [
            'error' => [
                'code' => 401,
                'message' => 'Unauthorized'
            ]
        ]); 
    }
}
