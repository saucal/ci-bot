<?php
/**
 * Test vipgoci_lint_scan_commit() function.
 *
 * @package Automattic/vip-go-ci
 */

declare(strict_types=1);

namespace Vipgoci\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Class that implements the testing.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class LintScanCommitTest extends TestCase {
	/**
	 * Variable for PHP linting.
	 *
	 * @var $options_lint_scan
	 */
	private array $options_lint_scan = array(
		'commit-test-lint-scan-commit-1' => null,
		'commit-test-lint-scan-commit-2' => null,
		'commit-test-lint-scan-commit-3' => null,
		'commit-test-lint-scan-commit-4' => null,
		'lint-php1-path'                 => null,
		'lint-php1-version'              => null,
		'lint-php2-path'                 => null,
		'lint-php2-version'              => null,
	);

	/**
	 * Variable for git setup.
	 *
	 * @var $options_git
	 */
	private array $options_git = array(
		'git-path'        => null,
		'github-repo-url' => null,
		'repo-name'       => null,
		'repo-owner'      => null,
	);

	/**
	 * Setup function. Require files, etc.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		require_once __DIR__ . '/IncludesForTests.php';

		vipgoci_unittests_get_config_values(
			'lint-scan',
			$this->options_lint_scan
		);

		vipgoci_unittests_get_config_values(
			'git',
			$this->options_git
		);

		$this->options = array_merge(
			$this->options_lint_scan,
			$this->options_git
		);

		$this->options['github-token'] =
			vipgoci_unittests_get_config_value(
				'git-secrets',
				'github-token',
				true // Fetch from secrets file.
			);

		if ( empty( $this->options['github-token'] ) ) {
			$this->options['github-token'] = '';
		}

		$this->options['token'] =
			$this->options['github-token'];

		$this->options['lint'] = true;

		$this->options['lint-skip-folders'] = array();

		$this->options['phpcs-skip-folders'] = array();

		$this->options['branches-ignore'] = array();

		$this->options['skip-draft-prs'] = false;

		$this->options['skip-large-files'] = false;

		$this->options['skip-large-files-limit'] = 3;

		$this->options['lint-modified-files-only'] = false;

		$this->options['lint-php-versions'] = array(
			$this->options['lint-php1-version'],
			$this->options['lint-php2-version'],
		);

		$this->options['lint-php-version-paths'] = array(
			$this->options['lint-php1-version'] => $this->options['lint-php1-path'],
			$this->options['lint-php2-version'] => $this->options['lint-php2-path'],
		);

		unset(
			$this->options['lint-php1-path'],
			$this->options['lint-php1-version'],
			$this->options['lint-php2-path'],
			$this->options['lint-php2-version']
		);

		global $vipgoci_debug_level;
		$vipgoci_debug_level = 2;
	}

	/**
	 * Tear down function. Remove variables and temporary repository.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		if ( false !== $this->options['local-git-repo'] ) {
			vipgoci_unittests_remove_git_repo(
				$this->options['local-git-repo']
			);
		}

		unset( $this->options_lint_scan );
		unset( $this->options_git );
		unset( $this->options );
	}

	/**
	 * PHP lint file with syntax errors.
	 *
	 * @return void
	 *
	 * @covers ::vipgoci_lint_scan_commit
	 */
	public function testLintDoScan1() :void {
		$options_test = vipgoci_unittests_options_test(
			$this->options,
			array( 'github-token', 'token' ),
			$this
		);

		if ( - 1 === $options_test ) {
			return;
		}

		$this->options['commit'] =
			$this->options['commit-test-lint-scan-commit-1'];

		vipgoci_unittests_output_suppress();

		$this->options['local-git-repo'] =
			vipgoci_unittests_setup_git_repo(
				$this->options
			);

		if ( false === $this->options['local-git-repo'] ) {
			$this->markTestSkipped(
				'Could not set up git repository: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		$issues_submit  = array();
		$issues_stat    = array();
		$issues_skipped = array();

		/*
		 * Get PRs implicated and warm up stats.
		 */
		$prs_implicated = vipgoci_github_prs_implicated(
			$this->options['repo-owner'],
			$this->options['repo-name'],
			$this->options['commit'],
			$this->options['github-token'],
			$this->options['branches-ignore']
		);

		foreach ( $prs_implicated as $pr_item ) {
			$issues_stat[ $pr_item->number ]['error']              = 0;
			$issues_skipped[ $pr_item->number ]['issues']['total'] = 0;
		}

		if (
			( ! isset( $pr_item->number ) ) ||
			( ! is_numeric( $pr_item->number ) )
		) {
			$this->markTestSkipped(
				'Could not get Pull-Request information for the test: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		vipgoci_lint_scan_commit(
			$this->options,
			$issues_submit,
			$issues_stat,
			$issues_skipped
		);

		vipgoci_unittests_output_unsuppress();

		/*
		 * Some versions of PHP reverse the ',' and ';'
		 * in the string below; deal with that.
		 */
		for ( $i = 0; $i < 2; $i++ ) {
			$issues_submit[ $pr_item->number ][ $i ]['issue']['message'] =
				vipgoci_unittests_php_syntax_error_compat(
					$issues_submit[ $pr_item->number ][ $i ]['issue']['message'],
					true
				);
		}

		$this->assertSame(
			array(
				$pr_item->number => array(
					array(
						'type'      => 'lint',
						'file_name' => 'lint-scan-commit-test-2.php',
						'file_line' => 4,
						'issue'     => array(
							'message'  => 'Linting with PHP ' . $this->options['lint-php-versions'][0] . " turned up: <code>syntax error, unexpected end of file, expecting ',' or ';'</code>",
							'level'    => 'ERROR',
							'severity' => 5,
						),
					),
					array(
						'type'      => 'lint',
						'file_name' => 'lint-scan-commit-test-2.php',
						'file_line' => 4,
						'issue'     => array(
							'message'  => 'Linting with PHP ' . $this->options['lint-php-versions'][1] . " turned up: <code>syntax error, unexpected end of file, expecting ',' or ';'</code>",
							'level'    => 'ERROR',
							'severity' => 5,
						),
					),
				),
			),
			$issues_submit
		);

		$this->assertSame(
			array(
				$pr_item->number => array(
					'error' => 2,
				),
			),
			$issues_stat
		);

		unset( $this->options['commit'] );
	}


	/**
	 * PHP lint files, two files have syntax errors.
	 *
	 * @return void
	 *
	 * @covers ::vipgoci_lint_scan_commit
	 */
	public function testLintDoScan2() :void {
		$options_test = vipgoci_unittests_options_test(
			$this->options,
			array( 'github-token', 'token' ),
			$this
		);

		if ( - 1 === $options_test ) {
			return;
		}

		$this->options['commit'] =
			$this->options['commit-test-lint-scan-commit-2'];

		$this->options['lint-skip-folders'] = array(
			'tests3',
			'tests4000',
			'tests5000',
		);

		vipgoci_unittests_output_suppress();

		$this->options['local-git-repo'] =
			vipgoci_unittests_setup_git_repo(
				$this->options
			);

		if ( false === $this->options['local-git-repo'] ) {
			$this->markTestSkipped(
				'Could not set up git repository: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		$issues_submit  = array();
		$issues_stat    = array();
		$issues_skipped = array();

		/*
		 * Get PRs implicated and warm up stats.
		 */
		$prs_implicated = vipgoci_github_prs_implicated(
			$this->options['repo-owner'],
			$this->options['repo-name'],
			$this->options['commit'],
			$this->options['github-token'],
			$this->options['branches-ignore']
		);

		foreach ( $prs_implicated as $pr_item ) {
			$issues_stat[ $pr_item->number ]['error'] = 0;

			$issues_skipped[ $pr_item->number ]['issues']['total'] = 0;
		}

		if (
			( ! isset( $pr_item->number ) ) ||
			( ! is_numeric( $pr_item->number ) )
		) {
			$this->markTestSkipped(
				'Could not get Pull-Request information for the test: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		vipgoci_lint_scan_commit(
			$this->options,
			$issues_submit,
			$issues_stat,
			$issues_skipped
		);

		vipgoci_unittests_output_unsuppress();

		/*
		 * Some versions of PHP reverse the ',' and ';'
		 * in the string below; deal with that.
		 */
		for ( $i = 0; $i < 4; $i++ ) {
			$issues_submit[ $pr_item->number ][ $i ]['issue']['message'] =
				vipgoci_unittests_php_syntax_error_compat(
					$issues_submit[ $pr_item->number ][ $i ]['issue']['message'],
					true
				);
		}

		$this->assertSame(
			array(
				$pr_item->number => array(
					array(
						'type'      => 'lint',
						'file_name' => 'tests1/myfile1.php',
						'file_line' => 4,
						'issue'     => array(
							'message'  => 'Linting with PHP ' . $this->options['lint-php-versions'][0] . " turned up: <code>syntax error, unexpected end of file, expecting ',' or ';'</code>",
							'level'    => 'ERROR',
							'severity' => 5,
						),
					),
					array(
						'type'      => 'lint',
						'file_name' => 'tests1/myfile1.php',
						'file_line' => 4,
						'issue'     => array(
							'message'  => 'Linting with PHP ' . $this->options['lint-php-versions'][1] . " turned up: <code>syntax error, unexpected end of file, expecting ',' or ';'</code>",
							'level'    => 'ERROR',
							'severity' => 5,
						),
					),
					array(
						'type'      => 'lint',
						'file_name' => 'tests2/myfile1.php',
						'file_line' => 4,
						'issue'     => array(
							'message'  => 'Linting with PHP ' . $this->options['lint-php-versions'][0] . " turned up: <code>syntax error, unexpected end of file, expecting ',' or ';'</code>",
							'level'    => 'ERROR',
							'severity' => 5,
						),
					),
					array(
						'type'      => 'lint',
						'file_name' => 'tests2/myfile1.php',
						'file_line' => 4,
						'issue'     => array(
							'message'  => 'Linting with PHP ' . $this->options['lint-php-versions'][1] . " turned up: <code>syntax error, unexpected end of file, expecting ',' or ';'</code>",
							'level'    => 'ERROR',
							'severity' => 5,
						),
					),

					/*
					 * Note: tests3/myfile1.php should be skipped,
					 * according to --lint-skip-folders option
					 * set above.
					 */
				),
			),
			$issues_submit
		);

		$this->assertSame(
			array(
				$pr_item->number => array(
					'error' => 4,
				),
			),
			$issues_stat
		);

		unset( $this->options['commit'] );
	}

	/**
	 * PHP lint files when there are large files to be skipped
	 * and the feature is enabled.
	 *
	 * @return void
	 *
	 * @covers ::vipgoci_lint_scan_commit
	 */
	public function testLintWillSkipLargeFileWhenOptionIsOn(): void {

		$options_test = vipgoci_unittests_options_test(
			$this->options,
			array( 'github-token', 'token' ),
			$this
		);

		if ( - 1 === $options_test ) {
			return;
		}

		$this->options['commit'] = $this->options['commit-test-lint-scan-commit-3'];

		vipgoci_unittests_output_suppress();

		$this->options['local-git-repo'] =
			vipgoci_unittests_setup_git_repo(
				$this->options
			);

		if ( false === $this->options['local-git-repo'] ) {
			$this->markTestSkipped(
				'Could not set up git repository: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		$issues_submit  = array();
		$issues_stat    = array();
		$issues_skipped = array();

		/*
		 * Get PRs implicated and warm up stats.
		 */
		$prs_implicated = vipgoci_github_prs_implicated(
			$this->options['repo-owner'],
			$this->options['repo-name'],
			$this->options['commit'],
			$this->options['github-token'],
			$this->options['branches-ignore']
		);

		foreach ( $prs_implicated as $pr_item ) {
			$issues_stat[ $pr_item->number ]['error'] = 0;
			$issues_skipped[ $pr_item->number ]       = $this->getDefaultSkippedFilesDueIssuesMock();
		}

		if (
			( ! isset( $pr_item->number ) ) ||
			( ! is_numeric( $pr_item->number ) )
		) {
			$this->markTestSkipped(
				'Could not get Pull-Request information for the test: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		$this->options['skip-large-files'] = true;

		vipgoci_lint_scan_commit(
			$this->options,
			$issues_submit,
			$issues_stat,
			$issues_skipped
		);

		vipgoci_unittests_output_unsuppress();

		$expected_issues_skipped = array(
			39 => array(
				'issues' => array(
					'max-lines' => array( 'test1/myfile-1.php' ),
				),
				'total'  => 1,
			),
		);

		$this->assertSame(
			$expected_issues_skipped,
			$issues_skipped
		);

		unset( $this->options['commit'] );
	}

	/**
	 * PHP lint when skipping large files is disabled.
	 *
	 * @covers ::vipgoci_lint_scan_commit
	 */
	public function testLintShouldScanAllFilesWhenSkipLargeFilesOptionIsOff() :void {

		$options_test = vipgoci_unittests_options_test(
			$this->options,
			array( 'github-token', 'token' ),
			$this
		);

		if ( - 1 === $options_test ) {
			return;
		}

		$this->options['commit'] = $this->options['commit-test-lint-scan-commit-4'];

		$this->options['skip-large-files'] = false;

		vipgoci_unittests_output_suppress();

		$this->options['local-git-repo'] = vipgoci_unittests_setup_git_repo( $this->options );

		if ( false === $this->options['local-git-repo'] ) {
			$this->markTestSkipped(
				'Could not set up git repository: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		$issues_submit  = array();
		$issues_stat    = array();
		$issues_skipped = array();

		/*
		 * Get PRs implicated and warm up stats.
		 */
		$prs_implicated = vipgoci_github_prs_implicated(
			$this->options['repo-owner'],
			$this->options['repo-name'],
			$this->options['commit'],
			$this->options['github-token'],
			$this->options['branches-ignore']
		);

		foreach ( $prs_implicated as $pr_item ) {
			$issues_stat[ $pr_item->number ]['error'] = 0;
			$issues_skipped[ $pr_item->number ]       = $this->getDefaultSkippedFilesDueIssuesMock();
		}

		if (
			! isset( $pr_item->number )
			|| ! is_numeric( $pr_item->number )
		) {
			$this->markTestSkipped(
				'Could not get Pull-Request information for the test: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		vipgoci_lint_scan_commit(
			$this->options,
			$issues_submit,
			$issues_stat,
			$issues_skipped
		);

		vipgoci_unittests_output_unsuppress();

		$expected_issues_skipped = array(
			43 => array(
				'issues' => array(),
				'total'  => 0,
			),
		);

		$this->assertSame(
			$expected_issues_skipped,
			$issues_skipped
		);

		unset( $this->options['commit'] );
	}

	/**
	 * PHP lint when skipping large files is enabled and
	 * the limit is set to a custom value.
	 *
	 * @covers ::vipgoci_lint_scan_commit
	 */
	public function testLintShouldValidateAndSkipLargeFileWhenSkipLargeFilesAndLimitOptionsAreOn(): void {

		$options_test = vipgoci_unittests_options_test(
			$this->options,
			array( 'github-token', 'token' ),
			$this
		);

		if ( - 1 === $options_test ) {
			return;
		}

		$this->options['commit'] = $this->options['commit-test-lint-scan-commit-4'];

		$this->options['skip-large-files']       = true;
		$this->options['skip-large-files-limit'] = 15;

		vipgoci_unittests_output_suppress();

		$this->options['local-git-repo'] =
			vipgoci_unittests_setup_git_repo(
				$this->options
			);

		if ( false === $this->options['local-git-repo'] ) {
			$this->markTestSkipped(
				'Could not set up git repository: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		$issues_submit  = array();
		$issues_stat    = array();
		$issues_skipped = array();

		/*
		 * Get PRs implicated and warm up stats.
		 */
		$prs_implicated = vipgoci_github_prs_implicated(
			$this->options['repo-owner'],
			$this->options['repo-name'],
			$this->options['commit'],
			$this->options['github-token'],
			$this->options['branches-ignore']
		);

		foreach ( $prs_implicated as $pr_item ) {
			$issues_stat[ $pr_item->number ]['error'] = 0;
			$issues_skipped[ $pr_item->number ]       = $this->getDefaultSkippedFilesDueIssuesMock();
		}

		if (
			! isset( $pr_item->number )
			|| ! is_numeric( $pr_item->number )
		) {
			$this->markTestSkipped(
				'Could not get Pull-Request information for the test: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		vipgoci_lint_scan_commit(
			$this->options,
			$issues_submit,
			$issues_stat,
			$issues_skipped
		);

		vipgoci_unittests_output_unsuppress();

		$expected_issues_skipped = array(
			43 => array(
				'issues' => array(
					'max-lines' => array(
						0 => 'tests1/myfile1.php',
					),
				),
				'total'  => 1,
			),
		);

		$this->assertSame(
			$expected_issues_skipped,
			$issues_skipped
		);

		unset( $this->options['commit'] );
	}

	/**
	 * Returns custom array used in tests.
	 *
	 * @return array
	 */
	private function getDefaultSkippedFilesDueIssuesMock(): array {
		return array(
			'issues' => array(),
			'total'  => 0,
		);
	}

	/**
	 * PHP lint with the lint-modified-files-only option on.
	 *
	 * @return void
	 *
	 * @covers ::vipgoci_lint_scan_commit
	 */
	public function testLintDoScan3(): void {
		$options_test = vipgoci_unittests_options_test(
			$this->options,
			array( 'github-token', 'token' ),
			$this
		);

		if ( -1 === $options_test ) {
			return;
		}

		if ( empty( $this->options['github-token'] ) ) {
			$this->options['github-token'] = '';
		}

		$this->options['token'] = $this->options['github-token'];

		if ( - 1 === $options_test ) {
			return;
		}

		$this->options['commit'] = $this->options['commit-test-lint-scan-commit-1'];

		vipgoci_unittests_output_suppress();

		$this->options['local-git-repo'] = vipgoci_unittests_setup_git_repo(
			$this->options
		);

		$this->options['lint-modified-files-only'] = true;

		if ( false === $this->options['local-git-repo'] ) {
			$this->markTestSkipped(
				'Could not set up git repository: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		$issues_submit  = array();
		$issues_stat    = array();
		$issues_skipped = array();

		/*
		 * Get PRs implicated and warm up stats.
		 */
		$prs_implicated = vipgoci_github_prs_implicated(
			$this->options['repo-owner'],
			$this->options['repo-name'],
			$this->options['commit'],
			$this->options['github-token'],
			$this->options['branches-ignore']
		);

		foreach ( $prs_implicated as $pr_item ) {
			$issues_stat[ $pr_item->number ]['error']              = 0;
			$issues_skipped[ $pr_item->number ]['issues']['total'] = 0;
		}

		if ( ! isset( $pr_item->number ) || ! is_numeric( $pr_item->number ) ) {
			$this->markTestSkipped(
				'Could not get Pull-Request information for the test: ' .
				vipgoci_unittests_output_get()
			);

			return;
		}

		vipgoci_lint_scan_commit(
			$this->options,
			$issues_submit,
			$issues_stat,
			$issues_skipped
		);

		vipgoci_unittests_output_unsuppress();

		/*
		 * Some versions of PHP reverse the ',' and ';'
		 * in the string below; deal with that.
		 */

		for ( $i = 0; $i < 2; $i++ ) {
			$issues_submit[ $pr_item->number ][ $i ]['issue']['message'] = vipgoci_unittests_php_syntax_error_compat(
				$issues_submit[ $pr_item->number ][ $i ]['issue']['message'],
				true
			);
		}

		$this->assertSame(
			array(
				$pr_item->number => array(
					array(
						'type'      => 'lint',
						'file_name' => 'lint-scan-commit-test-2.php',
						'file_line' => 4,
						'issue'     => array(
							'message'  => 'Linting with PHP ' . $this->options['lint-php-versions'][0] . " turned up: <code>syntax error, unexpected end of file, expecting ',' or ';'</code>",
							'level'    => 'ERROR',
							'severity' => 5,
						),
					),
					array(
						'type'      => 'lint',
						'file_name' => 'lint-scan-commit-test-2.php',
						'file_line' => 4,
						'issue'     => array(
							'message'  => 'Linting with PHP ' . $this->options['lint-php-versions'][1] . " turned up: <code>syntax error, unexpected end of file, expecting ',' or ';'</code>",
							'level'    => 'ERROR',
							'severity' => 5,
						),
					),
				),
			),
			$issues_submit
		);

		$this->assertSame(
			array(
				$pr_item->number => array(
					'error' => 2,
				),
			),
			$issues_stat
		);

		unset( $this->options['commit'] );
	}
}
