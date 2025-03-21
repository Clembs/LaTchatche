<?php

namespace App\Core;

abstract class Model implements \JsonSerializable
{
  abstract public function jsonSerialize(): array;
}