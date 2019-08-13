<style>
     .template-item {
        position: relative;
        margin-bottom: 10px;
    }
    .template-item img {
        max-width: 100%;
        height: auto;
        cursor: pointer;
    }
   .template-item .ivu-icon-md-checkmark-circle {
        position: absolute;
        top: 2px;
        right: 5px;
    }
    .template-item h4{
        margin-top: 10px;
        font-size: 18px;
    }
</style>
<template>
    <div>
        <header class="mipcms-container-header clearfix">
            <div class="header-group">
                <h4 class="title">设置</h4> <h5 class="sub-title">网站模板</h5>
            </div>
            <div class="float-right">
               <a class="pull-right ivu-btn ivu-btn-primary ivu-btn-circle" href="http://www.mipmb.com">
                    <span>更多模板</span>
                </a>
            </div>
        </header>
                
        <main class="mipcms-container-body" style="height: calc(100% - 50px)">
            <div class="row">
                <div class="col-md-4" v-for='item in templateList'>
                    <section class="mip-box">
                        <section class="mip-box-body template-item">
                            <Icon type="md-checkmark-circle" v-if='item.status' size='26' color='#00aaee'></Icon>
                            <img :src="item.info.thumbnail" @click='chooseTemplate(item.fileName)'/>
                            <h4 class="p-1">{{item.info.name}}</h4>
                        </section>
                    </section>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                templateList: [],
                siteInfo: '',
                zhanqunInfo: '',
            }
        },
        watch: {
            siteChangeStatus() {
                this.getSettingData();
            }
        },
        mounted() {
        	if (this.$route.params.dataId) {
            	this.getZhanqunInfo(this.$route.params.dataId);
        	} else {
            	this.getSettingData();
        	}
        },
        methods: {
    		getZhanqunInfo(id) {
                this.$mip.ajax('{$domain}/zhanqun/ApiAdminMipzhanqun/itemInfo', {
                    id : id,
                }).then(res => {
                    if(res.code == 1) {
                        this.zhanqunInfo = res.data;
                        this.getSettingData();
                    }
                });
            },
            getTemplateList() {
            	if (this.zhanqunInfo && this.zhanqunInfo.type == 'client') {
                    var templateListUrl  = this.zhanqunInfo.web_url + '/setting/ApiUserTemplate/templateList';
                    
	                this.$mip.ajax(templateListUrl, {
	                	key: this.zhanqunInfo.link_key,
	                }).then(res => {
	                    if(res.code == 1) {
	                        var templateList = res.data;
	                        for (var i = 0; i < templateList.length; i++) {
	                            if (templateList[i].fileName == this.siteInfo.template) {
	                                templateList[i].status = true;
	                            } else {
	                                templateList[i].status = false;
	                            }
	                        }
	                        this.templateList = templateList;
	                    }
	                });
                } else {
                    var templateListUrl  = '{$domain}/setting/ApiAdminTemplate/templateList';
                    
	                this.$mip.ajax(templateListUrl, {
	                	
	                }).then(res => {
	                    if(res.code == 1) {
	                        var templateList = res.data;
	                        for (var i = 0; i < templateList.length; i++) {
	                            if (templateList[i].fileName == this.siteInfo.template) {
	                                templateList[i].status = true;
	                            } else {
	                                templateList[i].status = false;
	                            }
	                        }
	                        this.templateList = templateList;
	                    }
	                });
                }
            },
            getSettingData() {
                var _this = this;
                this.loading = true;
                this.$mip.ajax('{$domain}/setting/ApiAdminSetting/settingSelect', {
                    
                }).then(function(res) {
                    _this.loading = false;
                    if(res.code == 1) {
                        _this.siteInfo = res.data;
                        _this.getTemplateList();
                    }
                });
            },
            chooseTemplate(val) {
                this.$Modal.confirm({
                    title: '消息提示',
                    content: '<p>确定切换成您选择的模板？</p>',
                    onOk: () => {
                        var _this = this;
                        this.$mip.ajax('{$domain}/setting/ApiAdminTemplate/templateSave', {
                            fileName: val,
                        }).then(function(res) {
                            if(res.code == 1) {
                                _this.$Message.success('设置成功');
                                _this.getSettingData();
                            }
                        });
                    }, 
                    onCancel: () => {
                        //
                    }
                });
            },

        }
    }
</script>
