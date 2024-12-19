<?php

namespace app\controllers;
use app\models\Reservations;
use app\models\Rooms;
use yii\web\Response;
use app\models\Users;

use Yii;

class ReservationsController extends RequestController
{
    public $modelClass = 'app\models\Reservations';

    public function actionNew()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $request = Yii::$app->request->post();
            
            if (empty($request['room_id'])&& empty($request['count_people'])) {
                return $this->send(422, [
                    'error' => [
                        'code' => 422,
                        'message' => 'Validation error',
                        'error' => 'Не может быть пустым'
                    ]
                ]);
            }

            $roomId = $request['room_id'];
            $count_people = $request['count_people'];
            $room = Rooms::findOne($roomId);

            if (!$room) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Room not found'
                    ]
                ]);
            }
    
            $reservation = Reservations::find()->where(['user_id' => $user->id_user, 'room_id' => $roomId])->one();

            if ($reservation === null) {
                $reservation = new Reservations();
                $reservation->user_id = $user->id_user;
                $reservation->room_id = $roomId;
                $reservation->count_people = $count_people;
            } else {

                if ($reservation->contract_id !== null) {
                    $newReservationItem = new Reservations();
                    $newReservationItem->user_id = $user->id_user;
                    $newReservationItem->room_id = $roomId;
                    $newReservationItem->count_people = $count_people;

                    if ($newReservationItem->save()) {
                        return $this->send(200, [
                            'data' => [
                                'status' => 'ok',
                                'id' => $newReservationItem->id_reservation,
                            ]
                        ]);
                    } 
                } else {
                    $reservation->count_people += $count_people;
                }
            }
    
            if ($reservation->save()) {
                return $this->send(200,[
                    'data' => [
                        'status' => 'ok',
                        'id' => $reservation->id_reservation,
                    ],
                ]);
            }  

        } else{   
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]); 
        }
    }  

    public function actionItems()
    {
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $reservationItems = Reservations::find()->where(['user_id' => $user->id_user])->andWhere(['contract_id' => null])->all();

            if (empty($reservationItems)) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Забронированных номеров отеля нет'
                    ]
                ]);
            }

            $reservationData = [];

            foreach ($reservationItems as $item) {
                $room = Rooms::findOne($item->room_id); 

                if ($room) { 
                    $totalPrice = $item->count_people * $room->price; 

                    $reservationData[] = [
                        'number' => $room->number,
                        'count_people' => $item->count_people,
                        'total_price' => $totalPrice,
                    ];
                }
            }

            return [
                'data' => [
                    'reservation' => $reservationData,
                ],
            ];

        } else {
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]); 
        }
    }
    
    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id_reservation = Yii::$app->request->get('id_reservation');
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $reservationItems = Reservations::find()->where(['id_reservation' => $id_reservation, 'user_id' =>$user->id_user])->one();

            if ($reservationItems) {
                if ($reservationItems->delete()) {
                    return $this->send(200,[
                        'data' => [
                            'status' => 'ok'
                    
                        ],
                    ]);
                } 
            } else {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Данный номер не забронирован'
                    ]
                ]); 
            }
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