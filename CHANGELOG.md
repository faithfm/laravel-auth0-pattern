# Changelog

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

No unreleased changes

## 1.0.7 - 2021-12-16

### Added

* Bug Fix: api_token authentication was broken in our projects since using this pattern library - two separate providers were actually needed - for session-vs-token guards.

* config/auth.php - brought this new (bug-fixed) file into our pattern library as an auto-cloning file (for greater consistency)

### Changed

* installation.md - updated

* CHANGELOG.md - had not been updated since project created - brought it up-to-date


## 1.0.6 - 2021-11-15

### Changed

* Bug Fix: replace hasColumns for hasColumn method in edit_user_table Migration

## 1.0.5 - 2021-11-03

MAJOR UPDATE

### Changed

* Move lots of files from clone to core

* Add comprehensive documentation

## 1.0.0 - 2021-09-28

### Added

* Created repo as new composer package from pre-existing source files to improve authentication file consistency across our Faith FM projects
