<?php

namespace ServiceMap\Manager;

use Illuminate\Contracts\Config\Repository;
use InvalidArgumentException;

abstract class AbstractManager implements ManagerInterface
{
  /**
   * Description in here...
   *
   *
   */
  protected $config;

  /**
   * Description in here...
   *
   *
   */
  protected $connections = [];

  /**
   * Description in here...
   *
   *
   */
  protected $extensions = [];

  /**
   * Description in here...
   *
   *
   */
  public function __construct(Repository $config)
  {
    $this->config = $config;
  }

  /**
   * Description in here...
   *
   *
   */
  public function connection($name = null)
  {
    $name = $name ?: $this->getDefaultConnection();

    if (!isset($this->connections[$name])) {
      $this->connections[$name] = $this->makeConnection($name);
    }

    return $this->connections[$name];
  }

  /**
   * Description in here...
   *
   *
   */
  public function reconnect($name = null)
  {
    $name = $name ?: $this->getDefaultConnection();

    $this->disconnect($name);

    return $this->connection($name);
  }

  /**
   * Description in here...
   *
   *
   */
  public function disconnect($name = null)
  {
    $name = $name ?: $this->getDefaultConnection();

    unset($this->connections[$name]);
  }

  /**
   * Description in here...
   *
   *
   */
  abstract protected function createConnection(array $config);

  /**
   * Description in here...
   *
   *
   */
  protected function makeConnection($name)
  {
    $config = $this->getConnectionConfig($name);

    if (isset($this->extensions[$name])) {
      return call_user_func($this->extensions[$name], $config);
    }

    if ($driver = array_get($config, 'driver')) {
      if (isset($this->extensions[$driver])) {
        return call_user_func($this->extensions[$driver], $config);
      }
    }

    return $this->createConnection($config);
  }

  /**
   * Description in here...
   *
   *
   */
  abstract protected function getConfigName();

  /**
   * Description in here...
   *
   *
   */
  public function getConnectionConfig($name)
  {
    $name = $name ?: $this->getDefaultConnection();

    $connections = $this->config->get($this->getConfigName().'.connections');

    if (!is_array($config = array_get($connections, $name)) && !$config) {
      throw new InvalidArgumentException('Connection [' . $name . '] not configured.');
    }

    $config['name'] = $name;

    return $config;
  }

  /**
   * Description in here...
   *
   *
   */
  public function getDefaultConnection()
  {
    return $this->config->get($this->getConfigName().'.default');
  }

  /**
   * Description in here...
   *
   *
   */
  public function setDefaultConnection($name)
  {
    $this->config->set($this->getConfigName().'.default', $name);
  }

  /**
   * Description in here...
   *
   *
   */
  public function extend($name, $resolver)
  {
    $this->extensions[$name] = $resolver;
  }

  /**
   * Description in here...
   *
   *
   */
  public function getConnections()
  {
    return $this->connections;
  }

  /**
   * Description in here...
   *
   *
   */
  public function getConfig()
  {
    return $this->config;
  }

  /**
   * Dynamically pass methods to the default connection.
   *
   * @param  string $method
   * @param  array $parameters
   * @return mixed
   */
  public function __call($method, $parameters)
  {
    return call_user_func_array([$this->connection(), $method], $parameters);
  }
}
