(function(){var a=Handlebars.template,b=Handlebars.templates=Handlebars.templates||{};b.addDrop=a(function(a,b,c,d,e){function k(a,b){var c="",d;return c+='\n					<option value="',d=a.id,d=typeof d===h?d():d,c+=i(d)+'">',d=a.date,d=typeof d===h?d():d,c+=i(d)+"</option>\n				",c}function l(a,b){var c="",d;return c+='\n					<option value="',d=a.id,d=typeof d===h?d():d,c+=i(d)+'">',d=a.name,d=typeof d===h?d():d,c+=i(d)+"</option>\n				",c}function m(a,b){var c="",d;return c+='\n					<option value="',d=a.id,d=typeof d===h?d():d,c+=i(d)+'">',d=a.name,d=typeof d===h?d():d,c+=i(d)+"</option>\n				",c}function n(a,b){var c="",d;return c+='\n					<option value="',d=a.id,d=typeof d===h?d():d,c+=i(d)+'">',d=a.zone,d=typeof d===h?d():d,c+=i(d)+": ",d=a.name,d=typeof d===h?d():d,c+=i(d)+"</option>\n				",c}c=c||a.helpers;var f="",g,h="function",i=this.escapeExpression,j=this;f+='<div tabindex="-1" role="dialog" aria-labelledby="addDropLabel">\n	<form action="/drop/" method="POST" id="addDropForm">\n		<div class="modal-header">\n			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>\n			<h3 id="addDropLabel">Add Drop</h3>\n		</div>\n		<div class="modal-body">\n			<div id="notification-modal-holder"></div>\n			<label>Event</label>\n			<select name="event">\n				',g=b.events,g=c.each.call(b,g,{hash:{},inverse:j.noop,fn:j.program(1,k,e)});if(g||g===0)f+=g;f+='\n			</select>\n			<label>Player</label>\n			<select name="player">\n				',g=b.players,g=c.each.call(b,g,{hash:{},inverse:j.noop,fn:j.program(3,l,e)});if(g||g===0)f+=g;f+='\n			</select>\n			<label>Item</label>\n			<select name="item">\n				',g=b.items,g=c.each.call(b,g,{hash:{},inverse:j.noop,fn:j.program(5,m,e)});if(g||g===0)f+=g;f+='\n			</select>\n			<label>Boss</label>\n			<select name="boss">\n				',g=b.bosses,g=c.each.call(b,g,{hash:{},inverse:j.noop,fn:j.program(7,n,e)});if(g||g===0)f+=g;return f+='\n			</select>\n		</div>\n		<div class="modal-footer">\n			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>\n			<button type="submit" class="btn btn-primary">Add</button>\n		</div>\n	</form>\n</div>\n',f})})()