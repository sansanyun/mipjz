<style>
	
</style>
<template>
    <div>
        <header class="mipcms-container-header clearfix">
            <div class="header-group">
                <h4 class="title">设置</h4> <h5 class="sub-title">系统设置 （该设置仅用于单域名模式）</h5>
            </div>
            <div class="float-right">
                <button type="button" class="ivu-btn ivu-btn-primary ivu-btn-circle" @click='onSubmitSetting'>
                    <span>立即保存</span>
                </button>
            </div>
        </header>
        
        
        <main class="mipcms-container-body" style="height: calc(100% - 50px)">
        
        <section class="mip-box">
            <section class="mip-box-heading">
                <h3>MIP推送</h3>
            </section>
            <section class="mip-box-body">
                <i-form label-position="top">
                    <div class="row">
                        <div class="col-md-6">
                            <Form-item label="MIP引入提交接口">
                                <i-input v-model="setting.mipApiAddress" placeholder="请到百度站长平台 移动专区->MIP引入 获取（M域名）"></i-input>
                            </Form-item>
                        </div>
                        <div class="col-md-2">
                             <Form-item label="MIP自动推送">
                                <i-switch v-model="setting.mipPostStatus">
                                    <span slot="open">开</span>
                                    <span slot="close">关</span>
                                </i-switch>
                            </Form-item>
                        </div>
                    </div>
                  
                </i-form>
            </section>
        </section>
        
        <section class="mip-box">
            <section class="mip-box-heading">
                <h3>熊掌号</h3>
            </section>
            <section class="mip-box-body">
                <i-form label-position="top">
                    <div class="row">
                        <div class="col-md-6">
                            <Form-item label="熊掌号ID">
                                <i-input v-model="setting.guanfanghaoCambrian"></i-input>
                            </Form-item>
                            <Form-item label="熊掌号新增接口">
                                <i-input v-model="setting.guanfanghaoRealtimeUrl"></i-input>
                            </Form-item>
                            <Form-item label="熊掌号历史接口">
                                <i-input v-model="setting.guanfanghaoUrl"></i-input>
                            </Form-item>
                        </div>
                        <div class="col-md-2">
                             <Form-item label="熊掌号模式">
                                <i-switch v-model="setting.guanfanghaoStatus">
                                    <span slot="open">开</span>
                                    <span slot="close">关</span>
                                </i-switch>
                            </Form-item>
                            <Form-item label="熊掌号自动推送">
                                <i-switch v-model="setting.guanfanghaoStatusPost">
                                    <span slot="open">开</span>
                                    <span slot="close">关</span>
                                </i-switch>
                            </Form-item>
                        </div>
                    </div>
                </i-form>
            </section>
        </section>
        
        <section class="mip-box">
            <section class="mip-box-heading">
                <h3>原创推送</h3>
            </section>
            <section class="mip-box-body">
                <i-form label-position="top">
                    <div class="row">
                        <div class="col-md-6">
                            <Form-item label="原创提交接口">
                                <i-input v-model="setting.baiduYuanChuangUrl"></i-input>
                            </Form-item>
                        </div>
                        <div class="col-md-2">
                           <Form-item label="原创自动推送">
                                <i-switch v-model="setting.baiduYuanChuangStatus">
                                    <span slot="open">开</span>
                                    <span slot="close">关</span>
                                </i-switch>
                            </Form-item>
                        </div>
                    </div>
                </i-form>
            </section>
        </section>
        
        
        <section class="mip-box">
            <section class="mip-box-heading">
                <h3>链接推送</h3>
            </section>
            <section class="mip-box-body">
                <i-form label-position="top">
                    <div class="row">
                        <div class="col-md-6">
                            <Form-item label="链接提交接口">
                                <i-input v-model="setting.baiduTimePcUrl"></i-input>
                            </Form-item>
                        </div>
                        <div class="col-md-2">
                           <Form-item label="链接自动推送">
                                <i-switch v-model="setting.baiduTimePcStatus">
                                    <span slot="open">开</span>
                                    <span slot="close">关</span>
                                </i-switch>
                            </Form-item>
                        </div>
                    </div>
                </i-form>
            </section>
        </section>
        
        
        <section class="mip-box">
            <section class="mip-box-heading">
                <h3>百度站内搜索</h3>
            </section>
            <section class="mip-box-body">
                <i-form label-position="top">
                    <div class="row">
                        <div class="col-md-6">
                            <Form-item label="百度站内搜索Key">
                                <i-input v-model="setting.biaduZn"></i-input>
                            </Form-item>
                            <Form-item label="百度站内搜索sitemap">
                                <i-input v-model="setting.baiduSearchPcUrl"></i-input>
                            </Form-item>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                </i-form>
            </section>
        </section>
        </main>
    </div>
