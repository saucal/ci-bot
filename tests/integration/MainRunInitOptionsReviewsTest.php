<?php

declare(strict_types=1);

namespace Vipgoci\Tests\Integration;

require_once __DIR__ . '/IncludesForTests.php';

use PHPUnit\Framework\TestCase;

/**
 * Check if all options regarding reviews are correctly set up.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class MainRunInitOptionsReviewsTest extends TestCase {
	/**
	 * Set up variables.
	 */
	protected function setUp() :void {
		$this->options = array();
	}

	/**
	 * Clean up variables.
	 */
	protected function tearDown() :void {
		unset( $this->options );
	}

	/**
	 * Tests if default options regarding reviews are set up correctly.
	 *
	 * @covers ::vipgoci_run_init_options_reviews
	 */
	public function testRunInitOptionsReviewsDefault() :void {
		vipgoci_run_init_options_reviews(
			$this->options
		);

		$this->assertSame(
			array(
				'review-comments-sort'              => false,
				'review-comments-include-severity'  => false,
				'review-comments-max'               => 10,
				'review-comments-total-max'         => 200,
				'review-comments-ignore'            => array(),
				'dismiss-stale-reviews'             => false,
				'dismissed-reviews-repost-comments' => true,
				'dismissed-reviews-exclude-reviews-from-team' => array(),
			),
			$this->options
		);
	}

	/**
	 * Tests if custom options regarding reviews are set up correctly.
	 *
	 * @covers ::vipgoci_run_init_options_reviews
	 */
	public function testRunInitOptionsReviewsCustom() :void {
		$this->options = array(
			'review-comments-sort'              => 'true',
			'review-comments-include-severity'  => 'true',
			'review-comments-max'               => '50',
			'review-comments-total-max'         => '100',
			'review-comments-ignore'            => '  comment1.|||CoMMENt2  ',
			'dismiss-stale-reviews'             => 'true',
			'dismissed-reviews-repost-comments' => 'false',
		);

		vipgoci_run_init_options_reviews(
			$this->options
		);

		$this->assertSame(
			array(
				'review-comments-sort'              => true,
				'review-comments-include-severity'  => true,
				'review-comments-max'               => 50,
				'review-comments-total-max'         => 100,
				'review-comments-ignore'            => array( 'comment1', 'comment2' ),
				'dismiss-stale-reviews'             => true,
				'dismissed-reviews-repost-comments' => false,
				'dismissed-reviews-exclude-reviews-from-team' => array(),
			),
			$this->options
		);
	}
}