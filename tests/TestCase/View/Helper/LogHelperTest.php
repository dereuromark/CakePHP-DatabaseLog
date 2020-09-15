<?php

namespace DatabaseLog\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use DatabaseLog\View\Helper\LogHelper;

class LogHelperTest extends TestCase {

	/**
	 * @var \DatabaseLog\View\Helper\LogHelper
	 */
	public $Log;

	/**
	 * @var \Cake\View\View
	 */
	public $View;

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->View = new View();
		$this->Log = new LogHelper($this->View);
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		unset($this->Log);
	}

	/**
	 * @return void
	 */
	public function testTypeLabel() {
		$result = $this->Log->typeLabel('error');
		$expected = '<span class="badge badge-danger">error</span>';
		$this->assertSame($expected, $result);

		$result = $this->Log->typeLabel('warning');
		$expected = '<span class="badge badge-warning">warning</span>';
		$this->assertSame($expected, $result);

		$result = $this->Log->typeLabel('info');
		$expected = '<span class="badge badge-info">info</span>';
		$this->assertSame($expected, $result);

		$result = $this->Log->typeLabel('foooo');
		$expected = '<span class="badge badge-secondary">foooo</span>';
		$this->assertSame($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testTypeLabelDisabled() {
		$this->Log->setConfig('typeMap', false);

		$result = $this->Log->typeLabel('<Sweet>');
		$expected = '&lt;Sweet&gt;';
		$this->assertSame($expected, $result);
	}

}
