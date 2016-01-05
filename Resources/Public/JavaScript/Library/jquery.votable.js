/*
 * This file is part of the TYPO3 CMS project.
 *
 * See LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
(function($) {

	$.fn.votable = function(options) {

		// Establish our default settings
		var settings = $.extend($.fn.votable.options, options);

		// Traverse all nodes
		this.each(function(index, element) {

			var content = '';
			var currentObject = parseInt($(element).data('object'));

			if (!settings.userIsAuthenticated) {

				// User is still anonymous and need to log-in before voting.
				content = render(settings.templateIdentifier, {
					status: 'vote-authentication-required',
					enabledOrDisabled: 'disabled',
					tooltip: '',
					text: settings.label.authenticationRequired
				});

			} else if ($.inArray(currentObject, settings.votedItems) > -1) {

				// Already voted
				content = render(settings.templateIdentifier, {
					status: 'vote-done hasTooltip',
					enabledOrDisabled: 'disabled',
					tooltip: settings.label.tooltip,
					text: settings.label.alreadyVoted
				});
			} else {

				// Not yet voted
				content = render(settings.templateIdentifier, {
					status: 'vote-ready hasTooltip',
					enabledOrDisabled: 'enabled',
					tooltip: settings.label.tooltip,
					text: settings.label.vote
				});
			}

			$(this).html(content);
		});

		// Callback function
		//settings.whenUserIsLoggedOff.call();

		$('.vote-authentication-required').off('click').on('click', settings.whenUserIsLoggedOff);
		$('.vote-ready').off('click').on('click', function(e) {

			e.preventDefault();

			// display loading
			$(this).html('loading...');

			//$.ajax({
			//	url: window.location.pathname + '?type=1451549782',
			//	context: this
			//}).done(function() {
			//	$(this)
			//		.removeClass('vote-ready')
			//		.addClass('vote-done')
			//		.html('<i class="evicon-like voting-disabled"></i>' + settings.label.alreadyVoted + '</a>')
			//	$( this ).addClass( "done" );
			//}).fail(function() {
			//	console.log('Something went wrong when voting!');
			//});

		});

		//attachHandler();

		// allow jQuery chaining
		return this;
	};

	//
	//function attachHandler() {
	//	console.log(1231231111);
	//}

	var cache = {};

	/**
	 * Render a template
	 *
	 * @param {string} templateIdentifier
	 * @param {object} data
	 * @returns string
	 */
	function render(templateIdentifier, data) {

		var template = $('#' + templateIdentifier).html() || '';

		//cache[templateIdentifier] // @todo ?

		var fn =
			// Generate a reusable function that will serve as a template
			// generator (and which will be cached).
			new Function("obj",
				"var p=[],print=function(){p.push.apply(p,arguments);};" +

					// Introduce the data as local variables using with(){}
				"with(obj){p.push('" +
					// Convert the template into pure JavaScript
				template
					.replace(/[\r\t\n]/g, " ")
					.split("<%").join("\t")
					.replace(/((^|%>)[^\t]*)'/g, "$1\r")
					.replace(/\t=(.*?)%>/g, "',$1,'")
					.split("\t").join("');")
					.split("%>").join("p.push('")
					.split("\r").join("\\'")
				+ "');}return p.join('');");

		// Provide some basic currying to the user
		return data ? fn(data) : fn;
	}


	// Default options.
	$.fn.votable.options = {
		whenUserIsLoggedOff: function() {},
		userIsAuthenticated: false,
		templateIdentifier: 'votable-template',
		votedItems: [],
		label: {
			authenticationRequired: 'log-in to vote',
			alreadyVoted: 'You already voted',
			tooltip: 'You can vote only once for this item.',
			vote: 'vote'
		}
	};

}(jQuery));