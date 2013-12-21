module.exports = function (grunt) {
	['phpcs', 'phplint', 'php-analyzer', 'phpunit', 'php', 'parallelize', 'gh-pages', 'contrib-watch']
		.forEach(function (name) { grunt.loadNpmTasks('grunt-' + name); });

	grunt.initConfig({
		php: {
			app: { options: { port: 8080, base: 'public', open: true, keepalive: false } }
		},
		phpcs: {
			app: { dir: 'src' },
			options: { bin: 'vendor/bin/phpcs', standard: 'PSR1' }
		},
		phplint: {
			options: { swapPath: '/tmp' },
			app: ['src/**/*.php', 'index.php']
		},
		phpunit: {
			unit: { dir: 'tests/' },
			options: {
				bin: 'vendor/bin/phpunit',
				bootstrap: 'tests/Bootstrap.php',
				colors: true,
				testdox: true
			}
		},
		php_analyzer: {
			options: { bin: 'vendor/bin/phpalizer' },
			app: { dir: 'src' }
		},
		watch: {
			test: {
				files: ['tests/**/*.php'],
				tasks: ['phplint', 'phpunit']
			},
			livereload: {
				options: { livereload: true },
				files: ['src/**/*.php'],
				tasks: []
			}
		}
	});

	grunt.registerTask('precommit', ['phplint', 'phpunit']);
	grunt.registerTask('test', ['phplint', 'phpcs', 'phpunit', 'php_analyzer']);
	grunt.registerTask('serve', ['php', 'watch']);
	grunt.registerTask('default', ['phplint', 'phpcs']);
};
