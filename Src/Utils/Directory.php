<?php

namespace Emma\Common\Utils;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 4/9/2016
 * Time: 8:53 PM
 */
class Directory
{

    /**
     * Returns a home directory of current user.
     * @return string
     */
    public static function get_user_directory()
    {
        if (isset($_SERVER['HOMEDRIVE'])) return $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
        else return $_SERVER['HOME'];
    }


    /**
     * @param $dir_path
     */
    public static function makeDir($dir_path)
    {
        $delimiter = StringManagement::contains("\\", $dir_path) ? "\\" : "/";
        $paths = explode($delimiter, $dir_path);
        $paths = array_filter($paths);
        if ($paths[0] == "..") {       //If absolute path
            array_shift($paths);
            $paths[0] = "../" . $paths[0];
        }
        $newpath = "";
        foreach ($paths as $dir) {
            $newpath .= $dir;
            if (!is_dir($newpath)) {
                mkdir($newpath);
            }
            $newpath .= $delimiter;
        }
    }


    /**
     * Removes a directory (and its contents) recursively.
     *
     * Contributed by Askar (ARACOOL) <https://github.com/ARACOOOL>
     *
     * @param  string $dir The directory to be deleted recursively
     * @param  bool $traverseSymlinks Delete contents of symlinks recursively
     * @return bool
     * @throws \RuntimeException
     */
    public static function rmdir($dir, $traverseSymlinks = false)
    {
        if (!file_exists($dir)) {
            return true;
        } elseif (!is_dir($dir)) {
            throw new \RuntimeException('Given path is not a directory');
        }

        if (!is_link($dir) || $traverseSymlinks) {
            foreach (scandir($dir) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $currentPath = $dir . '/' . $file;

                if (is_dir($currentPath)) {
                    self::rmdir($currentPath, $traverseSymlinks);
                } elseif (!unlink($currentPath)) {
                    // @codeCoverageIgnoreStart
                    throw new \RuntimeException('Unable to delete ' . $currentPath);
                    // @codeCoverageIgnoreEnd
                }
            }
        }

        // Windows treats removing directory symlinks identically to removing directories.
        if (is_link($dir) && !defined('PHP_WINDOWS_VERSION_MAJOR')) {
            if (!unlink($dir)) {
                // @codeCoverageIgnoreStart
                throw new \RuntimeException('Unable to delete ' . $dir);
                // @codeCoverageIgnoreEnd
            }
        } else {
            if (!rmdir($dir)) {
                // @codeCoverageIgnoreStart
                throw new \RuntimeException('Unable to delete ' . $dir);
                // @codeCoverageIgnoreEnd
            }
        }

        return true;
    }


    /**
     * Returns size of a given directory in bytes.
     *
     * @param string $dir
     * @return integer
     */
    public static function directory_size($dir)
    {
        $dir = rtrim(str_replace('\\', '/', $dir), DIRECTORY_SEPARATOR);

        if (is_dir($dir) === true) {
            $totalSize = 0;
            $os = strtoupper(substr(PHP_OS, 0, 3));
            // If on a Unix Host (Linux, Mac OS)
            if ($os !== 'WIN') {
                $io = popen('/usr/bin/du -sb ' . $dir, 'r');
                if ($io !== false) {
                    $totalSize = intval(fgets($io, 80));
                    pclose($io);
                    return $totalSize;
                }
            }
            // If on a Windows Host (WIN32, WINNT, Windows)
            if ($os === 'WIN' && extension_loaded('com_dotnet')) {
                $obj = new \COM('scripting.filesystemobject');
                if (is_object($obj)) {
                    $ref = $obj->getfolder($dir);
                    $totalSize = $ref->size;
                    $obj = null;
                    return $totalSize;
                }
            }
            // If System calls didn't work, use slower PHP 5
            if (class_exists('FilesystemIterator')) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir,
                    \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS));
            } else {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            }

            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }
            return $totalSize;
        } else if (is_file($dir) === true) {
            return filesize($dir);
        } else {
            return 0;
        }
    }


    /**
     * Returns all paths inside a directory.
     * @param string $dir
     * @return array
     */
    public static function directory_contents($dir)
    {
        $contents = array();
        if (class_exists('RecursiveIteratorIterator')) {
            if (class_exists('FilesystemIterator')) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir,
                    \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS));
            } else {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            }
            foreach ($files as $pathname => $fi) {
                $contents[] = $pathname;
            }
        } else {
            $contents = glob($dir . "\\*");
        }

        natsort($contents);
        return $contents;
    }


}