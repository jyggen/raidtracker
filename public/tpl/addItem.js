(function(){var a=Handlebars.template,b=Handlebars.templates=Handlebars.templates||{};b.addItem=a(function(a,b,c,d,e){return c=c||a.helpers,'<div tabindex="-1" role="dialog" aria-labelledby="addItemLabel">\n	<form action="/item/" method="POST" id="addItemForm">\n		<div class="modal-header">\n			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>\n			<h3 id="addItemLabel">Add Item</h3>\n		</div>\n		<div class="modal-body">\n			<div id="notification-modal-holder"></div>\n			<label>Item ID</label>\n			<input name="id" type="text">\n		</div>\n		<div class="modal-footer">\n			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>\n			<button type="submit" class="btn btn-primary">Add</button>\n		</div>\n	</form>\n</div>\n'})})()