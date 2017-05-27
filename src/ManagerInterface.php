<?php

namespace ServiceMap\Manager;

interface ManagerInterface
{
  /**
   * Description in here...
   *
   *
   */
  public function connection($name = null);

  /**
   * Description in here...
   *
   *
   */
  public function reconnect($name = null);

  /**
   * Description in here...
   *
   *
   */
  public function disconnect($name = null);

  /**
   * Description in here...
   *
   *
   */
  public function getConnectionConfig($name);

  /**
   * Description in here...
   *
   *
   */
  public function getDefaultConnection();

  /**
   * Description in here...
   *
   *
   */
  public function setDefaultConnection($name);

  /**
   * Description in here...
   *
   *
   */
  public function extend($name, $resolver);

  /**
   * Description in here...
   *
   *
   */
  public function getConnections();
}
