<template>
    <el-dialog
        :before-close="handleClose"
        :visible.sync="dialogVisible"
        title="添加房间"
        width="40%">
        <el-form ref="form" :model="form" label-width="100px">
            <el-row :gutter=24>
                <el-col :span="12">
                    <el-form-item label="房间名称">
                        <el-input v-model="form.room_name" placeholder="输入房间姓名"></el-input>
                    </el-form-item>
                    <el-form-item label="房间状态">
                        <el-select style="width: 100%" v-model="form.state_id" filterable placeholder="选择房间状态">
                            <el-option
                                v-for="item in sOptions"
                                :key="item.state_id"
                                :label="item.state_name"
                                :value="item.state_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="押金">
                        <el-input v-model="form.deposit" placeholder="默认：20"></el-input>
                    </el-form-item>
				</el-col>
                <el-col :span="12">
                    <el-form-item label="房间类型">
                        <el-select style="width: 100%" v-model="form.type_id" filterable placeholder="请选择">
                            <el-option
                                v-for="item in options"
                                :key="item.type_id"
                                :label="item.type_name"
                                :value="item.type_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="楼层">
                        <el-select style="width: 100%" v-model="form.floor_id" filterable placeholder="输入楼层">
                            <el-option
                                v-for="item in fOptions"
                                :key="item.floor_id"
                                :label="item.floor_number"
                                :value="item.floor_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="备注">
                        <el-input v-model="form.mark" placeholder="默认为空"></el-input>
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
	export default{
		data() {
			return {
                dialogVisible:false,
                disableSubmit: false,
				form:{
                    room_name: '',
                    type_id: '1',
                    state_id: '1',
                    floor_id: '1',
                    deposit:20,
                    mark: '',
                },
                options:[],
                sOptions:[],
                fOptions:[],
			}
		},
		methods: {
		    //打开执行初始化
            open() {
                this.getListAll();
            },
            //关闭
            handleClose(){
                this.dialogVisible=false
            },
            //表单提交
            handleSubmit() {
                this.saveData();
            },
            //获取下拉数据
            getListAll() {
                this.$post_('room/room/get-list-all', {},(res) => {
                    if (res.code === '0') {
                        this.options = res.data.room_type;
                        this.sOptions = res.data.room_state;
                        this.fOptions = res.data.room_floor;
                        this.dialogVisible=true;
                        this.disableSubmit = false;
                    }
                });
            },
            //保存数据
            saveData() {
                this.disableSubmit = true;
                this.$post_('room/room/room_add',this.form,(res) =>{
                    if(res.code ==='0'){
                        this.$emit('callbackRoom');
                        this.dialogVisible=false;
                    }else{
                        this.disableSubmit = false;
                        this.$message.error(res.msg);
                    }
                })
            },
		}
	}
</script>

<style scoped="scoped">
</style>
