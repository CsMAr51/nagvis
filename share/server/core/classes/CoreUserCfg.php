<?php
/*****************************************************************************
 *
 * CoreUserCfg.php - Class for handling user/profile specific configurations
 *
 * Copyright (c) 2004-2010 NagVis Project (Contact: info@nagvis.org)
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
class CoreUserCfg {
	private $CORE;
	private $AUTHENTICATION;
	private $AUTHORISATION;
	private $profilesDir;

	// Optional list of value types to be fixed
	private $types = Array('sidebar' => 'i');

	/**
	 * Class Constructor
	 *
	 * @param	String			$name		Name of the map
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __construct() {
		$this->CORE = GlobalCore::getInstance();
		$this->AUTHENTICATION = $this->CORE->getAuthentication();
		$this->AUTHORISATION  = $this->CORE->getAuthorization();

		$this->profilesDir = $this->CORE->getMainCfg()->getValue('paths', 'profiles');
	}

	public function doGet($onlyUserCfg = false) {
		$opts = Array();
		if(!isset($this->AUTHENTICATION) || !$this->AUTHENTICATION->isAuthenticated() || !isset($this->AUTHORISATION))
			return $opts;

		if(!file_exists($this->profilesDir))
			return $opts;

		// Fetch all profile files to load
		$files = Array();
		if(!$onlyUserCfg)
			foreach($this->AUTHORISATION->getUserRoles($this->AUTHENTICATION->getUserId()) AS $role)
				$files[] = $role['name'].'.profile';
		$files[] = $this->AUTHENTICATION->getUser().'.profile';

		// Read all configurations and append to the option array
		foreach($files AS $file) {
			$f = $this->profilesDir.'/'.$file;
			if(!file_exists($f))
				continue;
			
			$a = json_decode(file_get_contents($f), true);
			if(!is_array($a))
				new GlobalMessage('ERROR', $this->CORE->getLang()->getText('Invalid data in "[FILE]".', Array('FILE' => $f)));

			$opts = array_merge($opts, $a);
		}

		return $opts;
	}

	public function doGetAsJson($onlyUserCfg = false) {
		return json_encode($this->doGet($onlyUserCfg));
	}

	public function doSet($opts) {
		$file = $this->profilesDir.'/'.$this->AUTHENTICATION->getUser().'.profile';

		if(!$this->CORE->checkExisting(dirname($file), true) || !$this->CORE->checkWriteable(dirname($file), true))
			return false;

		$cfg = $this->doGet(true);

		foreach($opts AS $key => $value) {
			if(isset($this->types[$key]))
				$value = $this->fixType($value, $this->types[$key]);
			$cfg[$key] = $value;
		}

		$ret = file_put_contents($file, json_encode($cfg)) !== false;
		$this->CORE->setPerms($file);
		return $ret;
	}

	public function getValue($key, $default = null) {
		$opts = $this->doGet();
		return isset($opts[$key]) ? $opts[$key] : $default;
	}

	private function fixType($val, $type) {
		if($type == 'i')
			return (int) $val;
		elseif($type == 'b')
			return (bool) $val;
		else
			return $val;
	}
}