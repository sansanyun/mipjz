<style>
    
.user-statistical .mip-box-body {
}

.user-statistical .mip-iconfont {
    font-size: 35px;
    position: absolute;
    right: 0;
    bottom: 0px;
    color: #0AE;
    opacity: 0.2;
}

.user-statistical h3 {
    font-size: 20px;
    color: #0AE;
}

.user-statistical h3 .unit {
    display: none;
    color: #999;
    font-size: 12px;
}

.user-statistical p {
    color: #bababa;
    font-size: 14px;
}
.line {
    line-height: 30px;
}
.copyright-info p {
    line-height: 30px;
}
</style>
<template>
    <div>
        
        <main class="mipcms-container-body" style="height: calc(100% - 50px)">
    <div class="row">
            <section class="col-lg-8">
               <div class="mip-box">
                <div class="mip-box-heading"><h3 class="left">系统信息</h3></div>
                    <div class="mip-box-body copyright-info">
                    	<p><b>操作系统：</b><?php echo php_uname('s');?></p>
                    	<p><b>服务环境：</b><?php echo $_SERVER['SERVER_SOFTWARE'];?></p>
                    	<p><b>PHP版本：</b><?php echo PHP_VERSION;?></p>
                    	<hr class="mb-2 mt-2" />
                        <p><b>软件名称：</b>MIPCMS内容管理系统</p>
                        <p><b>当前版本：</b>MIPCMS v5.0.1</p>
                        <p><b>联系方式：</b>QQ85249417</p>
                        <p><b>版权归属：</b>苏州三三云网络科技有限公司</p>
                    </div>
                </div>
            </section>
            <section class="col-lg-4">
                <div class="mip-box">
                    <div class="mip-box-heading"><h3 class="left">官网公告</h3></div>
                    <div class="mip-box-body">
                        <ul class="list-unstyled">
                            <li class="list-time line" v-for="item in gonggaoList" ><a :href="item.url" target="_blank" v-html='item.title'></a></li>
                        </ul>
                    </div>
                </div>
                <div class="mip-box">
                    <div class="mip-box-heading"><h3 class="left">功能介绍</h3></div>
                    <div class="mip-box-body">
                        <ul class="list-unstyled">
                            <li class="list-time line" v-for="item in functionList" ><a :href="item.url" target="_blank" v-html='item.title'></a></li>
                        </ul>
                    </div>
                </div>
            </section>
          </div>
        </main>
    </div>
</template>
<script>
export default {
        data () {
           return {
                usersList: [],
                gonggaoList:[],
                functionList: [],
               
            }
        },
        watch: {
        },
        mounted() {
            this.getGonggaoList();
            this.getFunctionList();
        },
        methods: {
                 
            getGonggaoList:function() {
                this.$mip.ajax('https://www.mipcms.cn/api/open/gonggaoList',{
                },'global').then(res => {
                    if (res.code == 1) {
                       this.gonggaoList = res.data.gonggaoList;
                    }
                });
            },

            getFunctionList:function() {
                this.$mip.ajax('https://www.mipcms.cn/api/open/functionList',{
                },'global').then(res => {
                    if (res.code == 1) {
                        this.functionList = res.data.functionList;
                    }
                });
            }, 
         }
    }
</script>
