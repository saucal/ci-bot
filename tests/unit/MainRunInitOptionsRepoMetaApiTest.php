<?php

declare(strict_types=1);

namespace Vipgoci\Tests\Unit;

require_once __DIR__ . '/../../main.php';
require_once __DIR__ . '/../../options.php';
require_once __DIR__ . '/../../misc.php';

use PHPUnit\Framework\TestCase;

/**
 * Check if repo-meta API options are handled
 * correctly.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class MainRunInitOptionsRepoMetaApiTest extends TestCase {
	/**
	 * Set up variable.
	 */
	protected function setUp() :void {
		$this->options = array();
	}

	/**
	 * Clear variable.
	 */
	protected function tearDown() :void {
		unset( $this->options );
	}

	/**
	 * Check if repo-meta API default options are correctly provided.
	 *
	 * @covers ::vipgoci_run_init_options_repo_meta_api
	 */
	public function testRunInitOptionsRepoMetaApiDefault() :void {
		vipgoci_run_init_options_repo_meta_api(
			$this->options
		);

		$this->assertSame(
			array(
				'repo-meta-api-base-url'     => null,
				'repo-meta-api-user-id'      => null,
				'repo-meta-api-access-token' => null,
			),
			$this->options
		);
	}

	/**
	 * Check if repo-meta API custom options are correctly parsed.
	 *
	 * @covers ::vipgoci_run_init_options_repo_meta_api
	 */
	public function testRunInitOptionsRepoMetaApiCustom() :void {
		$this->options = array(
			'repo-meta-api-base-url'     => 'https://api.test.local/v1/api  ',
			'repo-meta-api-user-id'      => '3500',
			'repo-meta-api-access-token' => '   test  ',
		);

		vipgoci_run_init_options_repo_meta_api(
			$this->options
		);

		$this->assertSame(
			array(
				'repo-meta-api-base-url'     => 'https://api.test.local/v1/api',
				'repo-meta-api-user-id'      => 3500,
				'repo-meta-api-access-token' => 'test',
			),
			$this->options
		);
	}
}
