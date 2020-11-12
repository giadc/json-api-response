<?php

use App\TestEntity;
use App\TestTransformer;
use Giadc\JsonApiResponse\Exceptions\InvalidAttributesRequested;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author David Hill
 */
class ResourceTransformerTest extends TestCase
{
    public function test_it_uses_default_fields(): void
    {
        $transformer = new TestTransformer();
        $transformer->setDefaultFields(['name']);

        $testEntity = new TestEntity(1, 'name');

        $this->assertEquals(['id' => 1, 'name' => 'name'], $transformer->transform($testEntity));
    }

    public function test_it_includes_only_provided_fields(): void
    {
        $request = Request::create(
            'http://test.com/articles'
                . '?fields[tests]=name'
        );

        $transformer = new TestTransformer($request);
        $testEntity = new TestEntity(1, 'name');

        $this->assertEquals(['id' => 1, 'name' => 'name'], $transformer->transform($testEntity));
    }

    public function test_it_excludes_provided_fields(): void
    {
        $request = Request::create(
            'http://test.com/articles'
                . '?excludes[tests]=name'
        );

        $transformer = new TestTransformer($request);
        $testEntity = new TestEntity(1, 'name');

        $this->assertEquals(['id' => 1, 'title' => 'asdf'], $transformer->transform($testEntity));
    }

    public function test_it_prioritizes_fields(): void
    {
        $request = Request::create(
            'http://test.com/articles'
                . '?fields[tests]=name'
                . '&excludes[tests]=name'
        );

        $transformer = new TestTransformer($request);
        $testEntity = new TestEntity(1, 'name');

        $this->assertEquals(['id' => 1, 'name' => 'name'], $transformer->transform($testEntity));
    }

    public function test_it_throws_exception_when_providing_invalid_fields(): void
    {
        $this->expectException(InvalidAttributesRequested::class);
        $this->expectExceptionMessage('Invalid Attribute Requested: ketchup. Valid keys are id, name, title.');

        $request = Request::create(
            'http://test.com/articles'
                . '?fields[tests]=name,ketchup'
        );

        $transformer = new TestTransformer($request);
        $testEntity = new TestEntity(1, 'name');

        $transformer->transform($testEntity);

    }
}
