module.exports = (grunt) ->
	grunt.initConfig
		pkg: grunt.file.readJSON("package.json")

	############################ JavaScript ############################

	##
	# JavaScript: check javascript coding guide lines
	##
		jshint:
			files: [
				"Resources/Public/JavaScript/Library/jquery.votable.js"
			]

			options:
			# options here to override JSHint defaults
				curly: true
				eqeqeq: true
				immed: true
				latedef: true
				newcap: true
				noarg: true
				sub: true
				undef: true
				boss: true
				eqnull: true
				browser: true
				loopfunc: true
				laxbreak: true
				globals:
					jQuery: true
					console: true
					module: true
					Uri: true
					define: true
					require: true
					VidiFrontend: true
					VS: true
					_: true

	##
	# JavaScript: minimize javascript
	##
		uglify:
			js:
				files: [
					src: "<%= jshint.files %>"
					dest: "Resources/Public/JavaScript/Library/jquery.votable.min.js"
				]


	########## Watcher ############
		watch:
			js:
				files: ["<%= jshint.files %>"]
				tasks: ["build"]


	########## Help ############
	grunt.registerTask "help", "Just display some helping output.", () ->
		grunt.log.writeln "Usage:"
		grunt.log.writeln ""
		grunt.log.writeln "- grunt watch        : watch your file and compile as you edit"
		grunt.log.writeln "- grunt build        : build your assets ready to be deployed"
		grunt.log.writeln ""
		grunt.log.writeln "Use grunt --help for a more verbose description of this grunt."
		return

	# Load Node module
	grunt.loadNpmTasks "grunt-contrib-uglify"
	grunt.loadNpmTasks "grunt-contrib-jshint"
	grunt.loadNpmTasks "grunt-contrib-watch"

	# Tasks
	grunt.registerTask "build", ["jshint", "uglify"]
	grunt.registerTask "default", ["help"]
	return