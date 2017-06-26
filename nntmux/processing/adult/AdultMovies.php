<?php

namespace nntmux\processing\adult;


abstract class AdultMovies
{
	/**
	 * @var \simple_html_dom
	 */
	protected $_html;

	/**
	 * AdultMovies constructor.
	 *
	 * @param array $options
	 *
	 * @throws \Exception
	 */
	public function __construct(array $options = [])
	{
		$this->_html = new \simple_html_dom();
	}

	/**
	 * @return mixed
	 */
	abstract protected function productInfo();

	/**
	 * @return mixed
	 */
	abstract protected function covers();

	/**
	 * @return mixed
	 */
	abstract protected function synopsis();

	/**
	 * @return mixed
	 */
	abstract protected function cast();

	/**
	 * @return mixed
	 */
	abstract protected function genres();

	/**
	 * @param string $movie
	 *
	 * @return mixed
	 */
	abstract public function processSite($movie);

	/**
	 * @return mixed
	 */
	abstract public function getAll();

	/**
	 * @return mixed
	 */
	abstract protected function trailers();
}