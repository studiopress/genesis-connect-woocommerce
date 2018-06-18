module.exports = function(grunt) {
  grunt.initConfig({
    phpcs: {
      application: {
          src: ['./*.php']
      },
      options: {
          bin: 'vendor/bin/phpcs',
      }
    }
  });

  grunt.loadNpmTasks('grunt-phpcs');
  grunt.registerTask('default', ['phpcs']);
};
