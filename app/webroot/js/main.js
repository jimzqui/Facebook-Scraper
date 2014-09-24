/**************
 * main.js
 **************/

$(document).ready(function() {

	$('#activity-table').dataTable({
	    "bProcessing": true,
	    "bServerSide": true,
	    "sAjaxSource": "/users/all?page=" + _Server.page,
	    "aaSorting": [[ 2, "desc" ]],
	    "aoColumnDefs": [
          {"aTargets": [2,3], "bSearchable": false},
          {"aTargets": [0], "bSortable": false},
        ],
	    "fnCreatedRow": function(nRow, aData, iDataIndex){
	    	$('td:eq(0)', nRow).html(iDataIndex + 1);
			$('td:eq(1)', nRow).html('<a href="http://facebook.com/' + aData[0] + '" target="_blank">' + aData[1] + '</a>');
			$('td:eq(2)', nRow).html('<a class="activity-btn btn" data-facebookid="' + aData[0] + '" data-type="comments">' + aData[2] + '</a>');
			$('td:eq(3)', nRow).html('<a class="activity-btn btn" data-facebookid="' + aData[0] + '" data-type="likes">' + aData[3] + '</a>');

			$('td:eq(2)', nRow).addClass('aligncenter');
			$('td:eq(3)', nRow).addClass('aligncenter');

			if (aData[2] == 0) {
				$('td:eq(2)', nRow).find('.btn').addClass('disabled');
			}

			if (aData[3] == 0) {
				$('td:eq(3)', nRow).find('.btn').addClass('disabled');
			}
        },
        "fnDrawCallback": function(oSettings) {
        	if (isNaN(oSettings._iRecordsDisplay)) {
        		$('#get-data').html('Get Data').show();
        	} else {
        		$('#get-data').html('Update Data').show();
        	}
        }
	});

	$('#activity-table').on('click', '.activity-btn', function() {
		var btn = $(this);
		var facebookid = btn.data('facebookid');
		var type = btn.data('type');

		$('#acmodal-content').html('Loading...');
		$('#activityModal').modal();

		if (type == 'likes') {
			$('#acmodal-title').html('Liked Post');
		} else {
			$('#acmodal-title').html('Commented Post');
		}

		if (btn.data('json') == undefined) {
			$.ajax({
				url: '/users/activity/' + facebookid + '/' + type,
				type: 'GET',
				dataType: 'json',
				success: function (json) {
					btn.data('json', json);
					loadActivity(json);
				}
			});
		} else {
			json = btn.data('json');
			loadActivity(json);
		}
	});

	$('#top-btns').on('click', '#get-data', function() {
		window.location = '/data/scrape?page=' + _Server.page;
	});

	$('#top-btns').on('click', '#change-page', function() {
		$('#changepageModal').modal();
	});

	$('#changepageModal').on('click', '#to-newpage', function() {
		var newpage = $('#newpage').val();
		if (newpage.trim() == '') return false;

		window.location = '/data/index?page=' + newpage;
	});

	if ($('#scraper').length > 0) {
		if (_Server.exit) {
			$('#scraper .note').html('Nothing to scrape for today. <br/>Try again tomorrow.');
			$('#scraper ul').hide();
		} else {
			getPosts();
		}
	}
});

var getPosts = function() {
	var html = '<li class="ret-posts">Retrieving posts...</li>' +
	'<li class="ret-likes">Retrieving likes...</li>' +
	'<li class="ret-comments">Retrieving comments...</li>';
	$('#scraper ul').html(html);

	$.ajax({
		url: '/data/getPosts',
		type: 'POST',
		dataType: 'json',
		data: {page: _Server.page, backdate: _Server.backdate},
		success: function (json) {
			if (json.result == 'error') {
				$('#scraper .note').html('Error in accessing graph API. <br/>Please refresh page.');
				$('#scraper ul').hide();
				return false;
			}

			$('#scraper .ret-posts').addClass('done');
			$('#scraper .ret-posts').html('Done retrieving posts! (' + json.length + ')');

			_Server.total_posts = json.length;
			_Server.posts = json;
			_Server.posts_chunk = array_chunk(json, 50);

			getLikes();
			getComments();
		}
	});
}

