<?php

declare(strict_types=1);

namespace Vipgoci\Tests\Unit;

require_once __DIR__ . '/../../main.php';
require_once __DIR__ . '/../../log.php';

require_once __DIR__ . '/../../options.php';

// Needed for functions vipgoci_unittests_output_suppress() and vipgoci_unittests_output_unsuppress().
require_once __DIR__ . '/../integration/IncludesForTestsOutputControl.php';

use PHPUnit\Framework\TestCase;

/**
 * Check if environmental options are handled
 * correctly.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class MainRunEnvOptionsHandleTest extends TestCase {
	/**
	 * Set up variables, set environmental variables.
	 */
	protected function setUp() :void {
		$this->options = array(
			'env-options' => 'repo-owner=REPO_OWNER,repo-name=REPO_NAME',
			'repo-owner'  => null,
			'repo-name'   => null,
		);

		$this->options_recognized = array(
			'repo-owner:',
			'repo-name:',
		);

		putenv( 'REPO_OWNER=myorg2' );
		putenv( 'REPO_NAME=myrepo1' );

	}

	/**
	 * Clear variables.
	 */
	protected function tearDown() :void {
		unset( $this->options );
		unset( $this->options_recognized );

		putenv( 'REPO_OWNER=' );
		putenv( 'REPO_NAME=' );
	}

	/**
	 * Check if environmental options are handled
	 * correctly.
	 *
	 * @covers ::vipgoci_run_env_options_handle
	 */
	public function testRunEnvOptionsHandle() :void {
		vipgoci_unittests_output_suppress();

		vipgoci_run_env_options_handle(
			$this->options,
			$this->options_recognized
		);

		vipgoci_unittests_output_unsuppress();

		$this->assertSame(
			array(
				'env-options' => array(
					'repo-owner=REPO_OWNER',
					'repo-name=REPO_NAME',
				),

				'repo-owner'  => 'myorg2',
				'repo-name'   => 'myrepo1',
			),
			$this->options
		);
	}
}
