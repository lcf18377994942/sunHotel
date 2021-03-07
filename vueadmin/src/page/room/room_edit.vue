<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>房间管理</el-breadcrumb-item>
				<el-breadcrumb-item><i class="el-icon-lx-calendar"></i>房间列表</el-breadcrumb-item>
                <el-breadcrumb-item>房间编辑</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

		<div class="container">
			<el-row>
				<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
					<el-form ref="form" :model="form" label-width="100px">
						<el-form-item label="房间名称">
							<el-input v-model="form.room_name" placeholder="输入房间姓名"></el-input>
						</el-form-item>
						<el-form-item label="房间类型">
                            <el-select v-model="form.type_id" filterable placeholder="选择房间类型">
                                <el-option
                                    v-for="item in options"
                                    :key="item.type_id"
                                    :label="item.type_name"
                                    :value="item.type_id">
                                </el-option>
                            </el-select>
						</el-form-item>
                        <el-form-item label="房间状态">
                            <el-select v-model="form.state_id" filterable placeholder="选择房间状态">
                                <el-option
                                    v-for="item in Soptions"
                                    :key="item.state_id"
                                    :label="item.state_name"
                                    :value="item.state_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="楼层">
                            <el-select v-model="form.floor_id" filterable placeholder="输入楼层">
                                <el-option
                                    v-for="item in Foptions"
                                    :key="item.floor_id"
                                    :label="item.floor_number"
                                    :value="item.floor_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="备注">
                            <el-input v-model="form.mark" placeholder="默认为空"></el-input>
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

                room_id:'',

				form:{
                    room_name: '',
                    type_id: '',
                    state_id: '',
                    floor_id: '',
                    mark: '',
                },
                options:[],
                Soptions:[],
                Foptions:[],
			}
		},
		created() {
            this.room_id = this.$route.query.room_id;
			this.$nextTick(()=>{
				this.getData();
				this.getListAll();
			})
		},
		methods: {
			getData() {
                this.ifload = true;
                let param = {room_id:this.room_id};
                this.$post_('room/room/room',param,(res) => {
                    console.log(res);
                    if(res.code=='0'){
                        this.form.room_name = res.data.room_name;
                        this.form.type_id = res.data.type_id;
                        this.form.state_id = res.data.state_id;
                        this.form.floor_id = res.data.floor_id;
                        this.form.mark = res.data.mark;
                    }
                })
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
            //保存数据
            saveData() {
                this.ifload = true;
                this.form.room_id = this.room_id;
                // this.form.grade = '1';
                this.$post_('room/room/room_edit',this.form,(res) =>{
                    console.log(res);
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
