<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>房间管理</el-breadcrumb-item>
				<el-breadcrumb-item><i class="el-icon-lx-calendar"></i>房间列表</el-breadcrumb-item>
                <el-breadcrumb-item>基础配置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

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

                        <el-form-item label="">
                            <el-button :loading="ifload" type="primary" @click="saveData">保存</el-button>
                        </el-form-item>

					</el-form>
				</el-col>
			</el-row>

		</div>


		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
	import loading from '@/components/utils/loading';
	export default{
		components:{loading},
		data() {
			return {
				ifload:true,
				form:{
                    type_name: '',
                    price: '',
                    state_name: '',
                    floor_number: '',
                },
			}
		},
		created() {
			this.$nextTick(()=>{
				this.getData();
			})
		},
		methods: {
            //初始化信息
			getData() {
				this.ifload = false;
			},

            //保存数据
            saveData() {
                this.ifload = true;
                this.$post_('room/room/room_basics_add',this.form,(res) =>{
                    this.ifload = false;
                    if(res.code=='0'){
                        this.$message.success(res.msg);
                        this.$router.back(-1);
                    }else{
                        this.$message.error(res.msg);
                    }
                })
            }
		}
	}
</script>

<style scoped="scoped">
</style>
