<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

abstract class tubepress_test_integration_IntegrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $_options;

    /**
     * @var \Symfony\Component\Process\Process
     */
    private static $_WEB_SERVER_PROCESS;

    /**
     * @var string
     */
    private static $_WEB_SERVER_ADDRESS = 'localhost:54321';

    public final function setUp()
    {
        $this->_options = array();
    }

    protected function setOptions(array $opts)
    {
        $this->_options = $opts;
    }

    protected function request($method, $path = 'index.php', array $params = array(), $debug = false)
    {
        $client      = \JonnyW\PhantomJs\Client::getInstance();
        $request     = $client->getMessageFactory()->createRequest();
        $response    = $client->getMessageFactory()->createResponse();
        $debugParams = array('tubepress_debug' => $debug ? 'true' : 'false', 'XDEBUG_SESSION_START' => 'true');
        $encodedOpts = base64_encode(serialize($this->_options));
        $finalParams = array_merge($debugParams, array('options' => $encodedOpts), $params);
        $urlFactory  = new tubepress_url_impl_puzzle_UrlFactory();
        $url         = $urlFactory->fromString('http://' . self::$_WEB_SERVER_ADDRESS)->setPath($path)->setQuery($finalParams);

        $request->setMethod($method);
        $request->setUrl($url->toString());

        $client->getEngine()->setPath(self::_getProjectRoot() . '/bin/phantomjs');

        $client->send($request, $response);

        if ($response->getStatus() !== 200) {

            throw new RuntimeException(sprintf('%s returned HTTP %s: %s', $url, $response->getStatus(), $response->getContent()));
        }

        return $response;
    }

    public static function bootstrap()
    {
        if (isset(self::$_WEB_SERVER_PROCESS)) {

            return;
        }

        self::_buildTubePress();
        self::_startWebServer();

        register_shutdown_function(array('tubepress_test_integration_IntegrationTest', 'onShutdown'));
    }

    public static function onShutdown()
    {
        $lockFile = self::_getLockFile();

        if (!file_exists($lockFile)) {

            return;
        }

        unlink($lockFile);
    }

    private static function _buildTubePress()
    {
        $finder  = new \Symfony\Component\Finder\Finder();
        $distDir = sprintf('%s/build/dist', self::_getProjectRoot());
        $matches = iterator_to_array($finder->in($distDir)->files()->name('*.zip')->getIterator());

        if (count($matches) !== 1) {

            self::_runProcess(
                'php build/php/scripts/build.php package',
                false,
                self::_getProjectRoot()
            );

            $finder  = new \Symfony\Component\Finder\Finder();
            $matches = iterator_to_array($finder->in($distDir)->files()->name('*.zip')->getIterator());

            if (count($matches) !== 1) {

                throw new RuntimeException('Failed to build TubePress');
            }
        }
    }

    /**
     * @param string      $command
     * @param bool        $async
     * @param string|null $cwd
     *
     * @return \Symfony\Component\Process\Process
     */
    private static function _runProcess($command, $async = false, $cwd = null)
    {
        $process = new \Symfony\Component\Process\Process($command, $cwd);

        self::_log(sprintf('Running command: %s', $command));

        if ($async) {

            $process->start();

            sleep(1);

            if (!$process->isRunning()) {

                throw new RuntimeException(sprintf('Failed to start %s: %s', $command, $process->getErrorOutput()));
            }

        } else {

            $process->run();

            if (!$process->isSuccessful()) {

                throw new RuntimeException(sprintf('Failed to run %s: %s', $command, $process->getErrorOutput()));
            }
        }

        return $process;
    }

    private static function _getProjectRoot()
    {
        return realpath(__DIR__ . '/../../../../../..');
    }

    private static function _log($msg)
    {
        printf("$msg\n");
    }

    private static function _startWebServer()
    {
        if (!extension_loaded('pcntl')) {

            throw new RuntimeException('Missing pcntl extension');
        }

        if (self::_isOtherServerProcessRunning()) {

            throw new RuntimeException('Another server is already running on ' . self::$_WEB_SERVER_ADDRESS);
        }

        $pid = pcntl_fork();

        if ($pid < 0) {

            throw new RuntimeException('Unable to start the server process.');
        }

        if ($pid > 0) {

            self::_log(sprintf('Web server listening on ' . self::$_WEB_SERVER_ADDRESS));

            return;
        }

        if (posix_setsid() < 0) {

            throw new RuntimeException('Unable to set the child process as session leader');
        }

        if (null === $process = self::_createServerProcess(sprintf('%s/tests/integration/fixtures/webroot', self::_getProjectRoot()))) {

            throw new RuntimeException('Failed to start server');
        }

        $process->disableOutput();
        $process->start();

        $lockFile = self::_getLockFile();

        touch($lockFile);

        if (!$process->isRunning()) {

            unlink($lockFile);

            throw new RuntimeException('Unable to start the server process');
        }

        // stop the web server when the lock file is removed
        while ($process->isRunning()) {

            if (!file_exists($lockFile)) {

                $process->stop();
            }

            sleep(1);
        }
    }

    private static function _getLockFile()
    {
        return sys_get_temp_dir() . '/' . strtr(self::$_WEB_SERVER_ADDRESS, '.:', '--') . '.pid';
    }

    private static function _createServerProcess($documentRoot)
    {
        $finder = new \Symfony\Component\Process\PhpExecutableFinder();

        if (false === $binary = $finder->find()) {

            throw new RuntimeException('Unable to find PHP binary to start server.');
        }

        $script = implode(' ', array_map(array('Symfony\Component\Process\ProcessUtils', 'escapeArgument'), array(
            $binary,
            '-S',
            self::$_WEB_SERVER_ADDRESS
        )));

        return new \Symfony\Component\Process\Process('exec ' . $script, $documentRoot, null, null, null);
    }

    private function _isOtherServerProcessRunning()
    {
        $lockFile = self::_getLockFile();

        if (file_exists($lockFile)) {

            return true;
        }

        list($hostname, $port) = explode(':', self::$_WEB_SERVER_ADDRESS);
        $fp                    = @fsockopen($hostname, $port, $errno, $errstr, 5);

        if (false !== $fp) {

            fclose($fp);

            return true;
        }

        return false;
    }
}