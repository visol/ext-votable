/*jshint -W054 */
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

		if (settings.isVoteOpen === true) {
			createDom(this, settings);
			attachHandler(settings);
		} else {
			if (window.console) {
				console.log('[INFO] Votes are currently closed.')
			}
		}

		// allow jQuery chaining
		return this;
	};

	var cache = {};

	/**
	 * @param {object} nodes
	 * @param {object} settings
	 * @returns string
	 */
	function createDom(nodes, settings) {
		// Traverse all nodes.
		nodes.each(function(index, element) {

			var content = '';
			var currentObject = parseInt($(element).data('object'));

			if (!settings.userIsAuthenticated) {

				// User is still anonymous and need to log-in before voting.
				content = render(settings.templateIdentifier, {
					status: 'vote-authentication-required',
					enabledOrDisabled: 'disabled',
					object: '',
					tooltip: '',
					voting: settings.label.voting,
					text: settings.label.authenticationRequired
				});

			} else if ($.inArray(currentObject, settings.votedItems) > -1) {

				// Already voted
				content = render(settings.templateIdentifier, {
					status: 'vote-done hasTooltip',
					enabledOrDisabled: 'disabled',
					object: $(element).data('object'),
					tooltip: settings.label.tooltip,
					voting: settings.label.voting,
					text: settings.label.alreadyVoted
				});
			} else {

				// Not yet voted
				content = render(settings.templateIdentifier, {
					status: 'vote-ready hasTooltip',
					enabledOrDisabled: 'enabled',
					object: $(element).data('object'),
					tooltip: settings.label.tooltip,
					voting: settings.label.voting,
					text: settings.label.vote
				});
			}

			$(this).html(content);
		});
	}

	/**
	 * @param {object} settings
	 * @returns string
	 */
	function attachHandler(settings) {
		$('.vote-authentication-required').off('click').on('click', settings.whenUserIsLoggedOff);
		$('.vote-done').on('click', function(e) {
			e.preventDefault();
		});
		$('.vote-ready').on('click', function(e) {

			e.preventDefault();

			// Display waiting message.
			$(this).hide().prev().show();

			var data = {};
			data['tx_votable_pi1[vote]'] = $(this).data('object');
			data['value'] = $(this).data('value');
			data['contentElement'] = settings.contentElement;
			$.ajax({
				url: window.location.pathname + '?type=1451549782',
				data: data,
				context: this
			}).done(function() {
				$(this)
					.show()
					.removeClass('vote-ready')
					.addClass('vote-done')
					.html('<i class="evicon-like voting-disabled"></i>' + settings.label.alreadyVoted + '</a>')

					// hide waiting message again.
					.prev()
					.hide();

			}).fail(function() {
				console.log('Something went wrong when voting!');
			});

		});
	}

	/**
	 * Basic template engine.
	 *
	 * @param {string} templateIdentifier
	 * @param {object} data
	 * @returns string
	 */
	function render(templateIdentifier, data) {

		var template = $('#' + templateIdentifier).html() || 'Missing vote template';

		//cache[templateIdentifier] // let see if necessary ?

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
		contentElement: 0,
		votedItems: [],
		isVoteOpen: true,
		label: {
			authenticationRequired: 'log-in to vote',
			alreadyVoted: 'You already voted',
			tooltip: 'You can vote only once for this item.',
			voting: 'Voting...',
			vote: 'vote'
		}
	};

}(jQuery));