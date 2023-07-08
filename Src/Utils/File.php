<?php

namespace Emma\Common\Utils;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 4/9/2016
 * Time: 8:52 PM
 */
class File
{
    /**
     * @param $filePath
     * @return string
     */
    public static function getGoogleDocumentViewerURL($filePath)
    {
        return "https://docs.google.com/gview?url=$filePath&embedded=true";
    }

    /**
     * @param $file
     * @return mixed
     */
    public static function getFileInfo($file)
    {
        return pathinfo($file);
    }

    /**
     * @param $path
     */
    public static function makeDirectory(string $path, bool $recursive = false)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, $recursive);
        }
    }

    /**
     * @return string
     */
    public static function newLineTabbed()
    {
        return self::newLine() . self::tab();
    }

    /**
     * @return string
     */
    public static function tab()
    {
        return "    ";
    }

    /**
     * @return string
     */
    public static function newLine()
    {
        return "\n";
    }

    /**
     * @return string
     */
    public static function EndOfLine()
    {
        return PHP_EOL;
    }

    /**
     * Return the file extension of the given filename.
     *
     * @param  string $file_path
     * @return string
     */
    public static function getFileExtension($file_path)
    {
        return pathinfo($file_path, PATHINFO_EXTENSION);
    }

    /**
     * @param $file_path
     * @return mixed
     */
    public static function getBasename($file_path)
    {
        return pathinfo($file_path, PATHINFO_BASENAME);
    }

    /**
     * @param $file_path
     * @return mixed
     */
    public static function getDirectory($file_path)
    {
        return pathinfo($file_path, PATHINFO_DIRNAME);
    }


    /**
     * @param $file
     * @param int $delete
     */
    public static function previewFileInBrowser($file, $delete = 0)
    {
        $filename = basename($file);
        $type = mime_type_from_basename($filename);
        header('Content-type: ' . $type);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');
        @readfile($file);
        if ($delete) {
            unlink($file);
        }
        exit();
    }

    /**
     * @param $file
     * @param int $delete
     */
    public static function viewOrDownloadFile($file, $delete = 0)
    {
        self::previewFileInBrowser($file, $delete);
    }


    /**
     * @param $file
     * @param int $delete
     */
    public static function download($file, $delete = 0)
    {
        //Download from server
        $filename = basename($file);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=$filename");
        header("Content-Transfer-Encoding: binary ");
        readfile($file);
        if ($delete) {
            unlink($file);
        }
    }

    /**
     * @param $source
     * @param $target
     * @return void
     */
    public function copy(string $source, string $target)
    {
        if (is_dir($source)) {
            @mkdir($target);
            $d = dir($source);
            while ( false !== ( $entry = $d->read() ) ) {
                if ( $entry == '.' || $entry == '..' ) {
                    continue;
                }

                $sourceFileOrDirectory = $source . '/' . $entry;
                if (is_dir($sourceFileOrDirectory)) {
                    $this->copy($sourceFileOrDirectory, $target . '/' . $entry);
                    continue;
                }
                copy($sourceFileOrDirectory, $target . '/' . $entry);
            }
            $d->close();
        } else {
            copy($source, $target);
        }
    }

    
}