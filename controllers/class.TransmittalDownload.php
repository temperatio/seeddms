<?php
/**
 * Implementation of Transmittal Download controller
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2010-2013 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Class which does the busines logic for downloading a transmittal
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2010-2013 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_Controller_TransmittalDownload extends SeedDMS_Controller_Common {

	public function run() {
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$transmittal = $this->params['transmittal'];

		$items = $transmittal->getItems();
		if($items) {
			include("../inc/inc.ClassDownloadMgr.php");
			$downmgr = new SeedDMS_Download_Mgr();

			foreach($items as $item) {
				$content = $item->getContent();
				$document = $content->getDocument();
				if ($document->getAccessMode($user) >= M_READ) {
					$downmgr->addItem($content);
				}
			}

			$filename = tempnam('/tmp', '');
			$downmgr->createArchive($filename);
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($filename));
			header("Content-Disposition: attachment; filename=\"export-" .date('Y-m-d') . ".zip\"");
			header("Content-Type: application/zip");
			header("Cache-Control: must-revalidate");

			readfile($filename);
			unlink($filename);
			exit;
		}
	}
}

