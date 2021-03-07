<?php

namespace common\models\room;
use common\models\BaseModel;

/**
 * This is the model class for table "{{%room_floor}}".
 *
 * @property int $floor_id 楼层ID
 * @property int $floor_number 楼层
 */
class RoomFloor extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%room_floor}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['floor_number'], 'integer'],
            [['floor_number'],'unique','on'=>'Reg'],
            [['floor_number'],'unique','on'=>'Edit'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'floor_id' => '楼层ID',
            'floor_number' => '楼层',
        ];
    }

    /*
        获取所有房间楼层
    */
    public static function getRoomFloorAll()
    {
        return self::find()->select(['floor_id','floor_number'])->indexBy('floor_id')->asArray()->all();
    }

    /*
        通过id获取房间楼层
    */
    public static function getRoomFloorById($floorId,$field=['*'])
    {
        if(!$floorId) return false;
        return self::find()->select($field)->where(['floor_id' => $floorId])->asArray()->one();
    }
}
