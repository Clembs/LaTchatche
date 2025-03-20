<?php

namespace App\Core;

abstract class Model implements \JsonSerializable
{
  abstract public static function findById(int $id): ?self;

  /**
   * @return self[]
   */
  abstract public static function findAll(): array;

  abstract public static function create(self $data): self;

  abstract public static function update(self $data): void;

  abstract public static function delete(int $id): void;

  abstract public function jsonSerialize(): array;
}