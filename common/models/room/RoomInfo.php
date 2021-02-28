<?php

namespace common\models\room;
use common\models\BaseModel;

/**
 * This is the model class for table "{{%room_info}}".
 *
 * @property int $room_id 房间编号
 * @property string $room_name 房间名称
 * @property int $type_id 类型编号
 * @property int $state_id 状态编号
 * @property int $floor_id 楼层编号
 * @property string $mark 备注
 * @property int $created_time 创建时间
 * @property int $updated_time 更新时间
 */
class RoomInfo extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%room_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'state_id', 'floor_id', 'created_time', 'updated_time'], 'integer'],
            [['room_name', 'mark'], 'string', 'max' => 20],
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
            'created_time' => '创建时间',
            'updated_time' => '更新时间',
        ];
    }
}
