<?php

while (true) {
  if (LMSObserver::logging()) {
    LMSObserver::log("database_calls", "file.php@L123", 1 / 7);
  }
  sleep(1);
}

class LMSObserver
{
  private static ?Socket $socket = null;
  private static bool $connected = false;

  public static function logging(bool $connect = true): bool
  {
    if (!self::$socket) {
      self::$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) ?: null;
      self::$connected = false;
    }
    if (!self::$connected) {
      if ($connect) {
        self::$connected = @socket_connect(self::$socket, 'localhost', '7777');
      }
    }
    return self::$connected;
  }

  public static function log(string $name, string $tag, float $duration)
  {
    if (self::$connected) {
      $line = sprintf("%s:%s:%g", $name, $tag, $duration);
      if (!@socket_write(self::$socket, $line . "\n", strlen($line) + 1)) {
        self::$socket = null;
        self::$connected = false;
      }
    }
  }
}
