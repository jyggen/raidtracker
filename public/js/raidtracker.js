function errorHandler(xhr, status, err) {

	errorMsg = xhr.responseText;

	if(errorMsg == '')
		errorMsg = 'An unknown error has occured. Please try again.'

	$('#notification-holder').html(Handlebars.templates.error({message: errorMsg}));
	$.scrollTo('#home');

}

$('#attendance').tooltip({
  selector: "div[rel=tooltip]"
});

$('#login').on('click', function(){
	navigator.id.request();
});

$('#logout').on('click', function(){
	navigator.id.logout();
});

$('#addDrop').on('click', function() {
	$.ajax({
		type   : 'GET',
		url    : '/drop/new',
		error  : errorHandler,
		success: function(res, status, xhr) {
			$('#modal-holder').html(Handlebars.templates.addDrop(res));
			$('#modal-holder').modal();
		},
	});
});

$('#modal-holder').on('hidden', function () {
	$('#modal-holder').html('');
});

$('#addDropForm').on('submit', function(event) {
	event.preventDefault();
	$.ajax({
		type   : 'POST',
		url    : $(this).attr('action'),
		data   : $(this).serialize(),
		success: function(res, status, xhr) { window.location.reload(); },
		error  : function(xhr, status, err) { alert("drop failure"); }
	});
});

$('#addEventForm').on('submit', function(event) {
	event.preventDefault();
	$.ajax({
		type   : 'POST',
		url    : $(this).attr('action'),
		data   : $(this).serialize(),
		success: function(res, status, xhr) { window.location.reload(); },
		error  : function(xhr, status, err) { alert("event failure"); }
	});
});

$('#addItemForm').on('submit', function(event) {
	event.preventDefault();
	$.ajax({
		type   : 'POST',
		url    : $(this).attr('action'),
		data   : $(this).serialize(),
		success: function(res, status, xhr) { window.location.reload(); },
		error  : function(xhr, status, err) { alert("item failure"); }
	});
});

navigator.id.watch({
	loggedInUser: currentUser,
	onlogin: function(assertion) {
		$.ajax({
			type   : 'POST',
			url    : '/user/login',
			data   : {assertion: assertion},
			success: function(res, status, xhr) { window.location.reload(); },
			error  : function(xhr, status, err) { alert("login failure"); }
		});
	},
	onlogout: function() {
		$.ajax({
			type   : 'POST',
			url    : '/user/logout',
			success: function(res, status, xhr) { /* window.location.reload();*/ },
			error  : function(xhr, status, err) { alert("logout failure"); }
		});
	}
});
