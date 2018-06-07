<?php

namespace Virtualorz\Pagination;

use Illuminate\Support\Arr;
use Route;
use Request;

/**
 * Pagination
 * 
 * @package App\Classes\Pagination
 */
class Pagination {

    protected $paginationParam;
    protected $pagination = null;

    public function __construct() {
        $default_curr_page = intval(Route::input('optional.page', 1));
        $default_items_per_page = intval(Request::input('page_display', 1));
        $default_pages_of_pagination = intval(config('pagination.pages', 1));
        $default_route_name = Route::currentRouteName();
        if (!in_array($default_items_per_page, config('pagination.data_display', []))) {
            $default_items_per_page = intval(config('pagination.items', 10));
        }

        $this->paginationParam = [
            'curr' => max(1, $default_curr_page),
            'total' => 0,
            'items' => max(1, $default_items_per_page),
            'pages' => max(1, $default_pages_of_pagination),
            'route_name' => $default_route_name,
            'route_param' => [],
            'route_option_param' => [],
        ];
    }

    /**
     * 設定分頁資料
     * 
     * @param array $data
     */
    public function setPagination($data) {
        foreach ($data as $k => $v) {
            if (isset($this->paginationParam[$k])) {
                $this->paginationParam[$k] = $v;
            }
        }
        $this->pagination = null;
    }

    /**
     * 取得資料庫搜尋分頁 第幾筆開始取
     * 
     * @return integer
     */
    public function getItemSkip() {
        return ($this->paginationParam['curr'] - 1) * $this->paginationParam['items'];
    }

    /**
     * 取得資料庫搜尋分頁 取幾筆
     * 
     * @return integer
     */
    public function getItemTake() {
        return $this->paginationParam['items'];
    }

    /**
     * 取得分頁資料
     * 
     * @return array
     */
    public function getPagination($prop = null, $default = null) {
        if (!$this->pagination) {
            $this->pagination = $this->buildPagination();
        }

        return array_get($this->pagination, $prop, $default);
    }

    /**
     * 取得分頁html
     * 
     * @return string
     */
    public function getPaginationHtml() {
        $pagination = $this->getPagination();
        $tmp_view = view('Backend.elements.pagination');
        $tmp_view->with('pagination', $pagination);

        return $tmp_view->render();
    }

    /**
     * 建立 pagination 資料
     */
    private function buildPagination() {
        $pagination = array_merge([], $this->paginationParam);
        $pagination['first'] = 1;
        $pagination['last'] = $pagination['total'] <= 0 ? 1 : ceil($pagination['total'] / $pagination['items']);
        $pagination['prev'] = ($pagination['curr'] - 1) <= 0 ? 1 : ($pagination['curr'] - 1);
        $pagination['next'] = ($pagination['curr'] + 1) > $pagination['last'] ? $pagination['last'] : ($pagination['curr'] + 1);
        $pagination['start'] = ($pagination['curr'] - floor($pagination['pages'] / 2)) < 1 ? 1 : ($pagination['curr'] - floor($pagination['pages'] / 2));
        $pagination['end'] = $pagination['start'] + $pagination['pages'] - 1;
        if ($pagination['end'] > $pagination['last']) {
            $diff = $pagination['end'] - $pagination['last'];
            $pagination['end'] = $pagination['last'];
            $pagination['start'] = ($pagination['start'] - $diff) < 1 ? 1 : $pagination['start'] - $diff;
        }
        $pagination['take_start'] = ($pagination['curr'] - 1) * $pagination['items'] + 1;
        $pagination['take_end'] = $pagination['take_start'] + $pagination['items'] - 1;
        if ($pagination['total'] <= 0) {
            $pagination['take_start'] = $pagination['take_end'] = 0;
        } else if ($pagination['total'] < $pagination['take_end']) {
            $pagination['take_end'] = $pagination['total'];
        }
        $pagination['page_name'] = 'page';

        return $pagination;
    }

}
