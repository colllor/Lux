<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "classes_rooms".
 *
 * @property int $id_class
 * @property string $name
 *
 * @property Rooms[] $rooms
 */

class ClassesRooms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'classes_rooms';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id_class' => 'Id Class',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Rooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    
    public function getRooms()
    {
        return $this->hasMany(Rooms::class, ['class_id' => 'id_class']);
    }
}
