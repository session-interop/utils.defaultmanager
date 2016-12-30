<?php
declare(strict_types=1);

namespace Interop\Session\Manager\Utils\DefaultManager;

use Interop\Session\Configuration\SessionConfigurationInterface;

class SessionConfiguration implements SessionConfigurationInterface {
  private $defaultSessionParams = null;

  protected function getDefaultSessionParams(): array {
    if (!$this->defaultSessionParams) {
      $this->defaultSessionParams = session_get_cookie_params();
    }
    return $this->defaultSessionParams;
  }

  public function getSessionHandler(): ?\SessionHandlerInterface {
    return null;
  }

  public function getCookieName(): string {
    return session_name();
  }

  public function getCookieLifetime(): int {
    return intval($this->getDefaultSessionParams()["lifetime"]);
  }

  public function getCookiePath(): string {
    return $this->getDefaultSessionParams()["path"];
  }

  public function getCookieDomain(): string {
    $domain = ini_get("session.cookie_domain");
    return $domain ?? "";
  }

  public function getGcMaxLifeTime(): int {
    $lt = ini_get("session.gc_maxlifetime");
    return $lt ? intval($lt) : 1440 ;
  }

  public function getSavePath(): string {
    return session_save_path();
  }

  public function isCookieOnlySecure(): bool {
      return boolval($this->getDefaultSessionParams()["secure"]);

  }
  public function isCookieHttpOnly(): bool {
    return boolval($this->getDefaultSessionParams()["httponly"]);
  }
}
