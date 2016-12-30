<?php
declare(strict_types=1);

namespace Interop\Session\Manager\Utils\DefaultManager;

use Interop\Session\Manager\SessionManagerInterface;
use Interop\Session\Configuration\SessionConfigurationInterface;


class DefaultSessionManager implements SessionManagerInterface {

  private $configuration = null;

  public function __construct(?SessionConfigurationInterface $sessionConfiguration) {
    if ($this->isSessionActive()) {
      return;
		}

    if ($this->isSessionDisabled()) {
      throw new \Exception("Session must not be disabled to use it");
    }

    if (!isset($_SESSION) && $sessionConfiguration) {
  		ini_set("session.cookie_lifetime", strval($sessionConfiguration->getCookieLifetime()));
  		ini_set("session.cookie_path", $sessionConfiguration->getCookiePath());
  		ini_set("session.cookie_domain", $sessionConfiguration->getCookieDomain());
  		ini_set("session.cookie_secure", strval($sessionConfiguration->isCookieOnlySecure()));
  		ini_set("session.cookie_httponly", strval($sessionConfiguration->isCookieHttpOnly()));
  		ini_set("session.gc_maxlifetime", strval($sessionConfiguration->getGcMaxLifeTime()));
      $sessionHandler = $sessionConfiguration->getSessionHandler();
      if ($sessionHandler) {
        session_set_save_handler($sessionHandler, false);
      }
      session_name($sessionConfiguration->getCookieName());
      session_save_path($sessionConfiguration->getSavePath());
    }
    $this->configuration = $sessionConfiguration;
  }

  private function initSession(): void {
        session_start();
  }
  private function commit(): void {
      session_write_close();
  }

  public function clear(): void {
    if ($this->isSessionDisabled()) {
      throw new \Exception("Session must not be disabled to use it");
    }
    session_unset();
    $this->ensureCommit();
  }

  public function ensureSessionHasStart(): void {
    if ($this->isSessionDisabled()) {
      throw new \Exception("Session must not be disabled to use it");
    }
    if ($this->isSessionInactive()) {
      $this->initSession();
    }
  }

  public function ensureCommit(): void {
    if ($this->isSessionDisabled()) {
      throw new \Exception("Session must not be disabled to use it");
    }
    if ($this->isSessionInactive()) {
      $this->ensureSessionHasStart();
      $this->commit();
    }
    else {
      $this->commit();
      $this->ensureSessionHasStart();
    }
  }

  public function close(): void {
    session_abort();
  }
  public function isSessionDisabled(): bool {
    return session_status() === PHP_SESSION_DISABLED;
  }

  public function isSessionActive(): bool {
    return !$this->isSessionInactive();
  }

  public function isSessionInactive(): bool {

      return session_status() === PHP_SESSION_NONE;
  }

  public function __destroy() {
  }


}
