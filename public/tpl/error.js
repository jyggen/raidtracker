(function(){var a=Handlebars.template,b=Handlebars.templates=Handlebars.templates||{};b.error=a(function(a,b,c,d,e){c=c||a.helpers;var f="",g,h,i="function",j=this.escapeExpression;return f+='<div class="alert alert-error">\n	<button type="button" class="close" data-dismiss="alert">×</button>\n	<strong>Error!</strong> ',h=c.message,h?g=h.call(b,{hash:{}}):(g=b.message,g=typeof g===i?g():g),f+=j(g)+"\n</div>",f})})()