<style>
	
</style>
<template>
    <div>
        <header class="mipcms-container-header clearfix">
            <div class="header-group">
                <h4 class="title">文章</h4> <h5 class="sub-title">SEO推送（请先配置相应的推送接口地址）</h5>
            </div>
        </header>
        <main class="mipcms-container-body" style="height: calc(100% - 50px)">
          
            <section class="" >
            <section class="mip-box">
                <section class="mip-box-body">
                    <!--内容列表-->
                    <section class="diy-table-list" v-cloak>
                        <div class="content-list-header d-flex justify-content-between">
                            <div>
                                <i-Select v-model="currentDomainId" transfer style="width:200px" v-if='superSites'>
                                    <i-Option v-for="item in domainSitesList" :value="item.id" :key="item.id">{{item.domain}}({{item.type}}站)</i-Option>
                                </i-Select>
                            </div>
                            <div v-if='superSites'>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!domainSitesInfo.mipApi || !itemListSelectStatus' @click="mipPush('mip')"><i class="ivu-icon ivu-icon-ios-bolt"></i> MIP推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!domainSitesInfo.ampApi || !itemListSelectStatus' @click="mipPush('amp')"><i class="ivu-icon ivu-icon-ios-bolt"></i> AMP推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!domainSitesInfo.xiongZhangNewApi || !itemListSelectStatus' @click="mipPush('xiongZhangNew')"><i class="ivu-icon ivu-icon-ios-paw"></i> 新增推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!domainSitesInfo.xiongZhangOldApi || !itemListSelectStatus' @click="mipPush('xiongZhangOld')"><i class="ivu-icon ivu-icon-ios-paw-outline"></i> 历史推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!domainSitesInfo.yuanChuangApi || !itemListSelectStatus' @click="mipPush('yuanChuang')"><i class="ivu-icon ivu-icon-ios-paper-outline"></i> 原创推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!domainSitesInfo.linkApi || !itemListSelectStatus' @click="mipPush('link')"><i class="ivu-icon ivu-icon-paper-airplane"></i> 链接推送</button>
                            </div>
                            <span v-if='!superSites'>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!settingsInfo.mipApiAddress || !itemListSelectStatus' @click="mipOnePush('mip')"><i class="ivu-icon ivu-icon-ios-bolt"></i> MIP推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!settingsInfo.guanfanghaoRealtimeUrl || !itemListSelectStatus' @click="mipOnePush('guanfanghaoNew')"><i class="ivu-icon ivu-icon-ios-paw"></i> 新增推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!settingsInfo.guanfanghaoUrl || !itemListSelectStatus' @click="mipOnePush('guanfanghaoOld')"><i class="ivu-icon ivu-icon-ios-paw-outline"></i> 历史推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!settingsInfo.baiduYuanChuangUrl || !itemListSelectStatus' @click="mipOnePush('baiduYuanChuangUrl')"><i class="ivu-icon ivu-icon-ios-paper-outline"></i> 原创推送</button>
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!settingsInfo.baiduTimePcUrl || !itemListSelectStatus' @click="mipOnePush('link')"><i class="ivu-icon ivu-icon-paper-airplane"></i> 链接推送</button>
                            </span>

                        </div>
                        <div class="diy-table-item diy-table-item-header">
                            <ul class="list-unstyled row">
                                <li class="col-md-1">
                                    <Checkbox v-model="itemListSelectStatus"  :disabled='!itemList.length' @on-change='itemListSelectChange'>全选</Checkbox>
                                </li>
                                <li class="col-md-1"><span>标题</span></li>
                                <li class="col-md-1">
                            		<Dropdown trigger="click" @on-click='changeClick'>
								        <a href="javascript:void(0)">MIP推送
								            <Icon type="arrow-down-b"></Icon>
								        </a>
								        <DropdownMenu slot="list">
								            <DropdownItem name='mipOk'>已推送</DropdownItem>
								            <DropdownItem name='mipNo'>未推送</DropdownItem>
								        </DropdownMenu>
								    </Dropdown>
                                </li>
                                <li class="col-md-1">
                            		<Dropdown trigger="click" @on-click='changeClick'>
								        <a href="javascript:void(0)">熊掌推送
								            <Icon type="arrow-down-b"></Icon>
								        </a>
								        <DropdownMenu slot="list">
								            <DropdownItem name='xzhOk'>已推送</DropdownItem>
								            <DropdownItem name='xzhNo'>未推送</DropdownItem>
								        </DropdownMenu>
								    </Dropdown>
                                </li>
                                <li class="col-md-1">
                            		<Dropdown trigger="click" @on-click='changeClick'>
								        <a href="javascript:void(0)">普通推送
								            <Icon type="arrow-down-b"></Icon>
								        </a>
								        <DropdownMenu slot="list">
								            <DropdownItem name='linkOk'>已推送</DropdownItem>
								            <DropdownItem name='linkNo'>未推送</DropdownItem>
								        </DropdownMenu>
								    </Dropdown>
                                </li>
                                
                                <li class="col-md-1">
                            		<Dropdown trigger="click" @on-click='changeClick'>
								        <a href="javascript:void(0)">抓取次数
								            <Icon type="arrow-down-b"></Icon>
								        </a>
								        <DropdownMenu slot="list">
								            <DropdownItem name='collectOk'>已抓取</DropdownItem>
								            <DropdownItem name='collectNo'>未抓取</DropdownItem>
								        </DropdownMenu>
								    </Dropdown>
                                </li>
                                <li class="col-md-2"><span>抓取时间</span></li>
                                
                            </ul>
                        </div>
                        <div v-if='itemList.length' class="diy-table-body">
                            <div class="diy-table-item" v-for='item in itemList' >
                                 <ul class="list-unstyled row">
                                    <li class="col-md-1">
                                        <Checkbox v-model="item.itemListSelectStatus"></Checkbox>
                                    </li>
                                    <li class="col-md-1 over-h-e">
                                        <span><a :href="item.url" target="_blank">{{item.title}}</a></span>
                                    </li>
                                    <li class="col-md-1">{{item.mip_push_num}}</li>
                                    <li class="col-md-1">{{item.xzh_push_num}}</li>
                                    <li class="col-md-1">{{item.link_push_num}}</li>
                                    <li class="col-md-1">{{item.baidu_spider_num}}</li>
                                    <li class="col-md-2 over-h-e">
                                        <span v-if='item.baidu_spider_time'>{{item.baidu_spider_time | time}}</span>
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
                    <!--分页-->
                    <div class="text-right clearfix mt-3">
                        <Page :total="pagination.total"  @on-page-size-change='itemPaginationSelect' :page-size-opts='[10,100,500,1000,5000]' show-total show-sizer placement='top' @on-change='itemPaginationClick'></Page>
                    </div>
                </section>
            </section>
         
        </section>
    </main>   
     <!--推送-->
    <Modal title="推送" v-model="push.pushStatus">
    	 <i-form :label-width="80">
            <Form-Item label="数量">
            	{{push.pushData.length}}篇
            </Form-Item>
            <Form-Item label="列表"> 
            	<ul class="list-unstyled">
                	<li class="text-999 font-12" style="line-height: 20px;" v-for='item in push.pushData'>{{item.url}}</li>
            	</ul>
            </Form-Item>
            <Form-Item label="接口">
            	{{push.pushAddress}}
            </Form-Item>
            <Form-Item label="进度">
                <i-Progress :percent="scale"></i-Progress>
            </Form-Item>
        </i-form>
        <span slot="footer" class="dialog-footer">
            <i-button @click="push.pushStatus = false">取 消</i-button>
            <i-button type="primary" @click='pushPost(0)'>确 定</i-button>
          </span>
    </Modal>
    
    
    <Modal title='批量查询' v-model="searchModal" width="360" v-cloak>
        <i-form :label-width="80">
            <Form-Item label="进度">
                <i-Progress :percent="scale"></i-Progress>
            </Form-Item>
        </i-form>

        <span slot="footer" class="dialog-footer">
            <i-button @click="searchModal = false">取 消</i-button>
            <i-button type="primary" @click="searchBatchAction(0)">点击开始</i-button>
        </span>
    </Modal>
    </div>
