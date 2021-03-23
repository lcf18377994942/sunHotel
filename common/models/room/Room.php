<?php

namespace common\models\room;
use common\models\BaseModel;

/**
 * This is the model class for table "{{%room}}".
 *
 * @property int $room_id 房间编号
 * @property string $room_name 房间名称
 * @property int $type_id 类型编号
 * @property int $state_id 状态编号
 * @property int $floor_id 楼层编号
 * @property float $deposit 押金
 * @property string $mark 备注
 * @property int $create_time 创建时间
 * @property int $update_time 更新时间
 */
class Room extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%room}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'state_id', 'floor_id', 'create_time', 'update_time'], 'integer'],
            [['deposit'], 'number'],
            [['room_name', 'mark'], 'string', 'max' => 20],
            [['room_name','type_id','state_id','floor_id','deposit'],'required','on'=>'Reg'],
            [['room_name'],'unique','on'=>'Reg'],
            [['room_name','type_id','state_id','floor_id','deposit'],'required','on'=>'Edit'],
            [['room_name'],'unique','on'=>'Edit'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'room_id' => '房间编号',
            'room_name' => '房间名称',
            'type_id' => '类型编号',
            'state_id' => '状态编号',
            'floor_id' => '楼层编号',
            'mark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /*
        通过id获取房间
    */
    public static function getRoomById($roomId,$field=['*'])
    {
        if(!$roomId) return false;
        return self::find()->select($field)
            ->from(['r'=>Room::tableName()])
            ->where(['room_id' => $roomId])
            ->leftJoin(RoomType::tableName().' rt','rt.type_id=r.type_id')
            ->asArray()->one();
    }

    /*
        获取所有未入住下拉房间
    */
    public static function getNullRoom($id = 0)
    {
        return self::find()->select(['room_id','room_name'])
            ->from(['r' => self::tableName()])
            ->leftJoin(RoomState::tableName().' rs','rs.state_id=r.state_id')
            ->where(['r.state_id' => RoomState::getRoomStateId()])
            ->orWhere(['room_id'=>$id])
            ->asArray()->all();
    }

    /*
        修改房间状态
    */
    public static function setStateId($id,$stateId)
    {
        self::updateAll(['state_id'=>$stateId],['room_id'=>$id]);
    }
}
