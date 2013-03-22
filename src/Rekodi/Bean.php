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

namespace Rekodi;

/**
 *
 */
abstract class Bean implements \IteratorAggregate
{
	/**
	 * @var array
	 */
	protected static $_fields = array();

	//--------------------------------------

	/**
	 *
	 */
	final function __construct(array $properties = null)
	{
		if ($properties)
		{
			$this->set($properties);
		}
		$this->markAsClean();
	}

	/**
	 *
	 */
	function __destruct()
	{}

	/**
	 * @param string $name
	 */
	function __get($name)
	{
		if (!isset($this->_properties[$name])
		    && !array_key_exists($name, $this->_properties))
		{
			trigger_error(
				'no such readable property: '.get_class($this).'->'.$name,
				E_USER_ERROR
			);
		}

		return $this->_properties[$name];
	}

	/**
	 * @param string $name
	 */
	function __isset($name)
	{
		return (isset($this->_properties[$name])
		        || array_key_exists($name, $this->_properties));
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	function __set($name, $value)
	{
		if (!isset(static::$_fields[$name]))
		{
			trigger_error(
				'no such writable property: '.get_class($this).'->'.$name,
				E_USER_ERROR
			);
		}

		// @todo Handle validators.

		$this->_properties[$name] = $value;
	}

	/**
	 * Returns the dirty properties.
	 *
	 * Dirty properties are the properties which have a different
	 * value than their original one.
	 *
	 * @return array
	 */
	final function getDirty()
	{
		return array_diff_assoc(
			$this->_properties,
			$this->_originals
		);
	}

	/**
	 * Returns all properties as an array.
	 *
	 * @return array
	 */
	final function getProperties()
	{
		return $this->_properties;
	}

	/**
	 * Returns all properties with their original values.
	 *
	 * @return array
	 */
	final function getOriginals()
	{
		return $this->_originals;
	}

	/**
	 * Mark all properties as clean.
	 */
	final function markAsClean()
	{
		$this->_originals = $this->_properties;
	}

	/**
	 *
	 */
	function set(array $properties)
	{
		foreach ($properties as $name => $value)
		{
			$this->__set($name, $value);
		}
	}

	//--------------------------------------
	// IteratorAggregate
	//--------------------------------------

	/**
	 * @return \ArrayIterator
	 */
	final function getIterator()
	{
		return new \ArrayIterator($this->_properties);
	}

	//--------------------------------------

	/**
	 * @var array
	 */
	private $_properties = array();

	/**
	 * @var array
	 */
	private $_originals = array();
}