</template>

<script>
    export default {
     data () {
       return {
            setting: '',
       }
     },
    mounted: function() {
            this.getSettingData();
    },
    methods: {
            onSubmitSetting: function() {
                this.$mip.ajax('{$domain}/setting/ApiAdminSetting/settingEdit', {
                    setting: JSON.stringify(this.setting),
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('保存成功');
                        this.getSettingData();
                    }
                });
            },
            getSettingData: function() {
                this.loading = true;
                this.$mip.ajax('{$domain}/setting/ApiAdminSetting/settingSelect', {

                }).then(res => {
                    this.loading = false;
                    if(res.code == 1) {
                        var temp = res.data;
                        if(temp.systemStatus) {
                            temp.systemStatus = true;
                        } else {
                            temp.systemStatus = false;
                        }
                        if(temp.loginStatus) {
                            temp.loginStatus = true;
                        } else {
                            temp.loginStatus = false;
                        }
                        if(temp.registerStatus) {
                            temp.registerStatus = true;
                        } else {
                            temp.registerStatus = false;
                        }
                        if(temp.idStatus) {
                            temp.idStatus = true;
                        } else {
                            temp.idStatus = false;
                        }
                        if(temp.codeCompression) {
                            temp.codeCompression = true;
                        } else {
                            temp.codeCompression = false;
                        }
                        if(temp.baiduSpider) {
                            temp.baiduSpider = true;
                        } else {
                            temp.baiduSpider = false;
                        }
                        if(temp.articlePages) {
                            temp.articlePages = true;
                        } else {
                            temp.articlePages = false;
                        }
                        if(temp.mipPostStatus) {
                            temp.mipPostStatus = true;
                        } else {
                            temp.mipPostStatus = false;
                        }
                        if(temp.baiduYuanChuangStatus) {
                            temp.baiduYuanChuangStatus = true;
                        } else {
                            temp.baiduYuanChuangStatus = false;
                        }
                        if(temp.guanfanghaoStatus) {
                            temp.guanfanghaoStatus = true;
                        } else {
                            temp.guanfanghaoStatus = false;
                        }
                        if(temp.guanfanghaoStatusPost) {
                            temp.guanfanghaoStatusPost = true;
                        } else {
                            temp.guanfanghaoStatusPost = false;
                        }

                        if(temp.baiduTimePcStatus) {
                            temp.baiduTimePcStatus = true;
                        } else {
                            temp.baiduTimePcStatus = false;
                        }
                        if(temp.baiduTimeMStatus) {
                            temp.baiduTimeMStatus = true;
                        } else {
                            temp.baiduTimeMStatus = false;
                        }
                        if(temp.diyUrlStatus) {
                            temp.diyUrlStatus = true;
                        } else {
                            temp.diyUrlStatus = false;
                        }
                        if(temp.urlCategory) {
                            temp.urlCategory = true;
                        } else {
                            temp.urlCategory = false;
                        }

                        if(temp.aritcleLevelRemove) {
                            temp.aritcleLevelRemove = true;
                        } else {
                            temp.aritcleLevelRemove = false;
                        }

                        if(temp.superSites) {
                            temp.superSites = true;
                        } else {
                            temp.superSites = false;
                        }

                        if(temp.rewrite) {
                            temp.rewrite = true;
                        } else {
                            temp.rewrite = false;
                        }
                        temp.baiduSearchPcUrl = '/baiduSitemapPc.xml';
                        temp.baiduSearchMUrl = '/baiduSitemapMobile.xml';
                        this.setting = temp;
                    }
                });
            },

        }
    }
</script>
