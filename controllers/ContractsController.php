<?php

namespace app\controllers;
use app\models\Contracts;
use yii\web\Response;
use app\models\Users;
use app\models\Reservations;

use Yii;

class ContractsController extends RequestController
{
    public $modelClass = 'app\models\Contracts';

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $data = Yii::$app->request->post(); 
            $reservationItems = Reservations::findAll(['user_id' => $user->id_user, 'contract_id' => null]);
        
            if (empty($reservationItems)) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Забронированных номеров нет'
                    ]
                ]);
            }
            
            $contract = new Contracts();
            $contract->load($data, '');

            if (!$contract->validate()) {
                return $this->validation($contract); 
            }

            $contract->save();

            if (!$contract->save()) {
                return $this->send(500, $contract->getErrors()); 
            }

            $reservationItems = Reservations::findAll(['user_id' => $user->id_user, 'contract_id' => null]);

            foreach ($reservationItems as $reservationItem) {
                $reservationItem->contract_id = $contract->id_contract;
                $reservationItem->save();
            }

            return $this->send(200, [
                'data' => [
                    'status' => 'ok'
                ]
            ]);   
        } else {
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]);
        }
    }   

    public function actionView()
    {
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $reservationItems = Reservations::find()->where(['user_id' => $user->id_user])->andWhere(['not', ['contract_id' => null]])->all();
            
            if (empty($reservationItems)) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Договоры не заключались'
                    ]
                ]);
            }

            $contractIds = array_column($reservationItems, 'contract_id');
            $contracts = Contracts::find()->where(['id_contract' => $contractIds])->all();

            if (empty($contracts)) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Contract not found'
                    ]
                ]);
            }

            $ContractData = [];

            foreach ($contracts as $contract) {
                $ContractData[] = [
                    'id' => $contract->id_contract,
                    'phone' => $contract->phone,
                    'date' => $contract->date_checkin,
                    'comments'=>$contract->comments,
                ];
            }

            return $this->send(200, [
                'data' => [
                    'contracts' => $ContractData
                ]
            ]);

        } else {
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]);
        }
    }
}