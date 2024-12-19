<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rooms".
 *
 * @property int $id_room
 * @property string $number
 * @property int $class_id
 * @property string $photo_room
 * @property string $description
 * @property int $price
 * @property string $date_added
 *
 * @property Reservations[] $reservations
 * @property ClassesRooms $classesRooms
 */
class Rooms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rooms';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['number', 'class_id', 'photo_room', 'description', 'price'], 'required'],
            [['class_id', 'price'], 'integer'],
            [['description'], 'string'],
            ['number', 'match', 'pattern' => '/^[а-яё\s0-9\s]+$/iu', 'message'=>'Название услуги может состоять только из кириллицы, цифр и пробелов'],
            ['class_id', 'match', 'pattern' => '/^[0-9]+$/iu', 'message'=>'Идентификатор - числовое значение'],
            ['price', 'match', 'pattern' => '/^[\d]+$/iu', 'message'=>'Цена услуги является числовым значением'],
            [['date_added'], 'safe'],
            [['number', 'photo_room'], 'string', 'max' => 250],
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClassesRooms::class, 'targetAttribute' => ['class_id' => 'id_class']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_room' => 'Id Room',
            'number' => 'Number',
            'class_id' => 'Class ID',
            'photo_room' => 'Photo Room',
            'description' => 'Description',
            'price' => 'Price',
            'date_added' => 'Date Added',
        ];
    }

    /**
     * Gets query for [[Reservations]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getReservations()
    {
        return $this->hasMany(Reservations::class, ['room_id' => 'id_room']);
    }

    /**
     * Gets query for [[ClassesRooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClassesRooms()
    {
        return $this->hasOne(ClassesRooms::class, ['id_class' => 'class_id']);
    }
}
