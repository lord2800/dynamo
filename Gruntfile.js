module.exports = function (grunt) {
	['phplint', 'php-analyzer', 'phpunit', 'php', 'parallelize', 'gh-pages'].forEach(function (name) { grunt.loadNpmTasks('grunt-' + name); });

};
