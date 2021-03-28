<template>
    <el-dialog
        :before-close="handleClose"
        :visible.sync="dialogVisible"
        title="修改房间配置"
        width="40%">
        <el-form ref="form" :model="form" label-width="100px">
            <el-row :gutter=24>
                <el-col :span="12">
                    <el-form-item label="原类型">
                        <el-select style="width: 100%" v-model="form.type_id" clearable filterable @change="setType" placeholder="选择房间类型">
                            <el-option
                                v-for="item in options"
                                :key="item.type_id"
                                :label="item.type_name"
                                :value="item.type_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter=24>
                <el-col :span="12">
                    <el-form-item label="类型名称">
                        <el-input v-model="form.type_name" placeholder="输入房间类型名称"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                <el-form-item label="类型价格">
                    <el-input v-model="form.price" placeholder="输入类型价格"></el-input>
                </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter=24 style="margin-top: 50px">
                <el-col :span="12">
                    <el-form-item label="原状态">
                        <el-select style="width: 100%" v-model="form.state_id" @change="setState" clearable filterable placeholder="选择房间状态">
                            <el-option
                                v-for="item in sOptions"
                                :key="item.state_id"
                                :label="item.state_name"
                                :value="item.state_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="状态名称">
                        <el-input v-model="form.state_name" placeholder="输入状态名称"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter=24 style="margin-top: 50px">
                <el-col :span="12">
                    <el-form-item label="原楼层">
                        <el-select style="width: 100%" v-model="form.floor_id" @change="setFloor" clearable filterable placeholder="选择楼层">
                            <el-option
                                v-for="item in fOptions"
                                :key="item.floor_id"
                                :label="item.floor_number"
                                :value="item.floor_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="楼层">
                        <el-input v-model="form.floor_number" placeholder="输入楼层"></el-input>
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
                    type_id: '',
                    type_name: '',
                    price: 0.00,
                    state_id: '',
                    state_name: '',
                    floor_id: '',
                    floor_number: '',
                },
                options:[],
                sOptions:[],
                fOptions:[],
			}
		},
		methods: {
		    //打开
            open() {
                this.getData();
            },
            //关闭
            handleClose() {
                this.dialogVisible=false;
            },
            //提交
            handleSubmit() {
                this.saveData();
            },
            //初始化信息
			getData() {
                this.form.type_id= '';
                this.form.type_name= '';
                this.form.price= '';
                this.form.state_id= '';
                this.form.state_name= '';
                this.form.floor_id= '';
                this.form.floor_number= '';
                this.getListAll();
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
            //设置类型
            setType(type_id) {
                this.form.type_name= '';
                this.form.price= '';
                if (type_id !== '') {
                    let obj = {}
                    obj = this.options.find((item) => {
                        return item.type_id === type_id;
                    });
                    let number = obj.type_name.indexOf("(");
                    let number1 = obj.type_name.indexOf("/");
                    this.form.type_name = obj.type_name;
                    this.form.price = obj.type_name.substring(number+1,number1);
                }
            },
            //设置状态
            setState(state_id) {
                this.form.state_name = '';
                if (state_id !== '') {
                    let obj = {}
                    obj = this.sOptions.find((item) => {
                        return item.state_id === state_id;
                    });
                    this.form.state_name = obj.state_name;
                }
            },
            //设置状态
            setFloor(floor_id) {
                this.form.floor_number = '';
                if (floor_id !== '') {
                    let obj = {}
                    obj = this.fOptions.find((item) => {
                        return item.floor_id === floor_id;
                    });
                    this.form.floor_number = obj.floor_number;
                }
            },
            //保存数据
            saveData() {
                this.disableSubmit = true;
                this.$post_('room/room/room_basics_edit',this.form,(res) =>{
                    if(res.code === '0'){
                        this.$emit('callbackRoom');
                        this.dialogVisible=false;
                    }else{
                        this.disableSubmit = false;
                        this.$message.error(res.msg);
                    }
                })
            }
		}
	}
</script>

<style scoped="scoped">
</style>
