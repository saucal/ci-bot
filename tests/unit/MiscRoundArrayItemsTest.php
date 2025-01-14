<?php

declare(strict_types=1);

namespace Vipgoci\Tests\Unit;

require_once( __DIR__ . './../../misc.php' );

use PHPUnit\Framework\TestCase;

// phpcs:disable PSR1.Files.SideEffects

final class MiscRoundArrayItemsTest extends TestCase {
	/**
	 * @covers ::vipgoci_round_array_items
	 */
	public function testRoundArrayItems() {
		$org_array = array(
			'test1'	=> 10.333330,
			'test2'	=> 0.034444444,
			'test3'	=> 3.359999999,
			'test4'	=> 5.0000003,
			'test5'	=> 7.377777777,
			'test6'	=> 5.00000001,
		);

		$res_array = vipgoci_round_array_items(
			$org_array,
			2,
			PHP_ROUND_HALF_UP
		);

		$expected_array = array(
			'test1'	=> 10.33,
			'test2'	=> 0.03,
			'test3'	=> 3.36,
			'test4'	=> 5.00,
			'test5'	=> 7.38,
			'test6'	=> 5.0,
		);

		$this->assertSame(
			$expected_array,
			$res_array
		);
	}
}
