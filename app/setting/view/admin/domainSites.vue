<style>
	
</style>
<template>
    <div>
        <header class="mipcms-container-header clearfix">
            <div class="header-group float-left">
                <h4 class="title">设置</h4> <h5 class="sub-title">多域名站</h5>
               <div style="display: inline-block;margin-top: 4px;" class="ml-2">
                    <i-switch v-model="siteSetting.superSites"  @on-change="siteSettingsSave">
                        <span slot="open">开</span>
                        <span slot="close">关</span>
                    </i-switch>
               </div>
            </div>
            <div class="float-right">
                <button type="button" class="pull-right ivu-btn ivu-btn-primary ivu-btn-circle m-l-lg" @click='itemAdd'>
                    <span>添加站点</span>
                </button>
            </div>
        </header>
        
        <main class="mipcms-container-body" style="height: calc(100% - 50px)">
        <section class="mip-box">
        <section class="mip-box-body">
            <p class="mb-2">提示：多域名模式开启后，域名对应的推送接口，需要单独设置</p>
            <!--内容列表-->
            <section class="diy-table-list" v-cloak>
                <div class="diy-table-item diy-table-item-header">
                    <ul class="list-unstyled row">
                        <li class="col-md-3"><span>ID</span></li>
                        <li class="col-md-2"><span>网址</span></li>
                        <li class="col-md-2"><span>类型</span></li>
                        <li class="col-md-2"><span>模板</span></li>
                        <li class="col-md-2"><span>操作</span></li>
                    </ul>
                </div>
                <div v-if='itemList.length' class="diy-table-body">
                    <div class="diy-table-item" v-for='item in itemList' >
                         <ul class="list-unstyled row">
                            <li class="col-md-3"><span>{{item.id}}</span></li>
                            <li class="col-md-2 over-h-e"><span>{{item.http_type}}{{item.domain}}</span></li>
                            <li class="col-md-2"><span>{{item.type}}</span></li>
                            <li class="col-md-2"><span>{{item.template}}</span></li>
                            <li class="col-md-2">
                                <a @click='itemDel(item)' :disabled='item.disabled'>删除</a>
                                <a @click='itemEdit(item)' class="ml-1">修改</a>
                                <a @click='clickSetting(item)' class="ml-1">设置</a>
                            </li>
                         
                        </ul>
                    </div>
                </div>
                <div class="no-block" v-else>
                    <Icon type="ios-filing-outline"></Icon>
                    <p>暂无数据</p>
                </div>
            </section>
            <!--内容列表结束-->
        </section>
    </section>
     <Modal :title="dialogItemTitle" size="small" v-model='dialogItemStatus' v-cloak>
            <i-form :model="item" :rules="itemRules" ref="item" :label-width="80" >
                    <Form-item label="域名" prop="domain">
                        <i-input v-model="item.domain" :disabled='item.disabled' placeholder="www.baidu.com">
                        <i-select v-model="item.httpType" slot="prepend" style="width: 80px">
                            <i-option value="http://">http://</i-option>
                            <i-option value="https://">https://</i-option>
                        </i-select>
                        </i-input>
                    </Form-item>
                    <Form-item label="类型">
                        <i-select v-model="item.type" style="width: 120px">
                            <i-option value="PC">PC</i-option>
                            <i-option value="M">M</i-option>
                            <i-option value="MIP">MIP</i-option>
                            <i-option value="AMP" disabled>AMP</i-option>
                            <i-option value="other">other</i-option>
                        </i-select>
                    </Form-item>
                    <Form-item label="模板">
                        <i-select v-model="item.template" style="width: 300px">
                            <i-option v-for='item in templateList' :value="item.fileName">{{item.fileName}}</i-option>
                        </i-select>
                    </Form-item>
            </i-form>
            <div slot="footer" class="dialog-footer">
                <i-button @click="dialogItemStatus = false">取 消</i-button>
                <i-button type="primary" @click="itemPost('item')">确 定</i-button>
            </div>
        </Modal>
                
        <Modal size="small" v-model='settingModelStatus' v-cloak width='1000'>
            
            <Tabs :animated='false' style='height: 400px;'>
                <Tab-Pane label="关键词描述" name="name1">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <i-form label-position="top">
                                <Form-item label="网站名称">
                                    <i-input v-model="setting.siteName"></i-input>
                                </Form-item>
                                <Form-item label="DIY首页全标题">
                                    <i-input v-model="setting.diySiteName"></i-input>
                                </Form-item>
                                <Form-item label="网站关键词">
                                    <i-input type="textarea" v-model="setting.keywords"></i-input>
                                </Form-item>
                                <Form-item label="网站描述">
                                    <i-input type="textarea" v-model="setting.description"></i-input>
                                </Form-item>
                            </i-form>
                        </div>
                        <div class="col-md-6">
                            <i-form label-position="top">
                                <Form-item label="网站副标题">
                                    <i-input v-model="setting.indexTitle" placeholder=" - 国内最大的互联网创业交流社区"></i-input>
                                </Form-item>
                                <Form-Item label="ICP备案号">
                                    <i-input v-model="setting.icp"></i-input>
                                </Form-Item>
                                <Form-Item label="流量统计代码">
                                    <i-input type="textarea" v-model="setting.statistical"></i-input>
                                </Form-Item>
                            </i-form>
                        </div>
                    </div>
                </Tab-Pane>
                <Tab-Pane label="MIP推送" name="name2">
                    <i-form label-position="top">
                        <div class="row">
                            <div class="col-md-6">
                                <Form-item label="MIP引入提交接口">
                                    <i-input v-model="setting.mipApi" placeholder="请到百度站长平台 移动专区->MIP引入 获取"></i-input>
                                </Form-item>
                            </div>
                            <div class="col-md-2">
                                 <Form-item label="MIP自动推送">
                                    <i-switch v-model="setting.mipAutoStatus">
                                        <span slot="open">开</span>
                                        <span slot="close">关</span>
                                    </i-switch>
                                </Form-item>
                            </div>
                        </div>
                      
                    </i-form>
                </Tab-Pane>
                <Tab-Pane label="AMP推送" name="name3">
                    <i-form label-position="top">
                        <div class="row">
                            <div class="col-md-6">
                                <Form-item label="AMP引入提交接口">
                                    <i-input v-model="setting.ampApi" placeholder="请到百度站长平台 移动专区->AMP引入 获取"></i-input>
                                </Form-item>
                            </div>
                            <div class="col-md-2">
                                 <Form-item label="AMP自动推送">
                                    <i-switch v-model="setting.ampAutoStatus">
                                        <span slot="open">开</span>
                                        <span slot="close">关</span>
                                    </i-switch>
                                </Form-item>
                            </div>
                        </div>
                      
                    </i-form>
                </Tab-Pane>
                <Tab-Pane label="熊掌号" name="name4">
                     <i-form label-position="top">
                        <div class="row">
                            <div class="col-md-6">
                                <Form-item label="熊掌号ID">
                                    <i-input v-model="setting.xiongZhangId"></i-input>
                                </Form-item>
                                <Form-item label="熊掌号新增接口">
                                    <i-input v-model="setting.xiongZhangNewApi"></i-input>
                                </Form-item>
                                <Form-item label="熊掌号历史接口">
                                    <i-input v-model="setting.xiongZhangOldApi"></i-input>
                                </Form-item>
                            </div>
                            <div class="col-md-2">
                                 <Form-item label="熊掌号模式">
                                    <i-switch v-model="setting.xiongZhangStatus">
                                        <span slot="open">开</span>
                                        <span slot="close">关</span>
                                    </i-switch>
                                </Form-item>
                                <Form-item label="熊掌号自动推送">
                                    <i-switch v-model="setting.xiongZhangNewAutoStatus">
                                        <span slot="open">开</span>
                                        <span slot="close">关</span>
                                    </i-switch>
                                </Form-item>
                            </div>
                        </div>
                    </i-form>
                </Tab-Pane>
                <Tab-Pane label="原创提交" name="name5">
                    <i-form label-position="top">
                        <div class="row">
                            <div class="col-md-6">
                                <Form-item label="原创提交接口">
                                    <i-input v-model="setting.yuanChuangApi"></i-input>
                                </Form-item>
                            </div>
                            <div class="col-md-2">
                               <Form-item label="原创自动推送">
                                    <i-switch v-model="setting.yuanChuangAutoStatus">
                                        <span slot="open">开</span>
                                        <span slot="close">关</span>
                                    </i-switch>
                                </Form-item>
                            </div>
                        </div>
                    </i-form>
                </Tab-Pane>
                <Tab-Pane label="链接提交" name="name6">
                    <i-form label-position="top">
                        <div class="row">
                            <div class="col-md-6">
                                <Form-item label="链接提交接口">
                                    <i-input v-model="setting.linkApi"></i-input>
                                </Form-item>
                            </div>
                            <div class="col-md-2">
                               <Form-item label="链接自动推送">
                                    <i-switch v-model="setting.linkAutoStatus">
                                        <span slot="open">开</span>
                                        <span slot="close">关</span>
                                    </i-switch>
                                </Form-item>
                            </div>
                        </div>
                    </i-form>
                </Tab-Pane>
                <Tab-Pane label="百度站内搜索" name="name7">
                    <i-form label-position="top">
                        <div class="row">
                            <div class="col-md-6">
                                <Form-item label="百度站内搜索Key">
                                    <i-input v-model="setting.baiduSearchKey"></i-input>
                                </Form-item>
                                <Form-item label="百度站内搜索sitemap">
                                    <i-input v-model="setting.baiduSearchSiteMap" disabled></i-input>
                                </Form-item>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                    </i-form>
                </Tab-Pane>
            </Tabs>
          
        <div slot="footer" class="dialog-footer">
            <i-button @click="settingModelStatus = false">取 消</i-button>
            <i-button type="primary" @click="onSubmitSetting()">确 定</i-button>
        </div>
        </Modal>
