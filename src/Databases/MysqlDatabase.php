<?php namespace BackupManager\Databases;

/**
 * Class MysqlDatabase
 * @package BackupManager\Databases
 */
class MysqlDatabase implements Database {

    /** @var array */
    private $config;

    /**
     * @param $type
     * @return bool
     */
    public function handles($type) {
        return strtolower($type) == 'mysql';
    }

    /**
     * @param array $config
     * @return null
     */
    public function setConfig(array $config) {
        $this->config = $config;
    }

    /**
     * @param $outputPath
     * @return string
     */
    public function getDumpCommandLine($outputPath) {
    	$extras = [];
    	if (array_key_exists('singleTransaction', $this->config) && $this->config['singleTransaction'] === true) {
    		$extras[] = '--single-transaction';
    	}
    	$command = 'mysqldump --routines '.implode(' ', $extras).' --host=%s --port=%s --user=%s --password=%s %s > %s';
        return sprintf($command,
            escapeshellarg($this->config['host']),
            escapeshellarg($this->config['port']),
            escapeshellarg($this->config['user']),
            escapeshellarg($this->config['pass']),
            escapeshellarg($this->config['database']),
            escapeshellarg($outputPath)
        );
    }

    /**
     * @param $inputPath
     * @return string
     */
    public function getRestoreCommandLine($inputPath) {
        return sprintf('mysql --host=%s --port=%s --user=%s --password=%s %s -e "source %s"',
            escapeshellarg($this->config['host']),
            escapeshellarg($this->config['port']),
            escapeshellarg($this->config['user']),
            escapeshellarg($this->config['pass']),
            escapeshellarg($this->config['database']),
            $inputPath
        );
    }
}
