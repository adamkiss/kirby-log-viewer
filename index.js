(function(){"use strict";function c(e,t,n,i,r,o,l,a){var s=typeof e=="function"?e.options:e;return t&&(s.render=t,s.staticRenderFns=n,s._compiled=!0),s._scopeId="data-v-"+o,{exports:e,options:s}}const u={props:{all:Object,log:Object,content:Object},data(){return{currtime:"…"}},computed:{logs(){return Object.keys(this.content).map(e=>[e,this.content[e]]).filter(([e,t])=>!(t===void 0||!Array.isArray(t)||!t.length||t.length===1&&(t[0].content===void 0||t[0].content==="")))},picklist(){return Object.keys(this.all).map(e=>({value:e,text:`${e} (${this.all[e].range})`}))}},mounted(){setInterval(()=>{this.currtime=this.$library.dayjs().format("D.MM.YY HH:mm:ss")},1e3)},methods:{onChangeLog(e){panel.open(`logs/${e}`)}},created(){console.log(this.content)}};var d=function(){var i,r;var t=this,n=t._self._c;return n("k-inside",[n("k-header",[n("span",{staticClass:"inline-flex gap-2 items-baseline"},[n("span",[t._v("Logy: "+t._s((i=t.log)==null?void 0:i.range))]),n("time",{staticClass:"text-base opacity-70 align-right"},[t._v(t._s(t.currtime))])]),n("div",{attrs:{slot:"buttons"},slot:"buttons"},[n("k-input",{ref:"input",attrs:{options:t.picklist,value:(r=t.log)==null?void 0:r.name,required:!0,type:"select"},on:{input:t.onChangeLog}})],1)]),t._l(t.logs,function([o,l]){return n("div",{key:o,staticClass:"mb-8"},[n("h2",{staticClass:"text-2xl mb-4"},[t._v(t._s(o))]),n("k-input",{staticClass:"block w-full"},t._l(l,function(a,s){return n("div",{key:s,staticClass:"w-full overflow-x-scroll"},[n("pre",{class:a.class,domProps:{innerHTML:t._s(a.content)}})])}),0)],1)})],2)},f=[],p=c(u,d,f,!1,null,"fbb61708");const m=p.exports;window.panel.plugin("adamkiss/kirby-log-viewer",{components:{"ak-log-viewer":m}})})();