</main>

    </div>
</template>

<script>
    export default {
     data () {
       return {
            itemName: '站点',
            itemList: [], 
            templateList: [],
            
            item: {
                id: '',
                httpType: '',
                domain: '',
                type: '',
                data_id: 0,
                template: '',
                disabled: false,
            },
            dialogItemTitle: '添加站点',
            dialogItemStatus: false,
            
            itemRules: {
                domain: [{
                    required: true,
                    message: '请输入域名',
                    trigger: 'blur'
                }]
            },
            //弹出
            settingModelStatus: false,
            settingModelTitle: '',
            settingModelId: '',
            setting: '',
            siteSetting: '',
            siteInfo: '',
            zhanqunInfo: '',
            pagination: {
                currentPage: 1,
                limit: 10,
                total: this.total,
            },
       }
     },
    mounted() {
    	if (this.$route.params.dataId) {
        	this.getZhanqunInfo(this.$route.params.dataId);
     	} else {
   			this.getTemplateList();
     	}
        this.getItemList();
        this.getSettingsInfo();
    },
    methods: {
        siteSettingsSave: function() {
            this.$mip.ajax('{$domain}/setting/ApiAdminSetting/settingEdit', {
                setting: JSON.stringify(this.siteSetting),
            }).then(res => {
                if(res.code == 1) {
                    this.$Message.success('保存成功');
                }
            });
        },
        getSettingsInfo() {
            this.$mip.ajax('{$domain}/setting/ApiAdminSetting/settingSelect', {
            }).then(res => {
                this.loading = false;
                if(res.code == 1) {
                    var temp = res.data;
                    if(temp.superSites) {
                        temp.superSites = true;
                    } else {
                        temp.superSites = false;
                    }
                    this.siteSetting = temp;
                }
            });
        },
        getZhanqunInfo(id) {
            this.$mip.ajax('{$domain}/zhanqun/ApiAdminMipzhanqun/itemInfo', {
                id : id,
            }).then(res => {
                if(res.code == 1) {
                    this.zhanqunInfo = res.data;
                    this.getTemplateList();
                }
            });
        },
        clickSetting(item) {
            this.settingModelId = item.id;
            this.settingModelTitle = '设置';
            this.getDomainSites();
        },
        onSubmitSetting: function() {
            this.$mip.ajax('{$domain}/setting/ApiAdminDomainSettings/itemEdit', {
                id: this.settingModelId,
                setting: JSON.stringify(this.setting),
            }).then(res => {
                if(res.code == 1) {
                    this.$Message.success('保存成功');
                    this.getDomainSites();
                }
            });
        },
        getDomainSites() {
            this.$mip.ajax('{$domain}/setting/ApiAdminDomainSites/domainSitesFind', {
                id: this.settingModelId,
            }).then(res => {
                this.loading = false;
                if(res.code == 1) {
                    this.siteInfo = res.data;
                    this.getSettingData();
                }
             });
        },
        getSettingData: function() {
            this.$mip.ajax('{$domain}/setting/ApiAdminDomainSettings/itemFind', {
                id: this.settingModelId,
            }).then(res => {
                this.loading = false;
                if(res.code == 1) {
                    var temp = res.data;
                     
                    if (temp.mipAutoStatus) {
                        temp.mipAutoStatus = true;
                    } else {
                        temp.mipAutoStatus = false;
                    }
                    
                    if (temp.ampAutoStatus) {
                        temp.ampAutoStatus = true;
                    } else {
                        temp.ampAutoStatus = false;
                    }
                    
                    if (temp.xiongZhangStatus) {
                        temp.xiongZhangStatus = true;
                    } else {
                        temp.xiongZhangStatus = false;
                    }
                    
                    if (temp.xiongZhangNewAutoStatus) {
                        temp.xiongZhangNewAutoStatus = true;
                    } else {
                        temp.xiongZhangNewAutoStatus = false;
                    }
                    
                    if (temp.yuanChuangAutoStatus) {
                        temp.yuanChuangAutoStatus = true;
                    } else {
                        temp.yuanChuangAutoStatus = false;
                    }
                    
                    if (temp.linkAutoStatus) {
                        temp.linkAutoStatus = true;
                    } else {
                        temp.linkAutoStatus = false;
                    }
                    
                    temp.baiduSearchSiteMap = '{$domain}/baiduSitemapPc.xml';
                    
                    this.setting = temp;
                    this.settingModelStatus = true;
                }
            });
        },
        
        itemAdd() {
            this.item.id = '';
            this.item.dialogItemTitle = '添加站点';
            this.item.domain = '';
            this.item.httpType = 'http://';
            this.item.type = 'PC';
            this.item.data_id = 0;
            this.item.template = 'default';
            this.item.disabled = false;
            this.dialogItemStatus = true;
        },
        itemDel(item) {
            this.$Modal.confirm({
                title: '消息提示',
                content: '<p>确定删除么？删除后不可恢复</p>',
                onOk: () => {
                    _this = this;
                    this.$mip.ajax('{$domain}/setting/ApiAdminDomainSites/itemDel', {
                        id: item.id,
                    }).then(res => {
                        if(res.code == 1) {
                            if (this.zhanqunInfo && this.zhanqunInfo.type == 'local') {
                                    this.$mip.ajax('{$domain}/zhanqun/ApiAdminMipzhanqun/itemDel', {
                                        id: item.id,
                                    }).then(res => {
                                        if(res.code == 1) {
                                            this.$Message.success('删除成功');
                                            this.getItemList();
                                        }
                                    });
                                } else { 
                                    this.$Message.success('删除成功');
                                    this.getItemList();
                                }
                        }
                    });
                },
                onCancel: () => {
                }
            });
        },
        itemEdit(item) {
            this.item.id = item.id;
            this.item.dialogItemTitle = '修改站点';
            this.item.domain = item.domain;
            this.item.httpType = item.httpType;
            this.item.type = item.type;
            this.item.data_id = item.data_id ? item.data_id : 0;
            this.item.template = item.template;
            this.item.disabled = item.disabled;
            this.dialogItemStatus = true;
        },
        itemPost(val) {
            this.$refs[val].validate((valid) => {
                if(valid) {
                    if (this.item.id) {
                        this.$mip.ajax('{$domain}/setting/ApiAdminDomainSites/itemEdit', {
                            id: this.item.id,
                            httpType: this.item.httpType,
                            domain: this.item.domain,
                            template: this.item.template,
                            data_id: this.item.data_id,
                            type: this.item.type,
                        }).then(res => {
                            if(res.code == 1) {
                                if (this.zhanqunInfo && this.zhanqunInfo.type == 'local') {
                                    this.$mip.ajax('{$domain}/zhanqun/ApiAdminMipzhanqun/itemSubEdit', {
                                         siteId: this.item.id,
                                        httpType: this.item.httpType,
                                        domain: this.item.domain,
                                    }).then(res => {
                                        if(res.code == 1) {
                                            this.$Message.success('修改成功');
                                            this.getItemList();
                                            this.dialogItemStatus = false;
                                        }
                                    });
                                } else {
                                    this.$Message.success('修改成功');
                                    this.getItemList();
                                    this.dialogItemStatus = false;
                                }
                            }
                        });
                    } else {
                        this.$mip.ajax('{$domain}/setting/ApiAdminDomainSites/itemAdd', {
                            title: this.item.title,
                            httpType: this.item.httpType,
                            domain: this.item.domain,
                            data_id: this.item.data_id,
                            template: this.item.template,
                            type: this.item.type,
                        }).then(res => {
                            if(res.code == 1) {
                                //
                                $siteId = res.data;
                                if (this.zhanqunInfo && this.zhanqunInfo.type == 'local') {
                                    this.$mip.ajax('{$domain}/zhanqun/ApiAdminMipzhanqun/itemSubAdd', {
                                        id: this.$route.params.dataId,
                                        siteId: $siteId,
                                        httpType: this.item.httpType,
                                        domain: this.item.domain,
                                        type: this.item.type,
                                    }).then(res => {
                                        if(res.code == 1) {
                                            this.$Message.success('添加成功');
                                            this.getItemList();
                                            this.dialogItemStatus = false;
                                        }
                                    });
                                } else {
                                    this.$Message.success('添加成功');
                                    this.getItemList();
                                    this.dialogItemStatus = false;
                                }
                            }
                        });
                    }
                    
                }
            });
        },
        getItemList() {
            this.loading = true;
            this.$mip.ajax('{$domain}/setting/ApiAdminDomainSites/itemSelect', {
                page: this.pagination.currentPage,
                limit: this.pagination.limit,
            }).then(res => {
                this.itemList = [];
                if(res.code == 1) {
                    var itemList = res.data.itemList;
                    for (var i = 0; i < itemList.length; i++) {
                        itemList[i].httpType = itemList[i].http_type;
                        if ('' == itemList[i].httpType + itemList[i].domain && siteGlobal.siteInfo.superSites) {
                            itemList[i].disabled = true;
                        } else {
                            itemList[i].disabled = false;
                        }
                    }
                    this.itemList = itemList;
                    this.pagination.total = res.data.total;
                }
                this.loading = false;
            });
        },
        
        getTemplateList() {
            if (this.zhanqunInfo && this.zhanqunInfo.type == 'client') {
                var templateListUrl  = '//' + this.zhanqunInfo.domain + '/setting/ApiUserTemplate/templateList';
            } else {
                var templateListUrl  = '{$domain}/setting/ApiAdminTemplate/templateList';
            }
            this.$mip.ajax(templateListUrl, {
                key: this.zhanqunInfo.link_key
            }).then(res => {
                if(res.code == 1) {
                    var templateList = res.data;
                    this.templateList = templateList;
                }
            });
        },
    }
}
</script>
