<?php

// All paths are relative from current folder
$config = [
    'backupFile' => 'backup-no-vendor', // no ext
    'folderToBackup' => '',
    'excludes' => [ // array of paths, gitignore stays
        'web/admin',
        'web/assets',
        'web/files',

        'public_html/admin',
        'public_html/assets',
        'public_html/files',

        'admin-client/build',
        'admin-client/node_modules',
        'admin-client/package-lock.json',

//        'ckeditor5/build',
        'ckeditor5/node_modules',
        'ckeditor5/package-lock.json',

        'client-old',

        'protected/sqlite',
        'protected/temp',
        'protected/runtime',
        'protected/storage',
        'protected/vendor',
        'protected/composer.lock',
    ],
    'usePhar' => true,
    'dbBackup' => false,
    'databases' => [
        1 => [
            'name' => 'dbname',
            'host' => 'localhost',
            'user' => 'dbuser',
            'password' => 'dbpass',
            'tableExclude' => [],
        ],
    ]
];


$backUpper = new BackUpper($config);
$backUpper->makeArchive();

class BackUpper
{
    private $backupFile;
    private $folderToBackup;
    private $excludes = [];
    private $currentDir = __DIR__;
    private $leaveGitIgnore = true; // .gitignore for excluded folders
    private $usePhar = false;
    private $dbBackup = true;
    private $databases = [];
    private $_tempFiles = [];

    public function __construct($config)
    {
        $this->validateConfig($config);

        foreach($config as $name => $value) {
            $this->{$name} = $value;
        }
        $this->backupFile = $this->prepareFilename($this->backupFile);
        $this->folderToBackup = $this->prepareFilename($this->folderToBackup);

        $this->excludes = $this->prepareExcludes($this->excludes);
    }

    public function makeArchive()
    {
        if ($this->dbBackup) {
            $this->makeMySqlBackup();
        }

        if ($this->usePhar) {
            $this->makePharArchive();
        } else {
            $this->makeZipArchive();
        }

        $this->clearTemp();
    }

    private function makePharArchive()
    {
        $tarFilename = $this->getTarName($this->backupFile);
        $tarGzFilename = $this->getTarGzName($this->backupFile);

        if (file_exists($tarFilename)) {
            unlink($tarFilename);
        }
        if(file_exists($tarGzFilename)) {
            unlink($tarGzFilename);
        }

        $filenames = $this->getFilenamesFromFolder($this->folderToBackup);

        $phar = new PharData($tarFilename);
        $phar->buildFromIterator(new ArrayIterator($filenames));
        $phar->compress(Phar::GZ); // make path\to\archive\arch1.tar.gz
        $this->_tempFiles[] = $tarFilename;
    }

    private function makeZipArchive()
    {
        $zipFilename = $this->getZipName($this->backupFile);
        if(file_exists($zipFilename)) {
            unlink($zipFilename);
        }

        $zip = new ZipArchive();
        $zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $filenames = $this->getFilenamesFromFolder($this->folderToBackup);

        foreach ($filenames as $relativeFilename => $filename)
        {
            $zip->addFile($filename, $relativeFilename);
        }

        $zip->close();
    }

    private function getFilenamesFromFolder($folder)
    {
        $dirIterator = new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        $filenames = [];

        foreach ($iterator as $file) {
            $filename = $file->getPathname();
//            $basename = $file->getBasename();
            if ($file->isFile()) {
                if($this->isExcluded($filename))
                    continue;
                $filenames[] = $filename;
            } elseif($file->isDir()) {
                if($this->isExcluded($filename, true)
                    && $this->leaveGitIgnore
                    && file_exists($filename.DIRECTORY_SEPARATOR.'.gitignore')
                ) {
                    $filenames[] = $filename.DIRECTORY_SEPARATOR.'.gitignore';
                }
            }
        }

        $result = [];
        // [relative path] => absolute path
        foreach($filenames as $filename) {
            $result[$this->getRelativePath($filename) ] = $filename;
        }
        return $result;
    }

    private function isExcluded($filename, $strict = false)
    {
        foreach($this->excludes as $exclude) {
            if($strict) {
                if($filename === $exclude) {
                    return true;
                }
            }
            elseif (0 === strpos($filename, $exclude)) {
                return true;
            }
        }
        return false;
    }

    private function getRelativePath($filename)
    {
        return preg_replace('~^'.preg_quote($this->folderToBackup).'~', '', $filename);
    }

    private function prepareFilename($filename)
    {
        $filename = preg_replace('~^'.preg_quote($this->currentDir).'~', '', $filename);
        return $this->currentDir.DIRECTORY_SEPARATOR.trim($filename, DIRECTORY_SEPARATOR);
    }

    private function prepareExcludes($excludes)
    {
        $array = [];
        foreach($excludes as $filename) {
            $filename = $this->prepareFilename($filename);
            $array[] = $filename;
        }
        $array[] = $this->prepareFilename('.idea');
        $array[] = $this->getTarName($this->backupFile);
        $array[] = $this->getTarGzName($this->backupFile);

        return $array;
    }

    private function clearTemp()
    {
        foreach ($this->_tempFiles as $filename) {
            if(file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    private function makeMySqlBackup()
    {
        foreach($this->databases as $database) {
            $this->makeMysqlDatabaseBackup($database['host'], $database['name'], $database['user'], $database['password'], $database['tableExclude']);
        }
    }

    private function makeMysqlDatabaseBackup($host, $name, $user, $password, $tableExclude = [])
    {
        $backupFilename = $this->getSqlGzName($this->prepareFilename($name));
        if(file_exists($backupFilename)){
            unlink($backupFilename);
        }
        $ignoreTableString = '';
        foreach($tableExclude as $table) {
            $ignoreTableString .= " --ignore-table={$name}.{$table}";
        }
//        $databaseStructure = '/usr/bin/mysqldump -h "$DBHOST" --databases --single-transaction --no-data "$database" -u "$DBUSER" ${DBPASS} ${IGNORED_TABLES_STRING}';

        $databaseBackup = exec("mysqldump -h {$host} -u {$user} -p{$password} {$name} {$ignoreTableString} --no-tablespaces --single-transaction=TRUE --add-drop-table=FALSE --insert-ignore | gzip > {$backupFilename}");

        $this->_tempFiles[] = $backupFilename;
    }

    private function validateConfig($config)
    {
        if (!isset($config['backupFile']) || !isset($config['folderToBackup'])) {
            throw new \Exception('BackupFile and folderToBackup can not be null!');
        }
    }

    private function getTarName($filename)
    {
        return $filename.'.tar';
    }

    private function getTarGzName($filename)
    {
        return $filename.'.tar.gz';
    }

    private function getZipName($filename)
    {
        return $filename.'.zip';
    }

    private function getSqlGzName($filename)
    {
        return $filename.'.sql.gz';
    }
}
