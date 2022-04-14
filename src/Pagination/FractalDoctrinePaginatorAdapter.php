<?php

namespace Giadc\JsonApiResponse\Pagination;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiResponse\Interfaces\PaginatorContract;
use League\Fractal\Pagination\PaginatorInterface;

class FractalDoctrinePaginatorAdapter implements PaginatorInterface, PaginatorContract
{
    /**
     * The paginator instance.
     */
    protected Paginator $paginator;

    /**
     * The route generator.
     *
     * @var callable
     */
    protected $routeGenerator;

    /**
     * The RequestParams instance.
     */
    protected RequestParams $requestParams;

    /**
     * Create a new doctrine pagination adapter.
     */
    public function __construct(Paginator $paginator, RequestParams $requestParams)
    {
        $this->paginator = $paginator;
        $this->request = $requestParams;
    }

    /**
     * Get the current page.
     */
    public function getCurrentPage(): int
    {
        $paginator = $this->request->getPageDetails();
        return $paginator->getPageNumber();
    }

    /**
     * Get the last page.
     */
    public function getLastPage(): int
    {
        $paginator = $this->request->getPageDetails();
        $resultsPerPage = $paginator->getPageSize();

        return ceil($this->getTotal() / $resultsPerPage);
    }

    /**
     * Get the total.
     */
    public function getTotal(): int
    {
        return count($this->paginator);
    }

    /**
     * Get the count.
     */
    public function getCount(): int
    {
        if ($this->getPerPage() > $this->getTotal())
            return $this->getTotal();

        return $this->getPerPage();
    }

    /**
     * Get the number per page.
     */
    public function getPerPage(): int
    {
        $paginator = $this->request->getPageDetails();
        return $paginator->getPageSize();
    }

    /**
     * Get the url for the given page.
     */
    public function getUrl(int $page): string
    {
        $url = $this->request->getUri();
        $params = $this->request->getQueryString($page);

        return $url . '?' . $params;
    }

    /**
     * Get the paginator instance.
     */
    public function getPaginator(): Paginator
    {
        return $this->paginator;
    }

    /**
     * Get the the route generator.
     *
     * @return callable
     */
    public function getRouteGenerator()
    {
        return $this->routeGenerator;
    }
}