var savePosts = function() {
	var chunk_count = 0;
	var counter = 0;

	for (var i = _Server.posts_chunk.length - 1; i >= 0; i--) {
		var chunk = _Server.posts_chunk[i];
		$.ajax({
			url: '/data/savePosts',
			type: 'POST',
			dataType: 'json',
			data: {page: _Server.page, chunk: chunk},
			success: function (json) {
				counter += json;
				chunk_count++;

				if (chunk_count >= _Server.posts_chunk.length) {
					$('#scraper .ret-posts').addClass('done');
					$('#scraper .ret-posts').html('Done saving posts to DB! (' + counter + ')');
					_Server.saved_posts = counter;
				} else {
					$('#scraper .ret-posts').html('Saving posts to DB... (' + counter + ')');
				}
			}
		});
	};
}

var getLikes = function() {
	var chunk_count = 0;
	var counter = 0;

	for (var i = _Server.posts_chunk.length - 1; i >= 0; i--) {
		var chunk = _Server.posts_chunk[i];
		_Server.likes = [];

		$.ajax({
			url: '/data/getActivity/likes',
			type: 'POST',
			dataType: 'json',
			data: {page: _Server.page, chunk: chunk},
			success: function (json) {
				if (json.result == 'error') {
					$('#scraper .note').html('Error in accessing graph API. <br/>Please refresh page.');
					$('#scraper ul').hide();
					return false;
				}

				counter += json.length;
				chunk_count++;
				_Server.likes = _Server.likes.concat(json);
				
				if (chunk_count >= _Server.posts_chunk.length) {
					$('#scraper .ret-likes').addClass('done');
					$('#scraper .ret-likes').html('Done retrieving likes! (' + counter + ')');
					_Server.total_likes = counter;
					_Server.likes_chunk = array_chunk(_Server.likes, 50);

					if ($('#scraper .ret-comments').is('.done')) {
						setTimeout(function() {
							startSaving();
						}, 1000);
					}
				} else {
					$('#scraper .ret-likes').html('Retrieving likes... (' + counter + ')');
				}
			}
		});
	};
}

var saveLikes = function() {
	var chunk_count = 0;
	var counter = 0;

	for (var i = _Server.likes_chunk.length - 1; i >= 0; i--) {
		var chunk = _Server.likes_chunk[i];

		$.ajax({
			url: '/data/saveActivity/likes',
			type: 'POST',
			dataType: 'json',
			data: {page: _Server.page, chunk: chunk},
			success: function (json) {
				counter += json;
				chunk_count++;

				if (chunk_count >= _Server.likes_chunk.length) {
					$('#scraper .ret-likes').addClass('done');
					$('#scraper .ret-likes').html('Done saving likes to DB! (' + counter + ')');
					_Server.saved_likes = json;

					if ($('#scraper .ret-comments').is('.done')) {
						saveScrapes();
					}
				} else {
					$('#scraper .ret-likes').html('Saving likes to DB... (' + counter + ')');
				}
			}
		});
	};
}

var getComments = function() {
	var chunk_count = 0;
	var counter = 0;

	for (var i = _Server.posts_chunk.length - 1; i >= 0; i--) {
		var chunk = _Server.posts_chunk[i];
		_Server.comments = [];

		$.ajax({
			url: '/data/getActivity/comments',
			type: 'POST',
			dataType: 'json',
			data: {page: _Server.page, chunk: chunk},
			success: function (json) {
				if (json.result == 'error') {
					$('#scraper .note').html('Error in accessing graph API. <br/>Please refresh page.');
					$('#scraper ul').hide();
					return false;
				}

				counter += json.length;
				chunk_count++;
				_Server.comments = _Server.comments.concat(json);
				
				if (chunk_count >= _Server.posts_chunk.length) {
					$('#scraper .ret-comments').addClass('done');
					$('#scraper .ret-comments').html('Done retrieving comments! (' + counter + ')');
					_Server.total_comments = counter;
					_Server.comments_chunk = array_chunk(_Server.comments, 50);

					if ($('#scraper .ret-likes').is('.done')) {
						setTimeout(function() {
							startSaving();
						}, 1000);
					}
				} else {
					$('#scraper .ret-comments').html('Retrieving comments... (' + counter + ')');
				}
			}
		});
	};
}

