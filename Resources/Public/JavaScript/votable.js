/*
 * This file is part of the TYPO3 CMS project.
 *
 * See LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
(function($) {
	$(function() {
		$('.widget-votable').votable({

			// @todo...
			// x is the identifier of the voting, mandatory
			voting: 1,

			// y is the identifier of the user, mandatory
			user: 1,

			labels: {}
		})
	});
})(jQuery);