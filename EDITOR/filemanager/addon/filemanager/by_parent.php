<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst
 * @package bigace.addon.filemanager
 */
require_once(dirname(__FILE__).'/environment.php');
require_once(dirname(__FILE__).'/listings.php');
if(!defined('_BIGACE_FILEMANAGER')) die('An error occured.');

// Script only works for images and files!

import('classes.util.IOHelper');

import('classes.util.CMSLink');
import('classes.util.LinkHelper');

import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');

import('classes.menu.Menu');
import('classes.menu.MenuService');
import('classes.image.Image');
import('classes.image.ImageService');
import('classes.file.File');
import('classes.file.FileService');

$selectedID = ((defined('GALLERY_PARENT') && !is_null(GALLERY_PARENT)) ? GALLERY_PARENT : _BIGACE_TOP_LEVEL);

if($itemtype == null)
    $selfLink = "by_parent.php?".$parameter;
else
    $selfLink = "by_parent.php?itemtype=".$itemtype.'&'.$parameter;

showHtmlHeader();
$ms = new MenuService();
$mm = $ms->getMenu($selectedID,$language);

$allIts = array();

if($allow_menu_browsing || $allow_menu_categories || $allow_menu_search)
    $allIts["1"] = $mm->getName();

if($allow_image_browsing || $allow_image_categories || $allow_image_search || $allow_image_upload)
    $allIts["4"] = sprintf(getTranslation('gallery_title'),getTranslation('item_4'),$mm->getName());

if($allow_file_browsing || $allow_file_categories || $allow_file_search || $allow_file_upload)
    $allIts["5"] = sprintf(getTranslation('gallery_title'),getTranslation('item_5'),$mm->getName());

foreach($allIts AS $IT => $TITLE)
{
    $items = array();

    $req = new ItemRequest($IT, $selectedID);
    $req->setLanguageID($language);
    $req->setTreetype(ITEM_LOAD_LIGHT);
    $req->setOrderBy(ORDER_COLUMN_POSITION);
    $req->setOrder($req->_ORDER_ASC);
    $req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
    $itemWalker = new SimpleItemTreeWalker($req);

    $a = $itemWalker->count();

    for ($i=0; $i < $a; $i++)
    {
        $temp = $itemWalker->next();
        $items[] = $temp;
    }

    echo '<h2 style="margin-bottom:0px;">'.$TITLE.'</h2>';
    render_listing($IT,$items,false, false);
}

showHtmlFooter();