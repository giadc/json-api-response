<?php

namespace App;

use Giadc\JsonApiResponse\Interfaces\JsonApiResource;

class TestEntity implements JsonApiResource
{
    private $id;

    private string $name;

    private string $title;

    public function __construct($id, $name, $title = 'asdf')
    {
        $this->id   = $id;
        $this->name = $name;
        $this->title = $title;
    }

    /**
     * {@inheritDoc}
     */
    public function id()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function title(): string
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function getResourceKey(): string
    {
        return 'tests';
    }
}
