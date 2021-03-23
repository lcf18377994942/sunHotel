<template>
    <el-dialog
        :before-close="handleClose"
        :visible.sync="dialogVisible"
        width="30%">

		<div class="container">
			<el-row>
				<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
					<el-form ref="form" :model="form" label-width="100px">
                        <el-form-item label="类型添加"></el-form-item>
                        <el-form-item label="类型名称">
                            <el-input v-model="form.type_name" placeholder="输入房间类型名称"></el-input>
                        </el-form-item>
                        <el-form-item label="类型价格">
                            <el-input v-model="form.price" placeholder="输入类型价格"></el-input>
                        </el-form-item>
                        <el-form-item label="状态添加"></el-form-item>
						<el-form-item label="状态名称">
							<el-input v-model="form.state_name" placeholder="输入状态名称"></el-input>
						</el-form-item>
                        <el-form-item label="楼层添加"></el-form-item>
                        <el-form-item label="楼层">
                            <el-input v-model="form.floor_number" placeholder="输入楼层"></el-input>
                        </el-form-item>

					</el-form>
				</el-col>
			</el-row>

		</div>

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
                disableSubmit:false,
				form:{
                    type_name: '',
                    price: '',
                    state_name: '',
                    floor_number: '',
                },
			}
		},
		methods: {
		    //打开
            open() {
                this.getDate();
            },
            //关闭
            handleClose() {
                this.dialogVisible = false;
            },
            //提交
            handleSubmit() {
                this.saveData();
            },
            //初始化
            getDate() {
                this.form.type_name= '';
                this.form.price= '';
                this.form.state_name= '';
                this.form.floor_number= '';
                this.dialogVisible = true;
                this.disableSubmit = false;
            },
            //保存数据
            saveData() {
                this.disableSubmit = true;
                this.$post_('room/room/room_basics_add',this.form,(res) =>{
                    if(res.code=='0'){
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
