<?php

namespace app\controllers;
use app\models\Rooms;
use app\models\Users;
use yii\web\UploadedFile;

use Yii;

class RoomsController extends RequestController
{
    public $modelClass = 'app\models\Rooms';

    public function actionCreate()
    {
        $user = Users::getToken();
        $post_data=Yii::$app->request->post();

        if (!($user && $user->isAuthorized() && $user->isAdmin())) {
            return $this->send(403, ['error' => ['message' => 'Forbidden']]);
        }

        $post_data=Yii::$app->request->post();
        $room=new Rooms();
        $room->load($post_data, '');
        $room->photo_room=UploadedFile::getInstanceByName('photo_room');
    
        if ($room->photo_room) { 
            $hash=hash('sha256', $room->photo_room->baseName) . '.' . $room -> photo_room->extension;
            $room->photo_room->saveAs(\Yii::$app->basePath. '/api/assets/upload/' . $hash);
            $room->photo_room=$hash;
        } else {
            return $this->send(400, ['error' => ['message' => 'Файл не загружен или данная фотография уже имеется на сервере']]);
        }   

        if (!$room->validate()) {
            return $this->validation($room);
        }

        $room->save(false);
        
        return $this->send(200,[
            'data' => [
                'status' => 'ok',
                'id' => $room->id_room,

            ],
        ]);
    }

    public function actionView() {
        $rooms = Rooms::find()->all();
            
        if (empty($rooms)) {
            return $this->send(404, [
                'error' => [
                    'code' => 404,
                    'message' => 'Room not found'
                ]
             ]);
        }

        $roomList = [];
        
        foreach ($rooms as $room) {
            $roomList[] = [
                'id' => $room->id_room,
                'number' => $room->number,
                'classesRooms' => $room->classesRooms->name,
                'photo_room' => $room->photo_room, 
                'description' => $room->description,
                'price' => $room->price,
                'date_added' => $room->date_added,
            ];
        }
        
        return $this->send(200, [
            'data' => [
                'rooms' => $roomList
            ]
        ]);
    }

    public function actionSearch()
    {
        $query = Yii::$app->request->get('classesRooms');
        $rooms = Rooms::find()->joinWith('classesRooms')->where(['like', 'classes_rooms.name', $query])->all() ;
        
        if (empty($rooms)) {
            return $this->send(404, [
                'error' => [
                    'code' => 404,
                    'message' => 'Room not found'
                ]
            ]);
        }

        $result = [];

        foreach ($rooms as $room) {
            $result[] = [
                'id' => $room->id_room,
                'number' => $room->number,
                'classesRooms' => $room->classesRooms->name,
                'photo_room' => $room->photo_room, 
                'description' => $room->description,
                'price' => $room->price,
                'date_added' => $room->date_added,
            ];
        }

        return $this->send(200, [
            'data' => [
                'rooms' => $result
            ]
        ]);
    }

    public function actionEdit()
    {
        $id_room = Yii::$app->request->get('id_room');
        $user = Users::getToken();
        
        if (!($user && $user->isAuthorized() && $user->isAdmin())) {
            return $this->send(403, ['error' => ['message' => 'Forbidden']]);
        }
        
        $data = Yii::$app->request->post();
        $room = Rooms::findOne($id_room);
        
        if (!$room) {
            return $this->send(404, [
                'error' => [
                    'code' => 404,
                    'message' => 'Room not found'
                ]
            ]);
        }

        if (isset($data['number'])) {
            $room->number = $data['number'];
        }
                
        if (isset($data['class_id'])) {
            $room->type_id = $data['class_id'];
        }
            
        if (isset($data['price'])) {
            $room->price = $data['price'];
        }
            
        if (isset($data['description'])) {
            $room->description = $data['description'];
        }

        if ($room->validate() && $room->save()) {
            return $this->send(200, [
                'data' => [
                    'status' => 'ok'
                ]
            ]);
        }

        return $this->validation($room);
    }
}
