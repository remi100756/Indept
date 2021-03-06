module.exports = function(grunt) {
    grunt.initConfig({
        uglify: {
            options: {
                preserveComments: 'some'
            },
            my_target: {
                files: {
                    'web/js/editeur.min.js': [
                        'web/js/src/editeur.js',
                        'web/js/src/statistiques.js',
                        'web/js/src/general.js',
                        'web/js/src/tables.js'
                    ],
                    'web/js/home.min.js' : [
                        'web/js/src/home.js',
                        'web/js/src/general.js',
                        'web/js/src/statistiques.js'
                    ],
                    'web/js/material.min.js' : [
                        'web/js/material.min.js',
                        'web/js/src/ripples.min.js'
                    ]
                }
            }
        },
        cssmin: {
            options: { },
            combine: {
                files: {
                    'web/css/home.min.css': [
                        'web/css/src/home.css',
                        'web/css/src/global.css'
                    ],
                    'web/css/editeur.min.css': [
                        'web/css/src/editeur.css',
                        'web/css/src/global.css',
                        'web/css/src/tables.css'
                    ],
                    'web/css/login.min.css': [
                        'web/css/src/login.css'
                    ],
                    'web/css/material.min.css': [
                        'web/css/src/material-wfont.min.css',
                        'web/css/src/ripples.min.css'
                    ]
                }
            }
        },
        jshint: {
            all: ['Gruntfile.js', 'web/js/src/*.js']
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-jshint');

    grunt.registerTask('default', ['uglify', 'cssmin', 'jshint']);
};