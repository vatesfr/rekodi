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
interface Manager
{
	/**
	 * @param array[] $entries
	 *
	 * @return array[] For each entries an array with (at least)
	 *     generated properties.
	 */
	function create(array $entries);

	/**
	 * @param array $filter
	 *
	 * @return integer The number of deleted objets.
	 */
	function delete(array $filter);

	/**
	 * @param array $filter Properties that must match.
	 * @param array $fields The name of the fields to get, “null” for
	 *     everything.
	 *
	 * @return array[] The entries found.
	 */
	function get(array $filter = null, array $fields = null);

	/**
	 * @param array $filter Properties that must match.
	 * @param array[] $properties
	 *
	 * @return integer The number of updated entries.
	 */
	function update(array $filter, array $properties);
}
