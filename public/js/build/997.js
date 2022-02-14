"use strict";(self.webpackChunkDooTask=self.webpackChunkDooTask||[]).push([[997],{41130:(t,o,e)=>{e.d(o,{Z:()=>s});var a=e(1519),n=e.n(a)()((function(t){return t[1]}));n.push([t.id,".flow-content[data-v-0c402848],.flow-content .flow-iframe[data-v-0c402848]{height:100%;left:0;position:absolute;top:0;width:100%}.flow-content .flow-iframe[data-v-0c402848]{background:0 0;border:0;float:none;margin:-1px 0 0;max-width:none;outline:0;padding:0}.flow-content .flow-loading[data-v-0c402848]{left:50%;position:absolute;top:50%;transform:translate(-50%,-50%)}.flow-content .zoom-box[data-v-0c402848]{background-color:#fff;border-radius:3px;bottom:20px;box-shadow:3px 3px 10px rgba(0,0,0,.2);color:#666;height:34px;left:20px;max-width:50%;padding:0 6px;position:absolute;z-index:10}.flow-content .zoom-box .zoom-svg[data-v-0c402848]{align-items:center;display:flex;height:34px}.flow-content .zoom-box .zoom-svg .icon[data-v-0c402848]{margin:0 6px}.flow-content .zoom-box .zoom-slider[data-v-0c402848]{display:none;max-width:100%;padding:0 10px;width:300px}.flow-content .zoom-box.zoom-ing .zoom-svg[data-v-0c402848],.flow-content .zoom-box:hover .zoom-svg[data-v-0c402848]{display:none}.flow-content .zoom-box.zoom-ing .zoom-slider[data-v-0c402848],.flow-content .zoom-box:hover .zoom-slider[data-v-0c402848]{display:inline-block}",""]);const s=n},21997:(t,o,e)=>{e.r(o),e.d(o,{default:()=>p});var a=e(17959),n=e(20629);function s(t,o){var e=Object.keys(t);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(t);o&&(a=a.filter((function(o){return Object.getOwnPropertyDescriptor(t,o).enumerable}))),e.push.apply(e,a)}return e}function i(t,o,e){return o in t?Object.defineProperty(t,o,{value:e,enumerable:!0,configurable:!0,writable:!0}):t[o]=e,t}const r={name:"Drawio",props:{value:{type:Object,default:function(){return{}}},readOnly:{type:Boolean,default:!1}},data:function(){return{loadIng:!0,url:null,zoom:-1,zoomIng:!1,bakData:""}},created:function(){var t="en";switch(this.getLanguage()){case"CN":case"TC":t="zh"}var o=this.readOnly?"viewer":"index",e=this.themeIsDark?"dark":"light";this.url=$A.originUrl("js/drawio/"+o+".html?lang="+t+"&theme="+e+"&dev=1")},mounted:function(){window.addEventListener("message",this.handleMessage)},beforeDestroy:function(){window.removeEventListener("message",this.handleMessage)},watch:{value:{handler:function(t){this.bakData!=$A.jsonStringify(t)&&(this.bakData=$A.jsonStringify(t),this.updateContent())},deep:!0},zoom:function(t){this.$refs.myFlow.contentWindow.postMessage({act:"zoom",params:{zoom:t/100}},"*")}},computed:function(t){for(var o=1;o<arguments.length;o++){var e=null!=arguments[o]?arguments[o]:{};o%2?s(Object(e),!0).forEach((function(o){i(t,o,e[o])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(e)):s(Object(e)).forEach((function(o){Object.defineProperty(t,o,Object.getOwnPropertyDescriptor(e,o))}))}return t}({},(0,n.rn)(["themeIsDark"])),methods:{formatZoom:function(t){return t+"%"},updateContent:function(){this.zoom=Math.max(1,100*("number"==typeof this.value.scale?this.value.scale:1)),this.$refs.myFlow.contentWindow.postMessage({act:"setXml",params:Object.assign(this.value,void 0===this.value.xml?{xml:this.value.content}:{})},"*")},handleMessage:function(t){var o=t.data;switch(o.act){case"ready":this.loadIng=!1,this.updateContent();break;case"change":this.bakData=$A.jsonStringify(o.params),this.$emit("input",o.params);break;case"save":this.$emit("saveData");break;case"imageContent":var e=new a.ZP({format:[o.params.width,o.params.height]});e.addImage(o.params.content,"PNG",0,0,0,0),e.save("".concat(o.params.name,".pdf"))}},exportPNG:function(t){var o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:10;this.$refs.myFlow.contentWindow.postMessage({act:"exportPNG",params:{name:t||this.$L("无标题"),scale:o,type:"png"}},"*")},exportPDF:function(t){var o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:10;this.$refs.myFlow.contentWindow.postMessage({act:"exportPNG",params:{name:t||this.$L("无标题"),scale:o,type:"imageContent"}},"*")}}};var c=e(93379),l=e.n(c),d=e(41130),m={insert:"head",singleton:!1};l()(d.Z,m);d.Z.locals;const p=(0,e(51900).Z)(r,(function(){var t=this,o=t.$createElement,e=t._self._c||o;return e("div",{staticClass:"flow-content"},[e("iframe",{ref:"myFlow",staticClass:"flow-iframe",attrs:{src:t.url}}),t._v(" "),t.loadIng?e("div",{staticClass:"flow-loading"},[e("Loading")],1):t._e(),t._v(" "),t.readOnly&&t.zoom>0?e("div",{staticClass:"zoom-box",class:{"zoom-ing":t.zoomIng}},[e("div",{staticClass:"zoom-svg"},[e("svg",{staticClass:"icon",attrs:{t:"1600613502044",viewBox:"0 0 1024 1024",version:"1.1",xmlns:"http://www.w3.org/2000/svg","p-id":"1161",width:"18",height:"18"}},[e("path",{attrs:{d:"M598.646154 401.723077H279.630769c-15.753846 0-27.569231 11.815385-27.569231 31.507692 0 15.753846 11.815385 27.569231 31.507693 27.569231h319.015384c15.753846 0 27.569231-11.815385 27.569231-31.507692 0-15.753846-15.753846-27.569231-31.507692-27.569231z",fill:"#666666","p-id":"1162"}}),e("path",{attrs:{d:"M921.6 850.707692l-204.8-196.923077c47.261538-59.076923 78.769231-137.846154 78.769231-220.553846 0-196.923077-157.538462-354.461538-354.461539-354.461538s-354.461538 157.538462-354.461538 354.461538 157.538462 354.461538 354.461538 354.461539c90.584615 0 173.292308-35.446154 236.307693-90.584616l204.8 196.923077c3.938462 3.938462 11.815385 7.876923 19.692307 7.876923s15.753846-3.938462 19.692308-7.876923c11.815385-15.753846 11.815385-35.446154 0-43.323077z m-484.430769-126.030769c-161.476923 0-295.384615-133.907692-295.384616-295.384615S275.692308 133.907692 437.169231 133.907692s295.384615 133.907692 295.384615 295.384616-129.969231 295.384615-295.384615 295.384615z",fill:"#666666","p-id":"1163"}})]),t._v(" "),e("svg",{staticClass:"icon",attrs:{t:"1600613514136",viewBox:"0 0 1024 1024",version:"1.1",xmlns:"http://www.w3.org/2000/svg","p-id":"1403",width:"18",height:"18"}},[e("path",{attrs:{d:"M929.476923 854.646154l-212.676923-200.861539c47.261538-59.076923 78.769231-137.846154 78.769231-220.553846 0-196.923077-157.538462-354.461538-354.461539-354.461538s-354.461538 157.538462-354.461538 354.461538 157.538462 354.461538 354.461538 354.461539c90.584615 0 173.292308-35.446154 236.307693-90.584616l212.676923 200.861539c3.938462 3.938462 11.815385 7.876923 19.692307 7.876923s15.753846-3.938462 19.692308-7.876923c11.815385-11.815385 11.815385-31.507692 0-43.323077z m-488.369231-126.030769c-161.476923 0-295.384615-133.907692-295.384615-295.384616s133.907692-295.384615 295.384615-295.384615 295.384615 133.907692 295.384616 295.384615-133.907692 295.384615-295.384616 295.384616z",fill:"#666666","p-id":"1404"}}),e("path",{attrs:{d:"M598.646154 401.723077h-129.969231V271.753846c0-15.753846-11.815385-31.507692-31.507692-31.507692s-31.507692 11.815385-31.507693 31.507692v129.969231H279.630769c-15.753846 0-31.507692 11.815385-31.507692 31.507692s11.815385 31.507692 31.507692 31.507693h129.969231V590.769231c0 15.753846 11.815385 31.507692 31.507692 31.507692s31.507692-11.815385 31.507693-31.507692v-129.969231h129.96923c15.753846 0 31.507692-11.815385 31.507693-31.507692s-15.753846-27.569231-35.446154-27.569231z",fill:"#666666","p-id":"1405"}})])]),t._v(" "),e("Slider",{staticClass:"zoom-slider",attrs:{min:1,max:300,"tip-format":t.formatZoom},on:{"on-change":function(o){t.zoomIng=!1},"on-input":function(o){t.zoomIng=!0}},model:{value:t.zoom,callback:function(o){t.zoom=o},expression:"zoom"}})],1):t._e()])}),[],!1,null,"0c402848",null).exports}}]);