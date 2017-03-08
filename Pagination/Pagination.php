<?php
namespace App\Helpers;
/**
 * class Pagination-create pages
 * return array pages
 */
class Pagination {
	/**
	 * total records - count from sql query for current user
	 * @var integer
	 */
	protected $total;
	/**
	 * total limit per page
	 * @var integer
	 */
	protected $count;
	/**
	 * Pages Grouping
	 * current page in the middle + 2 on left and right
	 * @var integer
	 */
	protected $grouping;
	/**
	 * Current page number
	 * @var integer
	 */
	protected $page;
	/**
	 * Set pageNumber from array
	 * @var integer
	 */
	protected $pageNumber;
	/**
	 * Set Total records from database for current user
	 * @return integer number of rows
	 */
	public function setTotal($total) {

		if (is_numeric($total) || $total > 0) {
			$this->total = $total;
		}
		return $this;
	}
	/**
	 * Set total records per page
	 * @param integer number of results per page
	 */
	public function setPerPage($count) {
		if (is_numeric($count) || $count > 0) {
			$this->count = $count;
		}
		return $this;
	}
	/**
	 * set how to group pages
	 * how many pages can see users at once
	 * @return integer grouping number
	 */
	public function setGrouping($grouping) {
		if (is_numeric($grouping) || $grouping > 0) {
			$this->grouping = $grouping;
		}
		return $this;
	}
	/**
	 * Set page number from array
	 * @param integer number of the page
	 */
	public function setPage($pageNumber) {
		if (is_numeric($pageNumber) || $pageNumber > 0) {
			$this->pageNumber = $pageNumber;
		}
		return $this;
	}
	/**
	 * Get current page number
	 * @return integer current page
	 */
	public function getCurrentPage() {
		$total_pages = (int) ceil($this->total / $this->count);

		$this->page = isset($this->pageNumber) ? abs($this->pageNumber) : 1;
		$this->page = $this->page <= 0 ? 1
		: ($this->page > $total_pages ? $total_pages
		: $this->page);

		return $this->page;
	}
	/**
	 * create pagination
	 * generate next and previous links and other pages
	 * @return array with pages
	 */
	public function getItems() {

		$pages = (int) ceil($this->total / $this->count);
		$pagination = [];
		$begin = 1;
		$end = $pages + 1;

		if ($this->grouping >= 2) {
			$diff = floor($this->grouping / 2);

			$begin = (int) ($this->page - $diff);
			$begin = $begin < 1 ? 1 : $begin;

			$end = $begin + $this->grouping;
			if ($end > $pages + 1) {
				$end = $pages + 1;
				$begin = (int) ($pages - $this->grouping + 1);
				$begin = $begin < 1 ? 1 : $begin;
			}
		}

		$previous = ($this->page - 1) > 0 ? ($this->page - 1) : $this->page;
		$pagination[] = [
			'may_remove' => $previous === $this->page,
			'page' => $previous,
			'name' => 'Previous',
			'active' => $previous === $this->page
		];

		for ($i = $begin; $i < $end; $i++) {
			$pagination[] = [
				'page' => $i,
				'name' => $i,
				'active' => $i == $this->page
			];
		}

		$next = ($this->page + 1) > $pages ? $pages : ($this->page + 1);
		$pagination[] = [
			'may_remove' => $next === $this->page,
			'page' => $next,
			'name' => 'Next',
			'active' => $next === $this->page
		];
		return $pagination;
	}

	/**
	 * from which result to start sql query
	 * @return integer
	 */
	public function getOffset() {
		$this->getCurrentPage();
		$offset = (($this->page - 1) * $this->count);
		return $offset;
	}

	/**
	 * show result depends on this limit- sql query
	 * @return integer number limit per page
	 */
	public function getLimit() {
		$limit = $this->count;
		return $limit;
	}
}
