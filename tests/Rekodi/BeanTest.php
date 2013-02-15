<?php
/**
 * This file is a part of Rekodi.
 *
 * Rekodi is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Rekodi is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Rekodi. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Julien Fontanet <julien.fontanet@vates.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0-standalone.html GPLv3
 *
 * @package Rekodi
 */

namespace Test\Rekodi;

use Rekodi\Bean;

//--------------------------------------

final class TestBean extends Bean
{
	static function init()
	{
		self::$_fields = array_flip(array(
			'field',
		));
	}

	protected static $_fields;
}
TestBean::init();

//--------------------------------------

/**
 * @covers Bean
 */
final class BeanTest extends \PHPUnit_Framework_TestCase
{
	function testGetUninitilizedProperty()
	{
		$bean = new TestBean;

		$this->setExpectedException('\PHPUnit_Framework_Error');

		$field = $this->field;
	}
}
