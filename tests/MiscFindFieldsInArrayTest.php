<?php

require_once( __DIR__ . '/IncludesForTests.php' );

use PHPUnit\Framework\TestCase;

final class MiscFindFieldsInArrayTest extends TestCase {
	/**
	 * @covers ::vipgoci_find_fields_in_array
	 */
	public function testFindFields1() {
		$this->assertEquals(
			array(
				0 => false,
				1 => true,
				2 => true,
				3 => false,
				4 => false,
			),
			vipgoci_find_fields_in_array(
				array(
					'a' => 920,
					'b' => 700,
				),
				array(
					array(
						'a' => 920,
						'b' => 500,
						'c' => 0,
						'd' => 1,
					),
					array(
						'a' => 920,
						'b' => 700,
						'c' => 0,
						'd' => 2,
					),
					array(
						'a' => 920,
						'b' => 700,
						'c' => 0,
						'd' => 2,
					),
					array(
						'a' => 900,
						'b' => 720,
						'c' => 0,
						'd' => 2,
					),
					array(
						'a' => 900,
					)
				)
			)
		);
	}
}