</template>

<script>
    export default {
     data () {
       return {
            itemListSelectStatus: false,
            cid: '',
            categoryList: [],
            itemList: [], 
            type: '',
            pullData: [],
            pullBtnInfo: '点击更新最新数据',
            status: '',
            statusText: '全部',
            //域名列表
            domainSitesList: [],
            currentDomainId: '',
            domainSitesInfo: '',
            //
            superSites: '',
            settingsInfo: '',
            //
            searchModal: false,
 			scale: 0,
			countList: [],
	
           //推送
            push: {
                pushStatus: false,
                pushData: [],
                pushType: '',
                pushAddress: ''
            },
            pagination: {
                currentPage: 1,
                limit: 10,
                total: this.total,
            },
       }
    },
        watch: {
            currentDomainId() {
                this.getDomainSettings();
            },
            itemList: {
                handler(newValue, oldValue) {
                    var tempStatus = false;
        　　　　　for (var i = 0; i < this.itemList.length; i ++) {
                    if (this.itemList[i].itemListSelectStatus == true) {
                        tempStatus = true;
                    }
                }
                if (tempStatus) {
                    this.itemListSelectStatus = true;
                } else {
                    this.itemListSelectStatus = false;
                }
        　　　　},
        　　　　deep: true
        　　}
        },
        mounted() {
            this.getSettingData();
        },
        methods: {
        	changeClick(val) {
        		this.type = val;
        		this.getItemList();
        	},
        	
        	searchBatchClick() {
	         	this.scale = 0;
	    		this.countList = [];
        		this.searchModal = true;
        		var itemIds = [];
	            for(var i = 0; i < this.itemList.length; i++) {
	                if (this.itemList[i].itemListSelectStatus) {
	                    itemIds.push(this.itemList[i].uuid);
	                }
	            }
	    		this.countList = itemIds;
        	},
        	searchBatchAction(index) {
        		if ('{$addInfo["searchUrl"]}' == '') {
	          		this.$Message.error('您尚未安装收录查询插件，若已安装，请刷新当前页面');
	          		return false;
        		}
                var scale = parseInt(index / (parseInt(this.countList.length)) * 100);
            	if (scale < 100) {
	                this.scale = scale;
	            } else {
	                this.scale = 100;
	            }
	            if (parseInt(index) + 1 > this.countList.length) {
	          		this.$Message.success('操作完毕');
	    			this.getItemList();
	                return false;
	            }
        		this.$mip.ajax('{$domain}/{$Think.config.admin}/ApiAdminSearchUrl/searchUrl', {
	            	uuid: this.countList[index],
	            }).then(res => {
	                if(res.code == 1) {
	                	this.searchBatchAction(index+1);
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
                        this.superSites = temp.superSites;
                        this.settingsInfo = temp;
                        if (temp.superSites) {
                            this.getDomainSites();
                        } else {
                            this.getItemList();
                        }
                    }
                });
            },
            //获取站点信息
            getDomainSites() {
                this.$mip.ajax('{$domain}/setting/ApiAdminDomainSites/itemSelect', {
                }).then(res => {
                    if(res.code == 1) {
                        this.domainSitesList = res.data.itemList;
                        if (this.domainSitesList && this.domainSitesList.length) {
                            this.currentDomainId = this.domainSitesList[0].id;
                        }
                    }
                 });
            },
            getDomainSettings() {
                this.$mip.ajax('{$domain}/setting/ApiAdminDomainSettings/itemFind', {
                    id: this.currentDomainId,
                }).then(res => {
                    if(res.code == 1) {
                        this.domainSitesInfo = res.data;
                        this.getItemList();
                    }
                 });
            },
            //获取选择的URL数据
            getUrl() {
                var itemUrlList = [];
                for(var i = 0; i < this.itemList.length; i++) {
                    if (this.itemList[i].itemListSelectStatus) {
                        itemUrlList.push({'url': this.itemList[i].url,'id': this.itemList[i].id});
                    }
                }
                return itemUrlList;
            },
            //mip推送
            mipPush(type) {
	         	this.scale = 0;
	    		this.countList = [];
                this.push.pushType = type;
                this.push.pushData = this.getUrl();
                if (this.push.pushType == 'mip') {
                    this.push.pushAddress = this.domainSitesInfo.mipApi;
                }
                if (this.push.pushType == 'amp') {
                    this.push.pushAddress = this.domainSitesInfo.ampApi;
                }
                if (this.push.pushType == 'xiongZhangNew') {
                    this.push.pushAddress = this.domainSitesInfo.xiongZhangNewApi;
                }
                if (this.push.pushType == 'xiongZhangOld') {
                    this.push.pushAddress = this.domainSitesInfo.xiongZhangOldApi;
                }
                if (this.push.pushType == 'yuanChuang') {
                    this.push.pushAddress = this.domainSitesInfo.yuanChuangApi;
                }
                if (this.push.pushType == 'link') {
                    this.push.pushAddress = this.domainSitesInfo.linkApi;
                }
                this.push.pushStatus = true;
            },
            
             //mip推送
            mipOnePush(type) {
	         	this.scale = 0;
	    		this.countList = [];
                this.push.pushType = type;
                this.push.pushData = this.getUrl();
                if (this.push.pushType == 'guanfanghaoNew') {
                    this.push.pushAddress = this.settingsInfo.guanfanghaoRealtimeUrl;
                }
                if (this.push.pushType == 'baiduYuanChuangUrl') {
                    this.push.pushAddress = this.settingsInfo.baiduYuanChuangUrl;
                }
                if (this.push.pushType == 'mip') {
                    this.push.pushAddress = this.settingsInfo.mipApiAddress;
                }
                if (this.push.pushType == 'guanfanghaoOld') {
                    this.push.pushAddress = this.settingsInfo.guanfanghaoUrl;
                }
                if (this.push.pushType == 'link') {
                    this.push.pushAddress = this.settingsInfo.baiduTimePcUrl;
                }
                this.push.pushStatus = true;
            },
            pushPost(index) {
                if (!this.push.pushData.length) {
                    this.$Message.error('请选择要推送的内容链接');
                    return false;
                }
                if (!this.push.pushType) {
                    this.$Message.error('系统无法识别你推送的类型');
                    return false;
                }
                
	            if (this.push.pushType != 'hand') {
	                if (!this.push.pushAddress) {
	                    this.$Message.error('请去完善你要推送的API接口地址');
	                    return false;
	                }
               	}
                
                var scale = parseInt(index / (parseInt(this.push.pushData.length)) * 100);
            	if (scale < 100) {
	                this.scale = scale;
	            } else {
	                this.scale = 100;
	            }
	            if (parseInt(index) + 1 > this.push.pushData.length) {
	          		this.$Message.success('操作完毕');
	    			this.getItemList();
	                return false;
	            }
	            
	            if (this.push.pushType != 'hand') {
	                this.$mip.ajax('{$domain}/setting/ApiAdminDomainSettings/urlPost', {
	                    url: this.push.pushData[index]['url'],
	                    id: this.push.pushData[index]['id'],
	                    postAddress: this.push.pushAddress,
	                    pushType: this.push.pushType,
	                }).then(res => {
	                    if(res.code == 1) {
	                        var resMIp = JSON.parse(res.msg);
	                        if (this.push.pushType == 'guanfanghaoNew') {
	                            this.$Message.success('推送成功：' + resMIp.success_realtime + '条；剩余:' + resMIp.remain_realtime + '条');
	                        }
	                        if (this.push.pushType == 'baiduYuanChuangUrl') {
	                            this.$Message.success('推送成功：' + resMIp.success_original + '条；剩余:' + resMIp.remain_original + '条');
	                        }
	                        if (this.push.pushType == 'mip') {
	                            if (resMIp.not_valid) {
	                                this.$Message.error('推送的网址不合法');
	                            }
	                            if (resMIp.not_same_site) {
	                                this.$Message.error('推送的地址，与百度接口中的域名不匹配');
	                            }
	                            if (resMIp.success_mip) {
	                                this.$Message.success('推送成功：' + resMIp.success_mip + '条；剩余:' + resMIp.remain_mip + '条');
	                            }
	                        }
	                        if (this.push.pushType == 'guanfanghaoOld') {
	                            this.$Message.success('推送成功：' + resMIp.success_batch + '条；剩余:' + resMIp.remain_batch + '条');
	                        }
	                        if (this.push.pushType == 'link') {
	                            this.$Message.success('推送成功：' + resMIp.success + '条');
	                        }
		                	this.pushPost(index+1);
	                    }
	                });
	            } else {
	            	
	        		if ('{$addInfo["handPush"]}' == '') {
		          		this.$Message.error('您尚未安装收录查询插件，若已安装，请刷新当前页面');
		          		return false;
	        		}
	            	this.$mip.ajax('{$domain}/handPush/ApiAdminHandPush/push', {
	                    url: this.push.pushData[index]['url'],
	                    id: this.push.pushData[index]['id'],
	                }).then(res => {
	                    if(res.code == 1) {
	                        var resMIp = JSON.parse(res.msg);
	                     	this.$Message.success('推送成功：' + resMIp.success + '条；剩余:' + resMIp.remain + '条');
		                	this.pushPost(index+1);
	                    }
	                });
	            }
                
                
            }, 
            
             //选择全部
            itemListSelectChange() {
                var itemList = this.itemList;
                for (var i = 0; i < itemList.length; i++) {
                    if (this.itemListSelectStatus) {
                        itemList[i].itemListSelectStatus = true;
                    } else {
                        itemList[i].itemListSelectStatus = false;
                    }
                }
                this.itemList = itemList;
            },
            //
            getItemList() {
                this.loading = true;
                var _this = this;
                var tempDomain = '';
                if (this.currentDomainId) {
                    for (var i = 0; i < this.domainSitesList.length; i++) {
                           if (this.domainSitesList[i].id == this.currentDomainId) {
                               tempDomain = this.domainSitesList[i].http_type + this.domainSitesList[i].domain;
                           }
                    }
                }
                this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemList', {
                    domain: tempDomain,
                    type: this.type,
                    status: this.status,
                    cid: this.cid,
                    page: this.pagination.currentPage,
                    limit: this.pagination.limit,
                }).then(function(res) {
                    _this.itemList = [];
                    if(res.code == 1) {
                        var itemList = res.data.itemList;
                        if (itemList) {
                            for (var i = 0; i < itemList.length; i++) {
                                itemList[i].itemListSelectStatus = false; 
                            }
                            _this.itemList = itemList;
                        }
                        _this.pagination.total = res.data.total;
                    }
                    _this.loading = false;
                });
            },
            itemPaginationSelect(val) {
                this.pagination.limit = val;
                this.getItemList();
            },
            itemPaginationClick(val) {
                this.pagination.currentPage = val;
                this.getItemList();
            },
            menuClick(id) {
                for (var i = 0; i < this.categoryList.length; i++) {
                    this.categoryList[i].active = false;
                    if (this.categoryList[i].children) {
                        for (var j = 0; j < this.categoryList[i].children.length; j++) {
                            this.categoryList[i].children[j].active = false;
                        }
                    }
                }
                for (var i = 0; i < this.categoryList.length; i++) {
                    if (id == this.categoryList[i].id) {
                        this.categoryList[i].active = true;
                    }
                    if (this.categoryList[i].children) {
                        for (var j = 0; j < this.categoryList[i].children.length; j++) {
                            if (id == this.categoryList[i].children[j].id) {
                                this.categoryList[i].children[j].active = true;
                            }
                        }
                    }
                }
                this.cid = id;
                this.getItemList();
            },
        }
    }
</script>
