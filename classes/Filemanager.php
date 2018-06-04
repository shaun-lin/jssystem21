<?php

require_once __DIR__ .'/Adapter.php';
require_once __DIR__ .'/FilemanagerSetting.php';

class Filemanager extends Adapter
{
    const DEFAULT_FIELDS = [
        'file_id' => null,
        'file_category' => 0,
        'file_relation' => 0,
        'file_name' => '',
        'file_storage' => '',
        'file_dir' => '',
        'file_creator' => 0,
        'file_created' => '',
        'file_deleted_by' => 0,
        'file_deleted' => null
    ];

    public $storage = '';

    public function __construct($id=0, $key='file_id', $storage='')
    {
        $this->storage = $storage;
        $this->setting = new FilemanagerSetting();

        parent::__construct($id, $key);
    }

    public function storeAttachment($sourcePath='', $destPath='')
    {
        $dirName = dirname($destPath);

        if (!is_dir($dirName)) {
            mkdir($dirName, 0777, true);
        }

        move_uploaded_file($sourcePath, $destPath);
    }

    public function getPath()
    {
        if ($this->getId()) {
            $storagePath = $this->setting->get('storage', $this->getVar('file_storage'));
            $dirPath = empty($this->getVar('file_dir')) ? '' : ('/'. $this->getVar('file_dir'));
            $path = $storagePath . $dirPath .'/'. $this->getId();
            return $path;
        }

        return '';
    }

    public function getThumbPath($item=[])
    {
        $path = '';

        if (isset($item['file_id'])) {
            $storagePath = $this->setting->get('storage', $item['file_storage']);
            $dirPath = empty($item['file_dir']) ? '' : ('/'. $item['file_dir']);
            $path = $storagePath . $dirPath .'-thumb/'. $item['file_id'];
        } else if ($this->getId()) {
            $storagePath = $this->setting->get('storage', $this->getVar('file_storage'));
            $dirPath = empty($this->getVar('file_dir')) ? '' : ('/'. $this->getVar('file_dir'));
            $path = $storagePath . $dirPath .'-thumb/'. $this->getId();
        }

        return $path;
    }

    public function getExtractPath($item=[])
    {
        $path = '';
        
        if (isset($item['file_id'])) {
            $storagePath = $this->setting->get('storage', $item['file_storage']);
            $dirPath = empty($item['file_dir']) ? '' : ('/'. $item['file_dir']);
            $path = $storagePath . $dirPath .'-extract/'. $item['file_id'];
        } else if ($this->getId()) {
            $storagePath = $this->setting->get('storage', $this->getVar('file_storage'));
            $dirPath = empty($this->getVar('file_dir')) ? '' : ('/'. $this->getVar('file_dir'));
            $path = $storagePath . $dirPath .'-extract/'. $this->getId();
        }

        return $path;
    }

    public function getThumbFile($item=[])
    {
        $files = [];

        $thumbDir = $this->getThumbPath($item);

        if (is_dir($thumbDir)) {
            foreach (scandir($thumbDir) as $filename) {
                if (EndsWith(strtolower($filename), '.jpg')) {
                    if (strpos(PHP_OS, "WIN") !== false) {     
                        $files[] = base64_encode(mb_convert_encoding($filename, "UTF-8", "big5"));
                    } else {
                        $files[] = base64_encode($filename);
                    }
                }
            }
        }

        return $files;
    }

    public function getExtractFile($item=[])
    {
        $files = [];

        $extractDir = $this->getExtractPath($item);

        if (is_dir($extractDir)) {
            foreach (scandir($extractDir) as $filename) {
                if (StartsWith(strtolower($filename), '7zip-')) {
                    $files[] = $filename;
                }
            }
        }

        return $files;
    }

    public function getThumbType($filename='')
    {
        $name = empty($filename) ? $this->getVar('file_name') : $filename;

        preg_match("|\.([A-Za-z0-9]{2,4})$|i", $name, $fileSuffix);

        switch(strtolower($fileSuffix[1]))
        {
            case 'ppt':
            case 'pptx':
                return "PowerPoint.Application";
            case '7z':
            case 'gz':
            case 'gzip':
            case 'tar':
            case 'rar':
            case 'zip':
                return '7-Zip';
            default:
                return null;
        }
    }

