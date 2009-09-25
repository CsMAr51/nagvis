<?php
/*****************************************************************************
 *
 * NagVisIndexView.php - Class for parsing the NagVis index in nagvis-js
 *                       frontend
 *
 * Copyright (c) 2004-2009 NagVis Project (Contact: info@nagvis.org)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/
 
/**
 * @author	Lars Michelsen <lars@vertical-visions.de>
 */
class NagVisIndexView {
	private $CORE = null;
	
	private $sSubtitle = '';
	private $sCustomStylesheet = '';
	private $sHeaderMenu = '';
	private $sContent = '';
	
	/**
	 * Class Constructor
	 *
	 * @param    GlobalCore      $CORE
	 * @param    String          $NAME
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __construct(GlobalCore $CORE) {
		$this->CORE = $CORE;
	}

	public function setSubtitle($s) {
		$this->sSubtitle = ' &rsaquo; ' . $s;
	}

	public function setCustomStylesheet($s) {
		$this->sCustomStylesheet = $s;
	}

	public function setHeaderMenu($s) {
		$this->sHeaderMenu = $s;
	}

	public function setContent($s) {
		$this->sContent = $s;
	}
	
	/**
	 * Parses the map and the objects for the nagvis-js frontend
	 *
	 * @return	String 	String with JS Code
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function parse() {
		// Initialize template system
		$TMPL = New FrontendTemplateSystem($this->CORE);
		$TMPLSYS = $TMPL->getTmplSys();
		
		$aData = Array(
			'pageTitle' => $this->CORE->MAINCFG->getValue('internal', 'title') . $this->sSubtitle,
			'htmlBase' => $this->CORE->MAINCFG->getValue('paths', 'htmlbase'),
			'htmlJs' => $this->CORE->MAINCFG->getValue('paths', 'htmljs'),
			'htmlCss' => $this->CORE->MAINCFG->getValue('paths', 'htmlcss'),
			'customStylesheet' => $this->sCustomStylesheet,
			'headerMenu' => $this->sHeaderMenu,
			'content' => $this->sContent
		);
		
		// Build page based on the template file and the data array
		return $TMPLSYS->get($TMPL->getTmplFile('index'), $aData);
	}
}
?>