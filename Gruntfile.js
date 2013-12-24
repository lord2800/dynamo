module.exports = function (grunt) {
	['phplint', 'phpcs', 'php-analyzer', 'phpunit', 'php', 'parallelize', 'gh-pages', 'contrib-watch']
		.forEach(function (name) { grunt.loadNpmTasks('grunt-' + name); });

	grunt.initConfig({
		parallelize: { phplint: { app: require('os').cpus().length } },
		phplint: {
			options: { swapPath: '/tmp' },
			app: ['src/**/*.php', 'tests/**/*.php', 'index.php']
		},
		phpcs: {
			app: { dir: 'src' },
			options: { bin: 'vendor/bin/phpcs', standard: 'PSR1' }
		},
		// seemingly broken?
		// TODO figure out what's wrong and re-enable
		// php_analyzer: {
		// 	options: { bin: 'vendor/bin/phpalizer' },
		// 	app: { dir: 'src' }
		// },
		phpunit: {
			unit: { dir: 'tests/' },
			options: {
				bin: 'vendor/bin/phpunit',
				configuration: 'phpunit.xml'
			}
		},
		php: {
			app: { options: { port: 8080, base: '.', open: true, keepalive: false } }
		},
		'gh-pages': {
			options: {
				base: 'docs',
				message: 'Auto-commit via Travis [ci-skip]',
				repo: 'https://' + process.env.GH_OAUTH_TOKEN + '@github.com/lord2800/dynamo.git',
				silent: true,
				user: {
					name: 'Travis CI',
					email: 'lord2800@gmail.com'
				}
			},
			src: ['**']
		},
		watch: {
			test: {
				files: ['tests/**/*.php'],
				tasks: ['phplint', 'phpcs', 'phpunit']
			},
			livereload: {
				options: { livereload: 4738 },
				files: ['src/**/*.php', 'index.php'],
				tasks: []
			}
		}
	});

	grunt.registerTask('precommit', ['parallelize:phplint', 'phpcs', 'phpunit']);
	grunt.registerTask('test', ['phplint', 'phpcs', /*'php_analyzer',*/ 'phpunit']);
	grunt.registerTask('serve', ['php', 'watch']);
	grunt.registerTask('default', ['test']);
};