    public function download($filepath='', $method='attachment')
    {
        $path = empty($filepath) ? ($this->getId() ? $this->getPath() : '') : $filepath;
        $name = empty($filepath) ? ($this->getId() ? $this->getVar('file_name') : '') : basename($filepath);

        if (file_exists($path)) {
            ini_set('display_errors', 0);
            while (@ob_end_clean());
            header("Pragma: public");
            header("Expires: 0");
            header('Cache-Control: must-revalidate');
            
            header('Content-Description: File Transfer');
            header("Content-disposition: {$method}; filename=\"".addslashes($name)."\"; filename*=utf-8''".rawurlencode($name));
            header("Content-Type: ". $this->getMimeType($name));
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($path));

            @ob_clean();
            @flush();
            readfile($path);

            exit;
        }
        
        return null;
    }

    public function getAllFilesWithFolder($directory='')
    {
        $files = '';

        if (is_dir($directory)) {
            foreach (scandir($directory) as $filename) {
                if (in_array($filename, ['.', '..', '.DS_Store', '__MACOSX'])) {
                    continue;
                } else if (is_dir($directory .'/'. $filename)) {
                    $files .= $this->getAllFilesWithFolder($directory .'/'. $filename);
                } else {
                    $files .= $directory .'/'. $filename .',';
                }
            }
        }

        return $files;
    }

    public function getMimeType($filename='')
    {
        preg_match("|\.([A-Za-z0-9]{2,4})$|i", $filename, $fileSuffix);

        switch(strtolower($fileSuffix[1]))
        {
			case "js":
                return "application/x-javascript";
            case "json":
                return "application/json";
            case "jpg":
            case "jpeg":
            case "jpe":
                return "image/jpg";
            case "png":
            case "gif":
            case "bmp":
            case "tiff":
			case "tif":
                return "image/".strtolower($fileSuffix[1]);
            case "css":
                return "text/css";
            case "xml":
                return "application/xml";
            case "doc":
            case "docx":
                return "application/msword";
            case "docx":
            	return "pplication/vnd.openxmlformats-officedocument.wordprocessingml.document";
			case "csv":
				return "text/csv";
            case "xls":
            case "xlt":
            case "xlm":
            case "xld":
            case "xla":
            case "xlc":
            case "xlw":
            case "xll":
                return "application/vnd.ms-excel";
			case "xlsx":
				return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            case "ppt":
            case "pps":
                return "application/vnd.ms-powerpoint";
			case "pptx":
				return "application/vnd.openxmlformats-officedocument.presentationml.presentation";
			case "xltx":
				return "application/vnd.openxmlformats-officedocument.spreadsheetml.template";
			case "potx":
				return "application/vnd.openxmlformats-officedocument.presentationml.template";
			case "ppsx":
				return "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
			case "sldx":
				return "application/vnd.openxmlformats-officedocument.presentationml.slide";
			case "dotx":
				return "application/vnd.openxmlformats-officedocument.wordprocessingml.template";
			case "xlam":
				return "application/vnd.ms-excel.addin.macroEnabled.12";
			case "xlsb":
				return "application/vnd.ms-excel.sheet.binary.macroEnabled.12";
			case "rtf":
                return "application/rtf";
            case "pdf":
                return "application/pdf";
            case "html":
            case "htm":
            case "php":
                return "text/html";
            case "txt":
                return "text/plain";
            case "mpeg":
            case "mpg":
            case "mpe":
                return "video/mpeg";
            case "mp3":
                return "audio/mpeg3";
            case "wav":
                return "audio/wav";
            case "aiff":
            case "aif":
                return "audio/aiff";
            case "avi":
                return "video/msvideo";
            case "wmv":
                return "video/x-ms-wmv";
            case "mov":
                return "video/quicktime";
            case "zip":
                return "application/zip";
            case "tar":
                return "application/x-tar";
            case "swf":
                return "application/x-shockwave-flash";
            case "zip":
                return "application/zip";
            case 'rar':
                return "application/x-rar-compressed";
            default :
                return "application/octec-stream";
        }
    }
}
