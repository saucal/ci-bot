<?php

declare(strict_types=1);

namespace Vipgoci\Tests\Unit;

require_once __DIR__ . '/../../results.php';

use PHPUnit\Framework\TestCase;

final class ResultsFilterDuplicateTest extends TestCase {
	/**
	 * @covers ::vipgoci_results_filter_duplicate
	 */
	public function testFilterDuplicate1() {
		$issues_filtered = vipgoci_results_filter_duplicate(
			array(
				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 7,
					'column' => 6,
					'level' => 'WARNING',
				),

				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 7,
					'column' => 6,
					'level' => 'WARNING',
				),

				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 7,
					'column' => 6,
					'level' => 'WARNING',
				),
			)
		);

		$this->assertSame(
			array(
				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 7,
					'column' => 6,
					'level' => 'WARNING',
				)
			),
			$issues_filtered
		);
	}

	/**
	 * @covers ::vipgoci_results_filter_duplicate
	 */
	public function testFilterDuplicate2() {
		$issues_filtered = vipgoci_results_filter_duplicate(
			array(
				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 7,
					'column' => 6,
					'level' => 'WARNING',
				),

				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 7,
					'column' => 6,
					'level' => 'WARNING',
				),

				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 7,
					'column' => 6,
					'level' => 'WARNING',
				),

				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 80,
					'column' => 6,
					'level' => 'WARNING',
				),


			)
		);

		$this->assertSame(
			array(
				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 7,
					'column' => 6,
					'level' => 'WARNING',
				),

				array(
					'message' => 'json_encode() is discouraged. Use wp_json_encode() instead.',
					'source' => 'WordPress.WP.AlternativeFunctions.json_encode_json_encode',
					'severity' => 5,
					'fixable' => false,
					'type' => 'WARNING',
					'line' => 80,
					'column' => 6,
					'level' => 'WARNING',
				),
			),
			$issues_filtered
		);
	}
}
