module.exports = function (grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		watch: {
			scripts: {
				files: ['assets/scripts/**/*.js'],
				tasks: ['uglify'],
			},
			css: {
				files: 'assets/css/**/*.css',
				tasks: ['cssmin'],
			},
		},

		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			target: {
				files: {
					'assets/css/min/dx-crm-activity-log.min.css': ['assets/css/src/dx-crm-activity-log.css'],
					'assets/css/min/dx-crm-admin.min.css': ['assets/css/src/dx-crm-admin.css'],
					'assets/css/min/dx-crm-dashboard.min.css': ['assets/css/src/dx-crm-dashboard.css'],
					'assets/css/min/dx-crm-post-view.min.css': ['assets/css/src/dx-crm-post-view.css'],
					'assets/css/min/dx-crm-report.min.css': ['assets/css/src/dx-crm-report.css'],
					'assets/css/min/dx-crm-templates.min.css': ['assets/css/src/dx-crm-templates.css'],
					'assets/css/min/meta-box.min.css': ['assets/css/src/meta-box.css'],
					'assets/css/min/progress-rating.min.css': ['assets/css/src/progress-rating.css']
				}
			}
		},
		
		uglify: {
			target: {
				files: { 					
					'assets/scripts/min/custom.min.js': ['assets/scripts/src/custom.js'],
					'assets/scripts/min/dx-crm-admin.min.js': ['assets/scripts/src/dx-crm-admin.js'],
					'assets/scripts/min/dx-crm-livequery.min.js': ['assets/scripts/src/dx-crm-livequery.js'],
					'assets/scripts/min/dx-crm-report.min.js': ['assets/scripts/src/dx-crm-report.js'],
					'assets/scripts/min/dx-crm-sort.min.js': ['assets/scripts/src/dx-crm-sort.js'],
					'assets/scripts/min/dx-crm-template.min.js': ['assets/scripts/src/dx-crm-template.js'],
					'assets/scripts/min/jquery.barrating.min.js': ['assets/scripts/src/jquery.barrating.js'],
					'assets/scripts/min/meta-box.min.js': ['assets/scripts/src/meta-box.js'],
				}
			}
		}

	});

	grunt.loadNpmTasks('grunt-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	grunt.registerTask('default', ['watch']);
	grunt.registerTask('css', ['cssmin']);
	grunt.registerTask('minify', ['cssmin']);
	grunt.registerTask('minifyjs', ['uglify']);
};