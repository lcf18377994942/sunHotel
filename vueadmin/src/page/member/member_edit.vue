<template>
    <el-dialog
        :before-close="handleClose"
        title="会员入住"
        :visible.sync="dialogVisible"
        width="40%">
        <el-form ref="form" :model="form" label-width="100px">
            <el-row :gutter=24>
                <el-col :span="12">
                    <el-form-item label="会员编号">
                        <el-input v-model="form.member_id" :disabled="true" placeholder="输入会员姓名"></el-input>
                    </el-form-item>
                    <el-form-item label="会员姓名">
                        <el-input v-model="form.member_name" placeholder="输入会员姓名"></el-input>
                    </el-form-item>
                    <el-form-item label="会员类型">
                        <el-select style="width: 100%" v-model="form.member_type_id" filterable placeholder="请选择会员类型">
                            <el-option
                                v-for="item in options"
                                :key="item.member_type_id"
                                :label="item.member_type_name"
                                :value="item.member_type_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="联系电话">
                        <el-input v-model="form.member_mobile" placeholder="输入联系电话"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="身份证号">
                        <el-input v-model="form.member_card_id" placeholder="必填"></el-input>
                    </el-form-item>
                    <el-form-item label="会员性别">
                        <el-radio v-model="form.sex" label="1">男</el-radio>
                        <el-radio v-model="form.sex" label="0">女</el-radio>
                    </el-form-item>
                    <el-form-item label="会员状态">
                        <el-select style="width: 100%" v-model="form.state_id" filterable placeholder="请选择会员类型">
                            <el-option
                                v-for="item in sOptions"
                                :key="item.state_id"
                                :label="item.state_name"
                                :value="item.state_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="消费金额">
                        <el-input v-model="form.charge" placeholder="默认：0.00"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">取 消</el-button>
            <el-button type="primary" :disabled="disableSubmit" @click="handleSubmit">确 定</el-button>
        </span>
    </el-dialog>
</template>

<script type="text/javascript">
import loading from '@/components/utils/loading';
export default{
    components:{loading},
    data() {
        return {
            dialogVisible:false,
            disableSubmit: false,//定义确定只能点击发送请求一次
            form:{
                member_id:'',
                member_name: '',
                member_mobile: '',
                sex: '1',
                member_type_id: '1',
                member_card_id: '',
                charge: '',
                state_id: '1',
            },
            options:[],
            sOptions:[]
        }
    },
    methods: {
        open(member_id){
            this.form.member_id = member_id;
            this.getData();
        },
        handleSubmit(){
            this.saveData();
        },
        handleClose(){
            this.dialogVisible=false
        },
        //初始化信息
        getData() {
            this.$post_('member/member/member_info',{member_id:this.form.member_id},(res) => {
                if(res.code==='0'){
                    this.form.member_id = res.data.member_id;
                    this.form.member_name = res.data.member_name;
                    this.form.member_mobile = res.data.member_mobile;
                    this.form.sex = res.data.sex;
                    this.form.member_type_id = res.data.member_type_id;
                    this.form.member_card_id = res.data.member_card_id;
                    this.form.charge = res.data.charge;
                    this.form.state_id = res.data.state_id;
                    this.getListAll();
                }
            })
        },
        //获取下拉数据
        getListAll() {
            this.$post_('member/member/get-member-type-all', {},(res) => {
                if (res.code==='0') {
                    this.options = res.data.member_type;
                    this.sOptions = res.data.member_state;
                    this.dialogVisible = true;
                    this.disableSubmit = false;
                }
            });
        },
        //保存数据
        saveData() {
            this.disableSubmit = true;
            this.$post_('member/member/member_edit',this.form,(res) =>{
                console.log(res);
                this.ifload = false;
                if(res.code==='0'){
                    this.$emit('callbackMember');
                    this.dialogVisible=false;
                    this.disableSubmit = false;
                }else{
                    this.disableSubmit = false;
                    this.$message.error(res.msg);
                }
            })
        }
    }}
</script>

<style scoped="scoped">
</style>
