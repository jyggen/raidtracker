function errorHandler(xhr, status, err) {

	errorMsg = xhr.responseText;

	if(errorMsg == '') {
		errorMsg = 'An unknown error has occured. Please try again.'
	} else {
		errorMsg = errorMsg.substring(1, (errorMsg.length-1));
	}

	$('#notification-holder').html(Handlebars.templates.error({message: errorMsg}));
	$.scrollTo('#home');

}

function errorModalHandler(xhr, status, error) {

	errorMsg = xhr.responseText;

	if(errorMsg == '') {
		errorMsg = 'An unknown error has occured. Please try again.'
	} else {
		errorMsg = errorMsg.substring(1, (errorMsg.length-1));
	}

	$('#notification-modal-holder').html(Handlebars.templates.error({message: errorMsg}));

}

function successHandler(res, status, xhr) {

	$('#modal-holder').modal('hide');

	if(res == '') {
		res = 'Operation successfully executed.'
	}

	$('#notification-holder').html(Handlebars.templates.success({message: res}));
	$.scrollTo('#home');

}

function submitHandler(event) {
	event.preventDefault();
	$.ajax({
		data   : $(this).serialize(),
		error  : errorModalHandler,
		success: successHandler,
		type   : 'POST',
		url    : $(this).attr('action')
	});
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

$('#addEvent').on('click', function() {
	$.ajax({
		type   : 'GET',
		url    : '/event/new',
		error  : errorHandler,
		success: function(res, status, xhr) {
			$('#modal-holder').html(Handlebars.templates.addEvent(res));
			$('#modal-holder').modal();
		},
	});
});

$('#addItem').on('click', function() {
	$('#modal-holder').html(Handlebars.templates.addItem());
	$('#modal-holder').modal();
});

$('#modal-holder').on('hidden', function () {
	$('#modal-holder').html('');
});

$('#modal-holder').on('submit', '#addDropForm', submitHandler);
$('#modal-holder').on('submit', '#addEventForm', submitHandler);
$('#modal-holder').on('submit', '#addItemForm', submitHandler);

navigator.id.watch({
	loggedInUser: currentUser,
	onlogin: function(assertion) {
		$.ajax({
			type   : 'POST',
			url    : '/user/login',
			data   : {assertion: assertion},
			success: function(res, status, xhr) { window.location.reload(); },
			error  : errorHandler
		});
	},
	onlogout: function() {
		$.ajax({
			type   : 'POST',
			url    : '/user/logout',
			success: function(res, status, xhr) { window.location.reload(); },
			error  : errorHandler
		});
	}
});
