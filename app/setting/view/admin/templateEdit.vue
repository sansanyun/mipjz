<style>
  
</style>
<template>
    <div>
    	<header class="mipcms-container-header clearfix">
	        <div class="header-group">
	            <h4 class="title">模板</h4> <h5 class="sub-title">在线编辑</h5>
	        </div>
	    </header>
	    <main class="mipcms-container-body" style="height: calc(100% - 50px)">
	        <aside class="side-box mip-box" style="height: calc(100% - 50px - 92px);bottom: 15px;">
	            <section class="side-box-body">
	                <section class="side-list">
	                    <ul>
	                        <li class="nav-item" :class='{"active" : item.active}' v-for='item in jsonList'>
	                            <a @click="menuClick(item)">{{item.name}}</a>
	                        </li>
	                    </ul>
	                </section>
	            </section>
	        </aside>
	        <section class="content-box">
	            <section class="mip-box">
	            	<section class="mip-box-body">
		                <Collapse v-model="currentName" accordion v-if='jsonInfo'>
					        <Panel :name='index' v-for='(currentData,index) in jsonInfo.data'>
					            {{currentData.blockName}}
					            <div slot="content">
					            	<Form>
								        <FormItem label="主标题" v-if='currentData && currentData.heading.content' >
			                	    		<Input size="large" v-model="currentData.heading.content"/>
								        </FormItem>
								        <FormItem label="副标题" v-if='currentData && currentData.subheading.content'>
			                	    		<Input size="large" v-model="currentData.subheading.content"/>
								        </FormItem>
								        <FormItem label="描述" v-if='currentData && currentData.description.content'>
			                	    		<Input size="large" type="textarea" rows='4' v-model="currentData.description.content"/>
								        </FormItem>
								    </Form>
					            </div>
					        </Panel>
					    </Collapse>
	            	</section>
	            </section>
	        </section>
	    </main>
    </div>
</template>

<script>
export default {
    data () {
        return {
        	currentName: '1',
        	jsonList: [],
        	jsonInfo: [],
        }
    },
    watch: {
       
    },
    mounted() {
    	this.getJsonList();
    },
    methods: {
    	getJsonList() {
    		this.$mip.ajax('{$domain}/setting/ApiAdminTemplateEdit/getJsonList', {
            }).then(res => {
                if(res.code == 1) {
                    this.jsonList = res.data;
                    if (this.jsonList && this.jsonList.length) {
                    	for (var i = 0; i < this.jsonList.length; i++) {
                    		if (i == 0) {
                    			this.jsonList[i].active = true;
                    		} else {
                    			this.jsonList[i].active = false;
                    		}
                    	}
                    	this.jsonInfo = this.jsonList[0];
                    }
                }
            });
    	},
		menuClick(item) {
			this.jsonInfo = item;
		},
    }
}
</script>
