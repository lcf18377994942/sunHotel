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
                        <el-form-item label="类型修改"></el-form-item>
                        <el-form-item label="原类型">
                            <el-select v-model="form.type_id" clearable filterable @change="setType" placeholder="选择房间类型">
                                <el-option
                                    v-for="item in options"
                                    :key="item.type_id"
                                    :label="item.type_name"
                                    :value="item.type_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="类型名称">
                            <el-input v-model="form.type_name" placeholder="输入房间类型名称"></el-input>
                        </el-form-item>
                        <el-form-item label="类型价格">
                            <el-input v-model="form.price" placeholder="输入类型价格"></el-input>
                        </el-form-item>
                        <el-form-item label="状态修改"></el-form-item>
                        <el-form-item label="原状态">
                            <el-select v-model="form.state_id" @change="setState" clearable filterable placeholder="选择房间状态">
                                <el-option
                                    v-for="item in Soptions"
                                    :key="item.state_id"
                                    :label="item.state_name"
                                    :value="item.state_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
						<el-form-item label="状态名称">
							<el-input v-model="form.state_name" placeholder="输入状态名称"></el-input>
						</el-form-item>
                        <el-form-item label="楼层修改"></el-form-item>
                        <el-form-item label="原楼层">
                            <el-select v-model="form.floor_id" @change="setFloor" clearable filterable placeholder="选择楼层">
                                <el-option
                                    v-for="item in Foptions"
                                    :key="item.floor_id"
                                    :label="item.floor_number"
                                    :value="item.floor_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
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
                    type_id: '',
                    type_name: '',
                    price: '',
                    state_id: '',
                    state_name: '',
                    floor_id: '',
                    floor_number: '',
                },
                options:[],
                Soptions:[],
                Foptions:[],
			}
		},
		created() {
			this.$nextTick(()=>{
				this.getData();
                this.getListAll();
			})
		},
		methods: {
            //初始化信息
			getData() {
				this.ifload = false;
			},
            //获取下拉数据
            getListAll() {
                this.$post_('room/room/get-list-all', {},(res) => {
                    this.options = res.data.type;
                    this.Soptions = res.data.state;
                    this.Foptions = res.data.floor;
                });
            },
            //设置类型
            setType(type_id) {
                let obj = {}
                obj = this.options.find((item) => {
                    return item.type_id === type_id;
                });
                let number = obj.type_name.indexOf("(");
                let number1 = obj.type_name.indexOf("/");
                this.form.type_name = obj.type_name;
                this.form.price = obj.type_name.substring(number+1,number1);
            },
            //设置状态
            setState(state_id) {
                let obj = {}
                obj = this.Soptions.find((item) => {
                    return item.state_id === state_id;
                });
                this.form.state_name = obj.state_name;
            },
            //设置状态
            setFloor(floor_id) {
                let obj = {}
                obj = this.Foptions.find((item) => {
                    return item.floor_id === floor_id;
                });
                this.form.floor_number = obj.floor_number;
            },
            //保存数据
            saveData() {
                this.ifload = true;
                this.$post_('room/room/room_basics_edit',this.form,(res) =>{
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
