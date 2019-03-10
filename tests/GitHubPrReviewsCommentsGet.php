<?php

require_once( __DIR__ . '/IncludesForTests.php' );

use PHPUnit\Framework\TestCase;

final class GitHubPrReviewsCommentsGet extends TestCase {
	var $options_git_repo_tests = array(
		'commit-test-github-pr-reviews-get-1'	=> null
	);

	var $options_git = array(
		'repo-owner'				=> null,
		'repo-name'				=> null,
		'github-token'				=> null,
	);

	protected function setUp() {
		vipgoci_unittests_get_config_values(
			'git',
			$this->options_git
		);

		vipgoci_unittests_get_config_values(
			'git-repo-tests',
			$this->options_git_repo_tests
		);

		$this->options = array_merge(
			$this->options_git,
			$this->options_git_repo_tests
		);

		$this->options['token'] =
		$this->options['github-token'];
	}

	protected function tearDown() {
		$this->options_git_repo_tests = null;
		$this->options_git = null;
		$this->options = null;
	}

	/**
	 * @covers ::vipgoci_github_pr_reviews_comments_get
	 */
	public function testGitHubPrReviewsCommentsGet1() {
		$prs_comments = array();

		ob_start();

		vipgoci_github_pr_reviews_comments_get(
			$this->options,
			$this->options['commit-test-github-pr-reviews-get-1'],
			'2019-01-01T00:00:00',
			$prs_comments
		);

		ob_end_clean();

		$this->assertEquals(
			1,
			count( array_keys( $prs_comments ) )
		);

		$this->assertEquals(
			212601504,
			$prs_comments['file1.php:3'][0]->pull_request_review_id
		);

		$this->assertEquals(
			264037556,
			$prs_comments['file1.php:3'][0]->id
		);

		$this->assertEquals(
			'file1.php',
			$prs_comments['file1.php:3'][0]->path
		);

		$this->assertEquals(
			3,
			$prs_comments['file1.php:3'][0]->position
		);

		$this->assertEquals(
			'All output should be escaped.',
			$prs_comments['file1.php:3'][0]->body
		);
	}
}
