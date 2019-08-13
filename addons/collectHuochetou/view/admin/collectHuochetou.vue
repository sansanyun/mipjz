<style>
	
</style>
<template>
<div>
    <header class="mipcms-container-header clearfix">
        <div class="header-group">
            <h4 class="title">火车头插件</h4>
        </div>
    </header>
    <main class="mipcms-container-body" style="height: calc(100% - 50px)">
          <section class="mip-box">
        <section class="mip-box-body" style="min-height: 500px;">
            <h1 class="text-center mt-3">欢迎使用MIPCMS火车头采集插件</h1>
            <p class="text-center mt-3 mb-3">在使用过程中如遇见问题 请加qq群：576199348</p>
            <ul class="list-group">
              <li class="list-group-item">免登录直接发布接口：您的域名/collect/ApiUserHuochetou/articleAdd </li>
              <li class="list-group-item">免登录定时发布接口：您的域名/collect/ApiUserHuochetou/articleAddTime </li>
              <li class="list-group-item">神箭手直接发布接口：您的域名/collect/ApiUserShenjianshou/articleAdd</li>
              <li class="list-group-item">神箭手定时发布接口：您的域名/collect/ApiUserShenjianshou/articleAddTime</li>
              <li class="list-group-item">传输方式：POST</li>
              <li class="list-group-item list-group-item-light">字段 <span class="badge badge-secondary">password</span> 插件密码<span class="text-danger">（必选）</span><input type="text" style="width: 200px;display: inline-block;" name="keywords" id="keywords" class="form-control form-control-sm" v-model='keywords' /> <button class="btn btn-primary btn-sm" @click='setKeywords'>保存</button></li>
              <li class="list-group-item list-group-item-light">字段 <span class="badge badge-secondary">title</span> 文章标题<span class="text-danger">（必选）</span></li>
              <li class="list-group-item list-group-item-light">字段 <span class="badge badge-secondary">content</span> 文章内容<span class="text-danger">（必选）</span></li>
              <li class="list-group-item list-group-item-light">字段 <span class="badge badge-secondary">cid</span> 分类序号（可选）如果不填写，默认无分类</li>
              <li class="list-group-item list-group-item-light">字段 <span class="badge badge-secondary">uid</span> 用户序号（可选）</li>
              <li class="list-group-item list-group-item-light">字段 <span class="badge badge-secondary">tags</span> 文章标签（可选）如果填写了，请用英文逗号隔开 例如：seo,sem,营销,优化</li>
              <li class="list-group-item list-group-item-light">字段 <span class="badge badge-secondary">publish_time</span> 发布时间（可选）如果不填写，默认当前时间</li>
            </ul>
        </section>
    </section>
    </main>
      
</div>
</template>

<script>
    export default {
     data () {
       return {
            keywords: '',
       }
     },
     watch: {
            
        },
        mounted() {
             
            this.getKeywords();
        },
        methods: {
            getKeywords() {
                this.$mip.ajax('{$domain}/collectHuochetou/ApiAdminHuochetou/getKeywords', {
                }).then(res => {
                    if(res.code == 1) {
                        this.keywords = res.data;
                    }
                });
            },
            setKeywords() {
                this.$mip.ajax('{$domain}/collectHuochetou/ApiAdminHuochetou/setKeywords', {
                    keywords: this.keywords
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success(res.msg);
                        this.getKeywords();
                    }
                });
            },
        }
    }
</script>
