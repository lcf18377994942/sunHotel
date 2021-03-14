<?php
namespace common\models\member;
use common\models\room\RoomState;
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
            [['openid', 'member_avatar'], 'string', 'max' => 128],
            [['create_time', 'update_time', 'charge'], 'default', 'value' => 0],
            [['member_name','loginpwd','paypwd','member_mobile'],'required','on'=>'Reg'],
            [['member_mobile'],'unique','on'=>'Reg'],
            [['member_mobile'],'unique','on'=>'Edit'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'member_id' => '会员ID',
            'member_name' => '会员姓名',  //微信开放平台ID unionid
            'member_mobile' => '会员电话',  //微信openID 唯一标识
            'loginpwd' => '登录密码',
            'paypwd' => '支付密码',   // 默认微信昵称
            'auth_key' => '会员令牌',
            'invite_id' => '推荐人',
            'openid' => '微信openid',
            'member_avatar' => '用户头像',
            'state_id' => '用户状态',  //  1正常 0冻结
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
        return self::find()->select($field)->from(['m' => MemberModel::tableName()])->leftJoin(MemberTypeModel::tableName().' mt','mt.member_type_id=m.member_type_id')->where(['member_id' => $memberId])->asArray()->one();
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
    public static function getMemberAll()
    {
        return self::find()->select(['member_id','member_name'])->where(['state_id' => RoomState::getRoomStateId()])->asArray()->all();
    }

    /*
        修改会员状态
    */
    public static function setStateId($id,$stateId)
    {
        self::updateAll(['state_id'=>$stateId],['member_id'=>$id]);
    }
}
