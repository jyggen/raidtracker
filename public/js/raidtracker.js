$('#attendance').tooltip({
  selector: "div[rel=tooltip]"
});

$('#login').on('click', function(){
	navigator.id.request();
});

$('#logout').on('click', function(){
	navigator.id.logout();
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
