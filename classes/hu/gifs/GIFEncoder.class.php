<?php
/*
	GIFEncoder Version 3.0 by László Zsidi, http://gifs.hu
*/

class hu_gifs_GIFEncoder {

	private $_gif = 'GIF89a';		
	private $_ver = 'GIFEncoder V3.00';
	private $_buf = array();
	private $_ofs = array();
	private $_sig =  0;
	private $_lop =  0;
	private $_dis =  2;
	private $_col = -1;
	private $_img = -1;
	private $_err = array(
		ERR00 => 'Does not supported function for only one image!',
		ERR01 => 'Source is not a GIF image!',
		ERR02 => 'Unintelligible flag ',
		ERR03 => 'Does not make animation from animated GIF source',
	);

	public function encode($GIF_src, $GIF_dly, $GIF_lop, $GIF_dis,$GIF_red, $GIF_grn, $GIF_blu, $GIF_ofs,$GIF_mod)
	{
		if (!is_array($GIF_src) && !is_array($GIF_dly)) {
			printf('%s: %s', $this->_ver, $this->_err['ERR00']);
			exit(0);
		}
		if (is_array($GIF_ofs) && count($GIF_ofs) > 1) {
			$this->_sig = 1;
			$this->_ofs = $GIF_ofs;
		}
		$this->_lop =($GIF_lop > -1) ? $GIF_lop : 0;
		$this->_dis =($GIF_dis > -1) ? (($GIF_dis < 3) ? $GIF_dis : 3) : 2;
		$this->_col =($GIF_red > -1 && $GIF_grn > -1 && $GIF_blu > -1) ?
					($GIF_red | ($GIF_grn << 8) | ($GIF_blu << 16)) : -1;

		for($i = 0; $i < count($GIF_src); $i++) {
			if (strToLower($GIF_mod) == 'url') {
				$this->_buf[] = fread(fopen($GIF_src[$i], 'rb'), filesize($GIF_src[$i]));
			}
			else if (strToLower($GIF_mod) == 'bin') {
				$this->_buf[] = $GIF_src[$i];
			}
			else {
				printf		'%s: %s(%s)!', $this->_ver, $this->_err['ERR02'], $GIF_mod);
				exit(0);
			}
			if (substr($this->_buf[$i], 0, 6) != 'GIF87a' && substr($this->_buf[$i], 0, 6) != 'GIF89a') {
				printf('%s: %d %s', $this->_ver, $i, $this->_err['ERR01']);
				exit(0);
			}
			for($j =(13 + 3 *(2 << (ord($this->_buf[$i] { 10 }) & 0x07))), $k = TRUE; $k; $j++) {
				switch($this->_buf[$i] { $j }) {
					case '!':
						if ((substr($this->_buf[$i],($j + 3), 8)) == 'NETSCAPE') {
							printf('%s: %s(%s source)!', $this->_ver, $this->_err['ERR03'],($i + 1));
							exit(0);
						}
						break;
					case ';':
						$k = FALSE;
						break;
				}
			}
		}
		$this->_addHeader();
		for($i = 0; $i < count($this->_buf); $i++) {
			$this->_addFrames($i, $GIF_dly[$i]);
		}
		$this->_addFooter();
	}

	private function _addHeader() {
		$cmap = 0;

		if (ord($this->_buf[0] { 10 }) & 0x80) {
			$cmap = 3 *(2 << (ord($this->_buf[0] { 10 }) & 0x07));

			$this->_gif .= substr($this->_buf[0], 6, 7);
			$this->_gif .= substr($this->_buf[0], 13, $cmap);
			$this->_gif .= '!\377\13NETSCAPE2.0\3\1' . hu_gifs_GIFEncoder::_word($this->_lop) . '\0';
		}
	}

	private function _addFrames($i, $d) {

		$Locals_str = 13 + 3 *(2 << (ord($this->_buf[$i] { 10 }) & 0x07));

		$Locals_end = strlen($this->_buf[$i]) - $Locals_str - 1;
		$Locals_tmp = substr($this->_buf[$i], $Locals_str, $Locals_end);

		$Global_len = 2 << (ord($this->_buf[0 ] { 10 }) & 0x07);
		$Locals_len = 2 << (ord($this->_buf[$i] { 10 }) & 0x07);

		$Global_rgb = substr($this->_buf[0 ], 13,
							3 *(2 << (ord($this->_buf[0 ] { 10 }) & 0x07)));
		$Locals_rgb = substr($this->_buf[$i], 13,
							3 *(2 << (ord($this->_buf[$i] { 10 }) & 0x07)));

		$Locals_ext = '!\xF9\x04' . chr(($this->_dis << 2) + 0) .
						chr(($d >> 0) & 0xFF) . chr(($d >> 8) & 0xFF) . '\x0\x0';

		if ($this->_col > -1 && ord($this->_buf[$i] { 10 }) & 0x80) {
			for($j = 0; $j < (2 << (ord($this->_buf[$i] { 10 }) & 0x07)); $j++) {
				if (
						ord($Locals_rgb { 3 * $j + 0 }) ==(($this->_col >> 16) & 0xFF) &&
						ord($Locals_rgb { 3 * $j + 1 }) ==(($this->_col >>  8) & 0xFF) &&
						ord($Locals_rgb { 3 * $j + 2 }) ==(($this->_col >>  0) & 0xFF)
					) {
					$Locals_ext = '!\xF9\x04' . chr(($this->_dis << 2) + 1) .
									chr(($d >> 0) & 0xFF) . chr(($d >> 8) & 0xFF) . chr($j) . '\x0';
					break;
				}
			}
		}
		switch($Locals_tmp { 0 }) {
			case '!':
				$Locals_img = substr($Locals_tmp, 8, 10);
				$Locals_tmp = substr($Locals_tmp, 18, strlen($Locals_tmp) - 18);
				break;
			case ',':
				$Locals_img = substr($Locals_tmp, 0, 10);
				$Locals_tmp = substr($Locals_tmp, 10, strlen($Locals_tmp) - 10);
				break;
		}
		if (ord($this->_buf[$i] { 10 }) & 0x80 && $this->_img > -1) {
			if ($Global_len == $Locals_len) {
				if (hu_gifs_GIFEncoder::_blockCompare($Global_rgb, $Locals_rgb, $Global_len)) {
					$this->_gif .=($Locals_ext . $Locals_img . $Locals_tmp);
				}
				else {
					/*
					 * XY Padding...
					 */
					if ($this->_sig == 1) {
						$Locals_img { 1 } = chr($this->_ofs[$i][0] & 0xFF);
						$Locals_img { 2 } = chr(($this->_ofs[$i][0] & 0xFF00) >> 8);
						$Locals_img { 3 } = chr($this->_ofs[$i][1] & 0xFF);
						$Locals_img { 4 } = chr(($this->_ofs[$i][1] & 0xFF00) >> 8);
					}
					$byte  = ord($Locals_img { 9 });
					$byte |= 0x80;
					$byte &= 0xF8;
					$byte |=(ord($this->_buf[0] { 10 }) & 0x07);
					$Locals_img { 9 } = chr($byte);
					$this->_gif .=($Locals_ext . $Locals_img . $Locals_rgb . $Locals_tmp);
				}
			}
			else {
				/*
				 * XY Padding...
				 */
				if ($this->_sig == 1) {
					$Locals_img { 1 } = chr($this->_ofs[$i][0] & 0xFF);
					$Locals_img { 2 } = chr(($this->_ofs[$i][0] & 0xFF00) >> 8);
					$Locals_img { 3 } = chr($this->_ofs[$i][1] & 0xFF);
					$Locals_img { 4 } = chr(($this->_ofs[$i][1] & 0xFF00) >> 8);
				}
				$byte  = ord($Locals_img { 9 });
				$byte |= 0x80;
				$byte &= 0xF8;
				$byte |=(ord($this->_buf[$i] { 10 }) & 0x07);
				$Locals_img { 9 } = chr($byte);
				$this->_gif .=($Locals_ext . $Locals_img . $Locals_rgb . $Locals_tmp);
			}
		}
		else {
			$this->_gif .=($Locals_ext . $Locals_img . $Locals_tmp);
		}
		$this->_img  = 1;
	}

	private function _addFooter() {
		$this->_gif .= ';';
	}

	private static function _blockCompare($GlobalBlock, $LocalBlock, $Len) {

		for($i = 0; $i < $Len; $i++) {
			if (
					$GlobalBlock { 3 * $i + 0 } != $LocalBlock { 3 * $i + 0 } ||
					$GlobalBlock { 3 * $i + 1 } != $LocalBlock { 3 * $i + 1 } ||
					$GlobalBlock { 3 * $i + 2 } != $LocalBlock { 3 * $i + 2 }
				) {
					return(0);
			}
		}

		return(1);
	}

	private static function _word($int) {
		return(chr($int & 0xFF) . chr(($int >> 8) & 0xFF));
	}

	public function getAnimation() {
		return($this->_gif);
	}
}