var saveComments = function() {
	var chunk_count = 0;
	var counter = 0;

	for (var i = _Server.comments_chunk.length - 1; i >= 0; i--) {
		var chunk = _Server.comments_chunk[i];

		$.ajax({
			url: '/data/saveActivity/comments',
			type: 'POST',
			dataType: 'json',
			data: {page: _Server.page, chunk: chunk},
			success: function (json) {
				counter += json;
				chunk_count++;

				if (chunk_count >= _Server.comments_chunk.length) {
					$('#scraper .ret-comments').addClass('done');
					$('#scraper .ret-comments').html('Done saving comments to DB! (' + counter + ')');
					_Server.saved_comments = json;

					if ($('#scraper .ret-likes').is('.done')) {
						saveScrapes();
					}
				} else {
					$('#scraper .ret-comments').html('Saving comments to DB... (' + counter + ')');
				}
			}
		});
	};
}

var saveScrapes = function() {
	$('#scraper .note').html('Finalizing...');

	$.ajax({
		url: '/data/saveScrapes',
		type: 'POST',
		dataType: 'json',
		data: {
			page: _Server.page, 
			saved_posts: _Server.saved_posts, 
			saved_comments: _Server.saved_comments, 
			saved_likes: _Server.saved_likes
		},
		success: function (json) {
			$('#scraper .note').html('<a href="/data/index?page=' + _Server.page + '">Done, please click here!</a>');
		}
	});
}

var startSaving = function() {
	$('#scraper .ret-posts').removeClass('done');
	$('#scraper .ret-likes').removeClass('done');
	$('#scraper .ret-comments').removeClass('done');
	$('#scraper .ret-posts').html('Saving posts to DB...');
	$('#scraper .ret-likes').html('Saving likes to DB...');
	$('#scraper .ret-comments').html('Saving comments to DB...');

	savePosts();
	saveLikes();
	saveComments();
}

var loadActivity = function(json) {
	var html = '';
	for(var i = 0; i < json.length; i++) {
		var post = json[i];
		html += '<li>' + (i+1) + ') <a target="_blank" href="' + post['link'] + '">' + post['type'] + '</a></li>';
	}

	$('#acmodal-content').html(html);
}

var array_chunk = function(input, size, preserve_keys) {
  var x, p = '',
    i = 0,
    c = -1,
    l = input.length || 0,
    n = [];

  if (size < 1) {
    return null;
  }

  if (Object.prototype.toString.call(input) === '[object Array]') {
    if (preserve_keys) {
      while (i < l) {
        (x = i % size) ? n[c][i] = input[i] : n[++c] = {}, n[c][i] = input[i];
        i++;
      }
    } else {
      while (i < l) {
        (x = i % size) ? n[c][x] = input[i] : n[++c] = [input[i]];
        i++;
      }
    }
  } else {
    if (preserve_keys) {
      for (p in input) {
        if (input.hasOwnProperty(p)) {
          (x = i % size) ? n[c][p] = input[p] : n[++c] = {}, n[c][p] = input[p];
          i++;
        }
      }
    } else {
      for (p in input) {
        if (input.hasOwnProperty(p)) {
          (x = i % size) ? n[c][x] = input[p] : n[++c] = [input[p]];
          i++;
        }
      }
    }
  }
  return n;
}