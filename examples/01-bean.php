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

require(__DIR__.'/../vendor/autoload.php');

use Rekodi\Bean;

//--------------------------------------

/**
 * Out first bean represents a music album.
 *
 * @property string  $artist The name of the artist.
 * @property string  $title  The title.
 * @property string  $date   The release date.
 * @property integer $tracks The number of tracks.
 */
final class Album extends Bean
{
	/**
	 * This function is not necessary but allow us to dynamically
	 * initialize our bean.
	 */
	static function init()
	{
		self::$_fields = array_flip(array(
			'artist',
			'title',
			'date',
			'tracks',
		));
	}

	protected static $_fields;
}
Album::init();

//--------------------------------------

/* Creates an album with initial properties.
 *
 * [!] Invalid fields are ignored.
 */
$album = new Album(array(
	'artist' => 'Michael Jackson',
	'title'  => 'Thriller',
	'date'   => '1982-11-30',
	'tracks' => 8,
));

// We can iterates over defined properties.
foreach ($album as $name => $value)
{
	echo "$name: $value\n";
}
echo "----\n";

// We can alter any properties.
$album->artist = 'Jackson, Michael';

// We can see which properties as been altered.
foreach ($album->getDirty() as $name => $value)
{
	echo "$name: $value\n";
}
echo "----\n";