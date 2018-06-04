<?php

require_once __DIR__ .'/Twig.php';

class Pagination
{
    var $total = 0;
    var $current = 1;
    var $start = 0;
    var $limit = 0;
    var $maxPage = 0;
    var $url = null;

    function __construct()
    {
        $this->twig = new Twig(dirname(__DIR__) .'/public/template');

        $this->id = "page_selector";

        $this->start = 0;
        $this->limit = 15;
        $this->total = 0;
        $this->current = 1;

        $this->maxPage = 0;

        $this->params = [];
    }

    function setConfig($config=[])
    {
        if (isset($config['limit']) && is_numeric($config['limit']) && $config['limit'] > 0 && $config['limit'] <= 100) {
            $this->limit = $config['limit'];
        }

        if (isset($config['start']) && is_numeric($config['start'])) {
            $this->start = $config['start'];
        }

        if (isset($config['total']) && !empty($config['total']) && is_numeric($config['total']) && $config['total'] > 0) {
            $this->total = $config['total'];
        }

        if (isset($config['url']) && !empty($config['url'])) {
            $this->url = $config['url'];
        }

        if ($this->total >= 1) {
            $this->maxPage = ($this->total % $this->limit) ? ((int)($this->total / $this->limit) + 1) : (int)($this->total / $this->limit);
        } else {
            $this->maxPage = 0;
        }

        if ($this->start > 0) {
            $this->current = (int)(($this->start + $this->limit) / $this->limit);
        } else {
            $this->current = 1;
        }

        if (isset($config['params'])) {
            $this->params = $config['params'];
        }

        return $this;
    }

    function getTopContent()
    {
        $this->twig->setFile('pagination_top.html', [
            'startRowNum' => $this->current > 0 ? ((($this->current - 1 )* ($this->limit)) + 1) : 0,
            'endRowNum' => $this->current > 0 ? ($this->current * $this->limit) : 0,
            'totalNum' => $this->total,
            'maxPage' => $this->maxPage,
            'currentPage' => $this->current,
            'limit' => $this->limit,
            'url' => $this->url,
        ]);

        return $this->twig->getContent();
    }

    function getBottomContent()
    {
        if ($this->current > $this->maxPage) {
            $this->current = $this->maxPage;
        }

        $paginationColumns = [];

        if ($this->current > 0) {
            $paginationColumns[] = $this->current;

            $prev = $this->current;
            $next = $this->current;

            while (count($paginationColumns) < 5) {
                $prev--;
                $next++;

                if ($prev > 0 && $prev <= $this->maxPage) {
                    array_unshift($paginationColumns, $prev);
                }

                if ($next > 0 && $next <= $this->maxPage) {
                    array_push($paginationColumns, $next);
                }

                if ($prev <= 0 && $next > $this->maxPage) {
                    break;
                }
            }
        }

        $endRowNum = $this->current > 0 ? ($this->current * $this->limit) : 0;
        if ($endRowNum > $this->total) {
            $endRowNum = $this->total;
        }

        $this->twig->setFile('pagination_bottom.html', [
            'startRowNum' => $this->current > 0 ? ((($this->current - 1 )* ($this->limit)) + 1) : 0,
            'endRowNum' => $endRowNum,
            'totalNum' => $this->total,
            'maxPage' => $this->maxPage,
            'currentPage' => $this->current,
            'paginationColumns' => $paginationColumns,
            'prevPage' => $this->current - 1 >= 1 ? ($this->current - 1) : 1,
            'nextPage' => $this->current + 1 <= $this->maxPage ? ($this->current + 1) : $this->maxPage,
        ]);

        return $this->twig->getContent();
    }
}
