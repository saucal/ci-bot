# Changelog for vip-go-ci

All notable changes to this project will be documented in this file.

## [1.0.8](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.8) - To be decided

### Added
- [#186](https://github.com/Automattic/vip-go-ci/pull/186): Skip large files functionality (``skip-large-files`` and ``skip-large-files-limit`` options).
- [#189](https://github.com/Automattic/vip-go-ci/pull/189): Function ``vipgoci_options_get_starting_with()``.
- [#195](https://github.com/Automattic/vip-go-ci/pull/195): Add Sunset HTTP header detection
- [#196](https://github.com/Automattic/vip-go-ci/pull/196): Log shutdown message to IRC
- [#199](https://github.com/Automattic/vip-go-ci/pull/199): Special exit status when no implicated PR was found, retry fetching PRs
- [#198](https://github.com/Automattic/vip-go-ci/pull/198): Validate SVG files, added details to message, and formatting changed slightly, add logging
- [#201](https://github.com/Automattic/vip-go-ci/pull/201): Adding missing namespace to SVG unit-test
- [#202](https://github.com/Automattic/vip-go-ci/pull/202): Rename branch "master" to "main" in tools-init.sh

### Fixed
- [#no-issue-number](https://github.com/Automattic/vip-go-ci/commit/a8988c4b932f2f5fdf5873c33c28ae91608bbc44): Github unit test that was failing due to an out-to-date ``merge_commit_sha`` value.

## [1.0.7](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.7) - 2021-07-05

### Fixed
- [#187](https://github.com/Automattic/vip-go-ci/pull/187): Attempt to reduce GitHub API usage a bit

### Updated
- [#183](https://github.com/Automattic/vip-go-ci/pull/183): Refine help message and options
- [#185](https://github.com/Automattic/vip-go-ci/pull/185): Upgrade phpcs-variable-analysis to 2.11.1

## [1.0.6](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.6) - 2021-06-09

### Fixed
- [#152](https://github.com/Automattic/vip-go-ci/pull/152): Add status code 100 as valid GitHub post response 

### Updated
- [#174](https://github.com/Automattic/vip-go-ci/pull/174): Updating README: Add required parameters to example
- [#177](https://github.com/Automattic/vip-go-ci/pull/177): Renaming option --results-comments-sort to --review-comments-sort
- [#179](https://github.com/Automattic/vip-go-ci/pull/179): SVG scanning: Renaming source of issue
- [#181](https://github.com/Automattic/vip-go-ci/pull/181): Replace informational URL with a message

## [1.0.5](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.5) - 2021-05-18

### New

- [#169](https://github.com/Automattic/vip-go-ci/pull/169): Support for build status indication.
- [#170](https://github.com/Automattic/vip-go-ci/pull/170): Improved PHPCS logging.
- [#172](https://github.com/Automattic/vip-go-ci/pull/172): Retry PHPCS scan on failure
- [#173](https://github.com/Automattic/vip-go-ci/pull/173): Make messages in IRC queue unique before sending

## [1.0.4](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.4) - 2021-05-10

### Fixed

- [#163](https://github.com/Automattic/vip-go-ci/pull/163): New structure for requiring files
- [#166](https://github.com/Automattic/vip-go-ci/pull/166): Resolve issues with new `git diff` mechanism
- [#167](https://github.com/Automattic/vip-go-ci/pull/167): Add more unit-tests, switch to assertSame() usage

## [1.0.3](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.3) - 2021-05-03

### Temporary release, due to problems

## [1.0.2](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.2) - 2021-05-03

### Re-release to fix issues with latest-release.php, see b057081

## [1.0.1](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.1) - 2021-05-03

### Re-release to make version numbers consistent

## [1.0.0](https://github.com/Automattic/vip-go-ci/releases/tag/1.0.0) - 2021-05-03

### Fixed
- [#153](https://github.com/Automattic/vip-go-ci/pull/153): Use local git repository for sources of `git diff`, resolving problems caused by the GitHub API not returning results or skipping files for long patches (see #135).
- [#158](https://github.com/Automattic/vip-go-ci/pull/158): Fix a bug in `vipgoci_gitrepo_blame_for_file()` which can lead to PHPCS results not being posted. This will resolve #90.
- [#162](https://github.com/Automattic/vip-go-ci/pull/162): Update `testVersion` parameter in CircleCI configuration
- [#161](https://github.com/Automattic/vip-go-ci/pull/161): Update `testVersion` parameter in README.md
- [#148](https://github.com/Automattic/vip-go-ci/pull/148): Fix problem with PHPCS config files that use paths without leading `/`.
- [#150](https://github.com/Automattic/vip-go-ci/pull/150): Assign PHP linting problems a severity value.

### Updated
 - [#159](https://github.com/Automattic/vip-go-ci/pull/159): Update VIP-Coding-Standards to newer version, see #159.
 - [#143](https://github.com/Automattic/vip-go-ci/pull/143): Allow more options to be configured via repository-config file.
 - [#151](https://github.com/Automattic/vip-go-ci/pull/151): Make name of support-level field configurable.