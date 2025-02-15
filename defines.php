<?php
/**
 * Various defines for vip-go-ci
 *
 * @package Automattic/vip-go-ci
 */

declare(strict_types=1);

/*
 * Version number.
 */
define( 'VIPGOCI_VERSION', '1.2.3' );

/*
 * Define minimum version requirements.
 */
define( 'VIPGOCI_GIT_VERSION_MINIMUM', '2.10' );
define( 'VIPGOCI_PHP_VERSION_MINIMUM', '7.3.0' );

/*
 * Client-ID for curl-requests, etc.
 */
define( 'VIPGOCI_CLIENT_ID', 'automattic-vip-go-ci' );
define( 'VIPGOCI_SYNTAX_ERROR_STR', 'PHP Syntax Errors Found' );
define( 'VIPGOCI_GITHUB_ERROR_STR', 'GitHub API communication error. Please contact a human.' );

/*
 * Base URLs for GitHub
 */
define( 'VIPGOCI_GITHUB_WEB_BASE_URL', 'https://github.com' );

/* Define if not defined. Unit-tests can define this for testing. */
if ( ! defined( 'VIPGOCI_GITHUB_BASE_URL' ) ) {
	define( 'VIPGOCI_GITHUB_BASE_URL', 'https://api.github.com' );
}

/*
 * Timeout constant for HTTP APIs.
 */
define( 'VIPGOCI_HTTP_API_LONG_TIMEOUT', 20 );
define( 'VIPGOCI_HTTP_API_SHORT_TIMEOUT', 5 );

/*
 * Various messages.
 */
define( 'VIPGOCI_FILE_IS_APPROVED_MSG', 'File is approved in review database (hashes-to-hashes).' );

define(
	'VIPGOCI_REVIEW_COMMENTS_TOTAL_MAX',
	'Total number of active review comments per ' .
					'pull request has been reached and some ' .
					'comments might not appear as a result. ' .
					'Please resolve some issues to see more'
);

define(
	'VIPGOCI_PHPCS_INVALID_SNIFFS',
	'Invalid PHPCS sniff(s) specified in ' .
					'options or options file. Those have ' .
					'been ignored temporarily. Please ' .
					'update the options so that scanning ' .
					'can continue as expected. '
);

define(
	'VIPGOCI_PHPCS_INVALID_SNIFFS_CONT',
	'<br />' .
					PHP_EOL . PHP_EOL .
					'* Option name: `%s`' . PHP_EOL .
					'* Invalid sniff(s): `%s`' . PHP_EOL
);

define(
	'VIPGOCI_PHPCS_DUPLICATE_SNIFFS',
	'Sniff(s) has been found in duplicate in ' .
					'options or options file. Those have ' .
					'been ignored temporarily. Please ' .
					'update the options so that scanning ' .
					'can continue as expected. ' .
					'<br /> '
);

define(
	'VIPGOCI_PHPCS_DUPLICATE_SNIFFS_CONT',
	'<br />' .
					PHP_EOL . PHP_EOL .
					'* Options: `%s` and `%s`' . PHP_EOL .
					'* Sniff(s) in duplicate: `%s`' . PHP_EOL .
					'<br />'
);

define(
	'VIPGOCI_OUT_OF_MEMORY_ERROR',
	'Unable to analyze the pull request due to resource constraints. The pull request may be too large to process. Please try submitting a smaller pull request'
);

define(
	'VIPGOCI_NO_ISSUES_FOUND_MSG_AND_NO_REVIEWS',
	'No issues were found to report when scanning latest commit'
);

define(
	'VIPGOCI_NO_ISSUES_FOUND_MSG_AND_EXISTING_REVIEWS',
	'Scanning latest commit did not yield any new issues. Please have a look at older feedback still existing'
);

define(
	'VIPGOCI_LINT_FAILED_MSG_START',
	'Unable to PHP lint one or more files due to error running PHP linter: '
);

define(
	'VIPGOCI_LINT_FAILED_MSG_END',
	'The error may be temporary. If the error persists, please contact a human'
);

define(
	'VIPGOCI_PHPCS_SCAN_FAILED_MSG_START',
	'Unable to PHPCS or SVG scan one or more files due to error running PHPCS/SVG scanner: '
);

