webpackJsonp([52],{ieB7:function(t,e){},tBAp:function(t,e,o){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=o("Dd8w"),n=o.n(s),a=(o("vAZe"),o("Au9i")),r=o("NYxO"),i=o("Brjg"),c={name:"Bind",data:function(){return{username:"",code:"",password:"",confirmPassword:""}},mounted:function(){},methods:n()({},Object(r.d)({saveToken:"signin",saveUser:"saveUser"}),{goBack:function(){this.$router.go(-1)},goProfile:function(){this.$router.go(-2)},onVerify:function(){var t=this,e=this.username;0!==e.length?this.validator.isNumber(e)?(a.Indicator.open(),i.e("86",e).then(function(o){t.onSendCode(e)},function(t){a.Indicator.close(),Object(a.Toast)(t.errorMsg)})):Object(a.Toast)("请输入正确格式的手机号"):Object(a.Toast)("请输入手机号")},onSendCode:function(t){var e=this;i.c("86",t).then(function(t){a.Indicator.close(),e.$refs.timer.start()},function(t){a.Indicator.close(),Object(a.Toast)(t.errorMsg),e.$refs.timer.stop()})},check:function(){var t=this.username,e=this.code,o=this.password,s=this.confirmPassword;return 0===t.length?(Object(a.Toast)("请输入手机号"),!1):0===e.length?(Object(a.Toast)("请输入验证码"),!1):6!==e.length?(Object(a.Toast)("请输入6位验证码"),!1):0===o.length?(Object(a.Toast)("请输入密码"),!1):o.length<6||o.length>20?(Object(a.Toast)("请输入6-20个字符的密码"),!1):0===s.length?(Object(a.Toast)("请输入确认密码"),!1):o.length!==s.length?(Object(a.Toast)("确认密码与输入密码不一致"),!1):o===s||(Object(a.Toast)("确认密码与输入密码不一致"),!1)},bind:function(){var t=this;this.check()&&(a.Indicator.open(),i.a(this.username,this.code,this.password).then(function(e){a.Indicator.close(),t.saveToken({token:e.token,isOnline:"online"}),t.saveUser(e.user),t.goHome()},function(t){a.Indicator.close(),Object(a.Toast)(t.errorMsg)}))},onSubmit:function(){this.bind()},goHome:function(){this.$router.push("/home")}})},d={render:function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"container"},[o("mt-header",{staticClass:"header",attrs:{fixed:"",title:"绑定手机"}},[o("header-item",{attrs:{slot:"left",isBack:!0},on:{onclick:t.goBack},slot:"left"})],1),t._v(" "),o("div",{staticClass:"section-wrapper top-wrapper"},[o("div",{staticClass:"input-wrapper"},[o("input",{directives:[{name:"model",rawName:"v-model",value:t.username,expression:"username"}],attrs:{placeholder:"请输入手机号",maxlength:"11"},domProps:{value:t.username},on:{input:function(e){e.target.composing||(t.username=e.target.value)}}})]),t._v(" "),o("div",{staticClass:"input-wrapper"},[o("input",{directives:[{name:"model",rawName:"v-model",value:t.code,expression:"code"}],staticClass:"bottom-input",attrs:{placeholder:"请输入手机验证码",maxlength:"6"},domProps:{value:t.code},on:{input:function(e){e.target.composing||(t.code=e.target.value)}}}),t._v(" "),o("countdown-button",{ref:"timer",staticClass:"countdown",on:{onclick:t.onVerify}})],1)]),t._v(" "),o("div",{staticClass:"section-wrapper bottom-wrapper"},[o("div",{staticClass:"input-wrapper"},[o("input",{directives:[{name:"model",rawName:"v-model",value:t.password,expression:"password"}],attrs:{type:"password",placeholder:"设置密码",maxlength:"20"},domProps:{value:t.password},on:{input:function(e){e.target.composing||(t.password=e.target.value)}}})]),t._v(" "),o("div",{staticClass:"input-wrapper"},[o("input",{directives:[{name:"model",rawName:"v-model",value:t.confirmPassword,expression:"confirmPassword"}],staticClass:"bottom-input",attrs:{type:"password",placeholder:"确认密码",maxlength:"20"},domProps:{value:t.confirmPassword},on:{input:function(e){e.target.composing||(t.confirmPassword=e.target.value)}}})])]),t._v(" "),o("label",{staticClass:"tips"},[t._v("6-20位数字/字母/符号")]),t._v(" "),o("gk-button",{staticClass:"button",attrs:{type:"primary"},on:{click:t.onSubmit}},[t._v("完成")])],1)},staticRenderFns:[]};var l=o("VU/8")(c,d,!1,function(t){o("ieB7")},"data-v-87590910",null);e.default=l.exports}});
//# sourceMappingURL=52.917b2d5d34ca5f370327.js.map