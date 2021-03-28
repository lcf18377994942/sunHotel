<?php
namespace common\models\member;
use common\models\room\RoomStateModel;
use Yii;
use common\models\BaseModel;

/**
	* 会员member共用模型
 */

class MemberModel extends BaseModel
{
    public static function tableName()
    {
        return '{{%member}}';
    }

    public function rules()
    {
        return [
            [['create_time', 'update_time', 'state_id'], 'integer'],
            [['member_name'], 'string', 'max' => 64],
            [['member_mobile'], 'string', 'max' => 13],
            [['member_card_id'], 'string', 'max' => 18],
            [['member_avatar'], 'string', 'max' => 128],
            [['create_time', 'update_time', 'charge'], 'default', 'value' => 0],
            [['sex', 'state_id'], 'default', 'value' => 1],
            //正则验证器：
            [['member_card_id'],'match','pattern'=>'/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/'],
            [['member_name','member_card_id'],'required','on'=>'Reg'],
            [['member_card_id'],'unique','on'=>'Reg'],
            [['member_card_id'],'unique','on'=>'Edit'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'member_id' => '会员ID',
            'member_name' => '会员姓名',
            'member_mobile' => '会员电话',
            'member_card_id' => '身份证号',
            'auth_key' => '会员令牌',
            'invite_id' => '推荐人',
            'member_avatar' => '用户头像',
            'state_id' => '用户状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /*
        通过id获取用户
    */
    public static function getMemberById($memberId,$field=['*'])
    {
        if(!$memberId) return false;
        return self::find()->select($field)
            ->from(['m' => MemberModel::tableName()])
            ->leftJoin(MemberTypeModel::tableName().' mt','mt.member_type_id=m.member_type_id')
            ->where(['member_id' => $memberId])
            ->asArray()->one();
    }

    /*
        通过id 或是 电话等信息查询用户
    */
    public static function queryMember($memberKey,$field)
    {
        if(!$memberKey) return false;
            $where = ['or','member_id='.$memberKey,'member_mobile="'.$memberKey.'"'];
        return self::find()->select($field)->where($where)->asarray()->one();
    }

    //生成auth_key
    public static function generateAuthKey()
    {
        return Yii::$app->security->generateRandomString();
    }

    /*
        获取所有未入住会员
    */
    public static function getMemberAll($id = 0)
    {
        return self::find()->select(['member_id','member_name'])
            ->from(['m' => self::tableName()])
            ->leftJoin(RoomStateModel::tableName().' rs','rs.state_id=m.state_id')
            ->where(['m.state_id' => RoomStateModel::getRoomStateId()])
            ->orWhere(['member_id'=>$id])
            ->asArray()->all();
    }

    /*
        获取所有未入住次会员
    */
    public static function getSecondMemberAll($id = 0)
    {
        return self::find()->select(['member_id','member_name'])
            ->from(['m' => self::tableName()])
            ->leftJoin(RoomStateModel::tableName().' rs','rs.state_id=m.state_id')
            ->where(['m.state_id' => RoomStateModel::getRoomStateId()])
            ->andWhere(['<>', 'member_id', $id])
            ->asArray()->all();
    }

    /*
        修改会员状态
    */
    public static function setStateId($id,$stateId,$charge = '')
    {
        $fileds = ['state_id'=>$stateId];
        if (!empty($charge)) {
            $fileds['charge'] = $charge;
        }
        self::updateAll($fileds,['member_id'=>$id]);
    }
}
