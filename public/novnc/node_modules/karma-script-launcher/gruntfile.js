module.exports = function (grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    pkgFile: 'package.json',

    eslint: {
      target: [
        '*.js'
      ]
    },

    conventionalChangelog: {
      release: {
        options: {
          changelogOpts: {
            preset: 'angular'
          }
        },
        src: 'CHANGELOG.md'
      }
    },

    conventionalGithubReleaser: {
      release: {
        options: {
          auth: {
            type: 'oauth',
            token: process.env.GH_TOKEN
          },
          changelogOpts: {
            preset: 'angular'
          }
        }
      }
    },

    bump: {
      options: {
        commitMessage: 'chore: release v%VERSION%',
        pushTo: 'upstream',
        commitFiles: [
          'package.json',
          'CHANGELOG.md'
        ]
      }
    },

    'npm-contributors': {
      options: {
        commitMessage: 'chore: Update contributors'
      }
    }
  })

  require('load-grunt-tasks')(grunt)

  grunt.registerTask('default', ['eslint'])

  grunt.registerTask('release', 'Bump the version and publish to npm.', function (type) {
    grunt.task.run([
      'npm-contributors',
      'bump:' + (type || 'patch') + ':bump-only',
      'conventionalChangelog',
      'bump-commit',
      'conventionalGithubReleaser',
      'npm-publish'
    ])
  })
}
