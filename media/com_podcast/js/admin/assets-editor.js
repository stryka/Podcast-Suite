// (c) 2012 Cory Webb Media, GNU/GPLv2 license.

window.addEvent('domready', function () {
	Assets.init();
});

var Assets = {
	assets: null,
	token: null,
	folders: [],
	pagination: null,
	search_string: '',
	asset_list: 'media_list',
	asset_template: 'asset_template',
	asset_template_html: null,
	pagination_holder: 'media_pagination',
	pagination_template: 'pagination_template',
	pagination_template_html: null,
	folder_root: null,
	folder_current: null,
	storage_engine: null,
	url_root: null,
	editor: null
};

// Loads the assets
Assets.init = function() {
	Assets.search_string = $('search_assets').get('value');
	Assets.page(1);
	$('search_assets').addEvent('keyup', function() {
		Assets.search_string = this.get('value');
		Assets.page(Assets.pagination.current);
	});

	$('search_clear').addEvent('click', function() {
		Assets.search_string = '';
		$('search_assets').set('value', '');
		Assets.page(1);
	});

	Assets.folder_current = Assets.folder_root;
};

// Render the assets returned from the server
Assets.render = function() {
	$(Assets.asset_list).empty();
	for (var i=0; i < Assets.assets.length; i++) {
		Assets.add_item(Assets.assets[i]);
	}

	// Add click events to the freshly rendered checkboxes
	$$('.asset_checkbox').addEvent('click', Assets.toggle_check);

	Assets.setup_pagination();
};

// Adds an asset item to the table
Assets.add_item = function (asset) {
	// render markup
	asset.asset_enclosure_length = Assets.convertLength(asset.asset_enclosure_length);
	
	var asset_html = Mustache.to_html(Assets.asset_template_html, asset);
	$(Assets.asset_list).innerHTML += asset_html;
};

// Requests a new page
Assets.page = function(page) {
	new Request.JSON({
		url: 'index.php',
		onSuccess: function	 (results) {
			Assets.assets = results.items;
			Assets.pagination = results.pagination;
			Assets.render();
			$$('a.title').addEvent('click', function() {
				window.parent.jInsertEditorText('{podcast_media '+this.get('data-id')+'}', Assets.editor);
				window.parent.SqueezeBox.close();
			});
		}
	}).get({
		option: 'com_podcast',
		page: page - 1,
		search: Assets.search_string,
		format: 'json',
		task: 'assets.list_available_assets'
	});
	Assets.asset_template_html = $(Assets.asset_template).innerHTML;
	Assets.pagination_template_html = $(Assets.pagination_template).innerHTML;
};

// Setup pagination
Assets.setup_pagination = function() {
	$(Assets.pagination_holder).innerHTML = Mustache.to_html(Assets.pagination_template_html, Assets.pagination);
	
	var pages = Assets.pagination;
	
	if (pages.current == '1') {
		$('page_start').getElement('div').set('html', '<span>'+$('page_start').getElement('a').get('text'));
		$('page_prev').getElement('div').set('html', '<span>'+$('page_prev').getElement('a').get('text'));
	}
	
	for (i = 1; i <= pages.total; i++) {
		if (i == pages.current) {
			$('page_pages').getElement('div').innerHTML += '<span>'+i+'</span>';
		}
		else {
			$('page_pages').getElement('div').innerHTML += '<a href="#assets" onclick="Assets.page('+i+');" title="'+i+'">'+i+'</a>';
		}
	}
	
	if (pages.current == pages.total) {
		$('page_last').getElement('div').set('html', '<span>'+$('page_last').getElement('a').get('text'));
		$('page_next').getElement('div').set('html', '<span>'+$('page_next').getElement('a').get('text'));
	}
};

// Function to change length to human readable values
Assets.convertLength = function(bytes) {
	if (!bytes) bytes = 0;
	var kilobyte = 1024;
	var megabyte = kilobyte * 1024;
	var gigabyte = megabyte * 1024;
	var terabyte = gigabyte * 1024;
	var precision = 2;

	if ((bytes >= 0) && (bytes < kilobyte)) {
		return bytes + ' B';
	} else if ((bytes >= kilobyte) && (bytes < megabyte)) {
		return (bytes / kilobyte).toFixed(precision) + ' KB';
	} else if ((bytes >= megabyte) && (bytes < gigabyte)) {
		return (bytes / megabyte).toFixed(precision) + ' MB';
	} else if ((bytes >= gigabyte) && (bytes < terabyte)) {
		return (bytes / gigabyte).toFixed(precision) + ' GB';
	} else {
		return bytes + ' B';
	}
};

Assets.file_tree = function(node, state) {
	var filter = "/" + node.text;
	var current = node;

	if (Assets.storage_engine == 'amazons3') {
		filter = Assets.filterS3(node);
		Assets.folder_current = node.text;
	} else {
		filter = Assets.recrusive_file_path(current, Assets.folder_root) + '/';
		Assets.folder_current = filter;
	}

	if (filter === Assets.folder_root) {
		filter = '';
	}
	
	$("search_assets").set("value", filter);
	

	Assets.search_string = filter;
	Assets.page(1);
};

Assets.rebuild_tree = function() {
	new Request.HTML({
		url: 'index.php?option=com_podcast&view=assets&format=raw&layout=default_folders',
		onSuccess: function  (responseTree, responseElements, responseHTML, responseJavaScript) {
			$('folders').innerHTML = '';
			var folders_tree = new Element('ul', {id: 'folders_tree', html: responseHTML});
			folders_tree.inject($('folders'), 'after');
			tree = new MooTreeControl({ div: 'folders', mode: 'folders', grid: true, theme: Assets.url_root + '/media/system/images/mootree.gif', onClick: Assets.file_tree},{ text: 'Root', open: true});
			tree.adopt('folders_tree');
		}
	}).get();
};

Assets.add_folder = function() {

  var request_vars = {
		option: 'com_podcast',
		format: 'json',
		task: 'assets.create_folder',
		folder: Assets.folder_current + '/' + $('new_folder_name').get('value')
  };

  request_vars[Assets.token] = 1;

  new Request.JSON({
		url: 'index.php',
		onSuccess: function	 (result) {
			Assets.rebuild_tree();
		},
		onFailure: function	 (xhr) {
			throw xhr;
		}
	}).post(request_vars);
};

Assets.filterS3 = function(node) {
	var filter = Assets.folder_root;
	return filter.replace("bucket", node.text);
};

Assets.recrusive_file_path = function (node, path) {
	if (node.parent === null) {
		return path; 
	}
	
	return path + Assets.recrusive_file_path(node.parent, '') + '/' + node.text;
};

Assets.toggle_checks = function	 (check_all) {

	if (check_all.target.checked === true) {
		$$('.asset_checkbox').each(function (item) {
			item.checked = true;
		}); 
		
		$('boxchecked').set('value', $$('.asset_checkbox').length);
	} else {
		$$('.asset_checkbox').each(function (item) {
			item.checked = false;
		});
		
		$('boxchecked').set('value', '0');
	}
};

Assets.toggle_check = function	(check) {
	var checked = parseInt($('boxchecked').get('value'), 10);

	if (check.target.checked === true) {
		checked++;
	} else {
		checked--;
	}

	$('boxchecked').set('value', checked);
};