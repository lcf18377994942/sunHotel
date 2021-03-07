<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>房间管理</el-breadcrumb-item>
                <el-breadcrumb-item>房间列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

        <div class="container">
            <el-tabs v-model="roomName" type="card">
                <el-tab-pane label="房间添加" name="add">
                    <roomAdd v-if="roomName=='add'"></roomAdd>
                </el-tab-pane>
                <el-tab-pane label="配置添加" name="basics">
                    <roomBasics v-if="roomName=='basics'"></roomBasics>
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script type="text/javascript">
import roomAdd from '@/page/room/room_add';
import roomBasics from '@/page/room/basics_add';
export default{
    components:{roomAdd,roomBasics},
    data() {
        return {
            roomName: 'add',
        }
    },
    created() {
    },
    methods: {
    }
}
</script>

<style>
</style>