define(
	'VIPGOCI_PHPCS_SCAN_FAILED_MSG_END',
	'The error may be temporary. If the error persists, please contact a human'
);

/*
 * Indicates which sections of log
 * messages should not be logged to IRC.
 */
define(
	'VIPGOCI_IRC_IGNORE_STRING_START',
	'<!-- vip-go-ci-irc-ignore-start -->'
);

define(
	'VIPGOCI_IRC_IGNORE_STRING_END',
	'<!-- vip-go-ci-irc-ignore-end -->'
);

/*
 * Define exit-codes
 */
define( 'VIPGOCI_EXIT_NORMAL', 0 );
define( 'VIPGOCI_EXIT_INTERNAL_ERROR', 220 );
define( 'VIPGOCI_EXIT_COMMIT_NOT_PART_OF_PR', 230 );
define( 'VIPGOCI_EXIT_EXEC_TIME', 249 );
define( 'VIPGOCI_EXIT_CODE_ISSUES', 250 );
define( 'VIPGOCI_EXIT_SYSTEM_PROBLEM', 251 );
define( 'VIPGOCI_EXIT_GITHUB_PROBLEM', 252 );
define( 'VIPGOCI_EXIT_USAGE_ERROR', 253 );

/*
 * Define statistics-types.
 *
 * Note: These are related to the command-line
 * arguments passed to the program (e.g., --phpcs)
 * -- altering these is not recommended.
 */

define( 'VIPGOCI_STATS_PHPCS', 'phpcs' );
define( 'VIPGOCI_STATS_LINT', 'lint' );
define( 'VIPGOCI_STATS_HASHES_API', 'hashes-api' );

/*
 * Define auto-approval types
 */

define( 'VIPGOCI_APPROVAL_AUTOAPPROVE', 'auto-approval' );
define( 'VIPGOCI_APPROVAL_HASHES_API', 'hashes-api' );

/*
 * Defines for vipgoci_runtime_measure() function.
 */
define( 'VIPGOCI_RUNTIME_START', 'start' );
define( 'VIPGOCI_RUNTIME_STOP', 'stop' );
define( 'VIPGOCI_RUNTIME_DUMP', 'dump' );

/*
 * Defines for vipgoci_counter_report() function.
 */
define( 'VIPGOCI_COUNTERS_DUMP', 'dump' );
define( 'VIPGOCI_COUNTERS_DO', 'do' );

/*
 * Define for vipgoci_cache() function.
 */
define( 'VIPGOCI_CACHE_CLEAR', '--VIPGOCI-CACHE-CLEAR-0x321--' );

/*
 * Define for vipgoci_http_api_wait() function.
 */
define( 'VIPGOCI_HTTP_API_WAIT_TIME_SECONDS', 2 );

/*
 * Defines for files.
 */
define( 'VIPGOCI_OPTIONS_FILE_NAME', '.vipgoci_options' );

/*
 * Define for vipgoci_git_diffs_fetch() function.
 */
define(
	'VIPGOCI_GIT_DIFF_CALC_CHANGES',
	array(
		'+' => 'additions',
		'-' => 'deletions',
	)
);

define( 'VIPGOCI_GIT_DIFF_DATA_SOURCE_GIT_REPO', 'local-git-repo' );
define( 'VIPGOCI_GIT_DIFF_DATA_SOURCE_GITHUB_API', 'github-api' );

/**
 * Define file number of lines limit.
 */
define( 'VIPGOCI_SKIPPED_FILES', 'skipped-files' );

define( 'VIPGOCI_VALIDATION_MAXIMUM_LINES_LIMIT', 15000 );
define( 'VIPGOCI_VALIDATION_MAXIMUM_LINES', 'max-lines' );
define(
	'VIPGOCI_VALIDATION',
	array(
		VIPGOCI_VALIDATION_MAXIMUM_LINES
			=> 'Maximum number of lines exceeded (%d)',
	)
);

define( 'VIPGOCI_VALIDATION_MAXIMUM_DETAIL_MSG', 'Note that the above file(s) were not analyzed due to their length.' );
