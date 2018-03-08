/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    uglify: {
      detect: {
        files: {
          'dist/js/detect.min.js': ['js/detect.js']
        }
      }
    },
    connect: {
      detect: {
        options: {
          keepalive: true
        }
      }
    },
    less: {
      detect: {
        options: {
          compress: true
        },
        files: {
          'dist/css/detect.min.css': 'css/detect.less'
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-connect');

  // Default task.
  grunt.registerTask('default', ['uglify', 'less', 'connect']);
};
