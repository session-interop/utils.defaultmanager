<?php
declare(strict_types=1);

namespace Interop\Session\Manager\Utils\DefaultManager;

use Interop\Session\Manager\SessionManagerInterface;
use Interop\Session\Configuration\SessionConfigurationInterface;


class DefaultSessionManager implements SessionManagerInterface {

  private $configuration = null;

  public function __construct(SessionConfigurationInterface $sessionConfiguration) {
    if (isset($_SESSION)) {
			throw new \Exception("This manager should not have any concurrency session management");
		}

    session_set_save_handler($sessionConfiguration->getSessionHandler(), false);
		session_name($sessionConfiguration->getCookieName());
    session_save_path($sessionConfiguration->getSavePath());
		ini_set("session.cookie_lifetime", strval($sessionConfiguration->getCookieLifetime()));
		ini_set("session.cookie_path", $sessionConfiguration->getCookiePath());
		ini_set("session.cookie_domain", $sessionConfiguration->getCookieDomain());
		ini_set("session.cookie_secure", strval($sessionConfiguration->isCookieOnlySecure()));
		ini_set("session.cookie_httponly", strval($sessionConfiguration->isCookieHttpOnly()));
		ini_set("session.gc_maxlifetime", strval($sessionConfiguration->getGcMaxLifeTime()));
    $this->configuration = $sessionConfiguration;
  }

  private function initSession(): void {
        session_start();
  }
  private function commit(): void {
      session_write_close();
  }

  public function clear(): void {
    session_unset();
    $this->ensureCommit();
  }

  public function ensureSessionHasStart(): void {
    if ($this->isSessionActive()) {
      $this->initSession();
    }
  }

  public function ensureCommit(): void {
    if ($this->isSessionActive()) {
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

  public function isSessionActive(): bool {
    return session_status() === PHP_SESSION_NONE;
  }

  public function __destroy() {
    //$this->silentCommit();
  }


}
