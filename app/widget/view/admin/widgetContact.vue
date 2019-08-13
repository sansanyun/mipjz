<style>
	
</style>
<template>
<div>
	<header class="mipcms-container-header clearfix">
	    <div class="header-group">
	        <h4 class="title">功能</h4> <h5 class="sub-title">留言管理</h5>
	    </div>
	</header>

<main class="mipcms-container-body" style="height: calc(100% - 50px)">
    <section class="mip-box">
        <section class="mip-box-body">
            <!--内容列表-->
            <section class="diy-table-list" v-cloak>
                
                <div class="diy-table-item diy-table-item-header">
                    <ul class="list-unstyled row">
                       
                        <li class="col-md-2">
                            <span>姓名</span>
                        </li>
                        <li class="col-md-2">
                            <span>电话</span>
                        </li>
                        <li class="col-md-2">
                            <span>时间</span>
                        </li>
                        <li class="col-md-4">
                            <span>内容</span>
                        </li>
                        <li class="col-md-2">
                            <span>操作</span>
                        </li>
                    </ul>
                </div>
                <div v-if='itemList.length' class="diy-table-body">
                    <div class="diy-table-item" v-for='item in itemList' >
                         <ul class="list-unstyled row">
                                
                            <li class="col-md-2 over-h-e">
                                <span>{{item.name}}</span>
                            </li>
                            <li class="col-md-2">
                                <span>{{item.tel}}</span>
                            </li>
                            <li class="col-md-2">
                                <span>{{item.add_time | dateTime}}</span>
                            </li>
                            <li class="col-md-4 over-h-e">
                                <span>{{item.info}}</span>
                            </li>
                            <li class="col-md-2">
                                <i-button type="error" size="small" @click='itemDel(item)'>删除</i-button>
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
</main>
</div>
</template>

<script>
export default {
    data () {
        return {
         	itemList: [], 
 
            pagination: {
                currentPage: 1,
                limit: 10,
                total: this.total,
            },
        }
    },
    watch: {
    },
    mounted() {
    	this.getItemList();
    },
    methods: {
        itemDel(item) {
                this.$Modal.confirm({
                    title: '消息提示',
                    content: '<p>确定删除么？删除后不可恢复</p>',
                    onOk: () => {
                        this.$mip.ajax('{$domain}/widget/ApiAdminWidgetContact/itemDel', {
                            id: item.id,
                        }).then(res => {
                            if(res.code == 1) {
                                this.$Message.success('删除成功');
                                this.getItemList();
                            }
                        });
                    },
                    onCancel: () => {
                    }
                });
            },
             
            getItemList() {
                this.loading = true;
                this.$mip.ajax('{$domain}/widget/ApiAdminWidgetContact/itemList', {
                    page: this.pagination.currentPage,
                    limit: this.pagination.limit,
                }).then(res => {
                    this.itemList = [];
                    if(res.code == 1) {
                        var itemList = res.data.itemList;
                        this.itemList = itemList;
                        this.pagination.total = res.data.total;
                    }
                    this.loading = false;
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
    }
}
</script>
