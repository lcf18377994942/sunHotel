<template>
    <div class="chart-container">
        <div ref="chart1" style="width: 100%;height: 100%;"></div>
    </div>
</template>
<script>
import * as echarts from 'echarts';
const option = {
    title: {
        text: '数据'
    }
}
export default {
    name: 'HelloWorld',
    data () {
        return {
            orgOptions: {},
        }
    },
    mounted() {
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(this.$refs.chart1);
        // 绘制图表
        myChart.setOption({
            title: {
                text: '某站点用户访问来源',
                subtext: '纯属虚构',
                left: 'center'
            },
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
            },
            series: [
                {
                    name: '访问来源',
                    type: 'pie',
                    radius: '50%',
                    data: [
                        {value: 1048, name: '搜索引擎'},
                        {value: 735, name: '直接访问'},
                        {value: 580, name: '邮件营销'},
                        {value: 484, name: '联盟广告'},
                        {value: 300, name: '视频广告'}
                    ],
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        });
    }
}
</script>

<style scoped>
.chart-container{
    position: relative;
    width: 100%;
    height: calc(95vh - 84px);
}
</style>
