<?php
/*
 *  This file is part of Anontune.
 *
 *  Anontune is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Anontune is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero Public License for more details.
 *
 *  You should have received a copy of the GNU Affero Public License
 *  along with Anontune.  If not, see <http://www.gnu.org/licenses/>.
 *  
 */

if($_ANONTUNE !== true) { die(); }

$sAlbum = new Album($router->uParameters[1]);

$sData[0]["id"] = $sAlbum->sId;
$sData[0]["title"] = $sAlbum->sTitle;
$sData[0]["artist_id"] = $sAlbum->sArtistId;
$sData[0]["is_valid"] = $sAlbum->sIsValid;
$sData[0]["artist"]["id"] = $sAlbum->sArtist->sId;
$sData[0]["artist"]["name"] = $sAlbum->sArtist->sName;

$sStatus = ANONTUNE_API_SUCCESS;
?>
