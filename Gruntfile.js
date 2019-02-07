'use strict';
module.exports = function(grunt) {

	grunt.initConfig({
		uglify: {
			dist: {
				files: {
					'includes/js/yikes-mailchimp-eu-admin-functions.min.js': [
						'includes/js/yikes-mailchimp-eu-admin-functions.js'
					],
					'includes/js/yikes-mailchimp-front-end-form-functions.min.js': [
						'includes/js/yikes-mailchimp-front-end-form-functions.js'
					],
				}
			}
		},
		cssmin: {
			target: {
				files: [
					{
						expand: true,
						cwd: 'includes/css',
						src: [
							'yikes-mailchimp-eu-law-extension-frontend.css',
							'yikes-mailchimp-eu-law-icons.css',
						],
						dest: 'includes/css',
						ext: '.min.css'
					}
				]
			}
		},
		pot: {
			options: {
				text_domain: 'eu-opt-in-compliance-for-mailchimp', 
				dest: 'languages/', 
		        keywords: [
		        	'__:1',
		        	'_e:1',
					'_x:1,2c',
					'esc_html__:1',
					'esc_html_e:1',
					'esc_html_x:1,2c',
					'esc_attr__:1', 
					'esc_attr_e:1', 
					'esc_attr_x:1,2c', 
					'_ex:1,2c',
					'_n:1,2', 
					'_nx:1,2,4c',
					'_n_noop:1,2',
					'_nx_noop:1,2,3c'
				],
			},
			files: {
				src:  [ '**/*.php' ],
				expand: true,
			}
		}

	});

	// Load tasks.
	grunt.loadNpmTasks( 'grunt-contrib-uglify-es' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-pot' );

	// Register task.
	grunt.registerTask( 'default', [
		'uglify',
		'cssmin'
	]);

};
