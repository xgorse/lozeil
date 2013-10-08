<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Bayesian_Elements extends Collector  {
	public $filters = null;
	public $count = array();
	public $categories = array();
	
	function __construct($class = null, $table = null, $db = null) {
		if ($class === null) {
			$class = substr(__CLASS__, 0, -1);
		}
		if ($table === null) {
			$table = $GLOBALS['dbconfig']['table_bayesianelements'];
		}
		if ($db === null) {
			$db = new db();
		}
		parent::__construct($class, $table, $db);
	}
	
	function increment_decrement(Writing $writing_before, Writing $writing) {
		if ($writing_before->different_from($writing)) {
			$bayesian_elements = new Bayesian_Elements();
			$bayesian_elements->stuff_with($writing_before);
			$bayesian_elements->decrement();
			
			$bayesian_elements = new Bayesian_Elements();
			$bayesian_elements->stuff_with($writing);
			$bayesian_elements->increment();
		}
	}
	
	function increment() {
		foreach ($this as $bayesian_element) {
			$bayesian_element->increment();
		}
		return true;
	}
	
	function decrement() {
		foreach ($this as $bayesian_element) {
			$bayesian_element->decrement();
		}
		return true;
	}
	
	function stuff_with(Writing $writing) {
		$this->reset();
		$datas = $writing->get_data();
		foreach ($datas['classification_target'] as $table_name => $table_id) {
			if ($table_id > 0) {
				foreach ($datas['classification_data'] as $field => $data) {
					foreach ($data as $value) {
						$bayesian_element = new Bayesian_Element();
						$bayesian_element->table_name = $table_name;
						$bayesian_element->table_id = $table_id;
						$bayesian_element->field = $field;
						$bayesian_element->element = $value;
						$this[] = $bayesian_element;
					}
				}
			}
		}
		return true;
	}
	
	function prepare() {
		$this->filter_with(array('table_name' => $GLOBALS['dbconfig']['table_categories']));
		$this->select();
		foreach ($this as $bayesiandictionnary) {
			if (!isset($this->count[$bayesiandictionnary->table_id])) {
				$this->count[$bayesiandictionnary->table_id] = array();
			}
			if (!isset($this->count[$bayesiandictionnary->table_id][$bayesiandictionnary->element])) {
				$this->count[$bayesiandictionnary->table_id][$bayesiandictionnary->element] = 0;
			}
			$this->count[$bayesiandictionnary->table_id][$bayesiandictionnary->element] += $bayesiandictionnary->occurrences;
		}
		
		$categories = new Categories();
		$categories->select();
		$this->categories = $categories;
	}
		
	function get_where() {
		$query_where = parent::get_where();
		
		if (isset($this->id) and !empty($this->id)) {
			if (!is_array($this->id)) {
				$this->id = array((int)$this->id);
			}
			$query_where[] = $this->db->config['table_bayesianelements'].".id IN ".array_2_list($this->id);
		}
		if (isset($this->filters['element'])) {
			$query_where[] = $this->db->config['table_bayesianelements'].".element = ".$this->db->quote($this->filters['element']);
		}
		if (isset($this->filters['table_name'])) {
			$query_where[] = $this->db->config['table_bayesianelements'].".table_name = ".$this->db->quote($this->filters['table_name']);
		}
		if (isset($this->filters['field'])) {
			$query_where[] = $this->db->config['table_bayesianelements'].".field = ".$this->db->quote($this->filters['field']);
		}
		if (isset($this->filters['table_id'])) {
			$query_where[] = $this->db->config['table_bayesianelements'].".table_id = ".(int)$this->filters['table_id'];
		}
		if (isset($this->filters['occurrences'])) {
			$query_where[] = $this->db->config['table_bayesianelements'].".occurrences > ".(int)$this->filters['occurrences'];
		}
		
		return $query_where;
	}
	
	function filter_with() {
		$elements = func_get_args();
		foreach ($elements as  $element) {
			foreach ($element as $key => $value) {
				$this->filters[$key] = $value;
			}
		}
	}
		
	function train() {
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->truncateTable();
		$writings = new Writings();
		$writings->filter_with(array('categories_min' => 1));
		$writings->select();
		foreach ($writings as $writing) {
			$bayesianelements = new Bayesian_Elements();
			$bayesianelements->stuff_with($writing);
			$bayesianelements->increment();
		}
		return true;
	}
	
	function element_probabilities($element, $categories_id) {
		$occurrences = isset($this->count[$categories_id]) ? array_sum($this->count[$categories_id]) : 0;
		$element_occurrence = isset($this->count[$categories_id][$element]) ? $this->count[$categories_id][$element] : 0;
		if ($occurrences == 0) return 0;
		return $element_occurrence/$occurrences;
	}
	
	function element_weighted_probabilities($element, $category, $weight = 1.0, $assumed_probability = 0.5) {
		$basic_probability = $this->element_probabilities($element, $category);

		$total = 0;
		foreach ($this->count as $category) {
			if (isset($category[$element])) {
				$total += $category[$element];
			}
		}
		
		return (($weight * $assumed_probability) + ($total * $basic_probability)) / ($weight + $total);
	}
	
	function data_probability(Writing $writing, $category) {
		$probabilities = 1;
		$data = $writing->get_data();
		
		foreach($data['classification_data']['comment'] as $element) {
			$probabilities *= $this->element_weighted_probabilities($element, $category, $GLOBALS['param']['comment_weight']);
		}
		
		$probabilities *= $this->element_weighted_probabilities($data['classification_data']['amount_inc_vat'][0], $category, $GLOBALS['param']['amount_inc_vat_weight']);
		
		return $probabilities;
	}
	
	function probability(Writing $writing, $category) {
		$proba = $this->data_probability($writing, $category);
		if (!isset($this->count[$category])) {
			return 0;
		}
		$sum = 0;
		foreach($this->count as $cat) {
			$sum += array_sum($cat);
		}
		return (count($this->count[$category])/$sum) * $proba;
	}
	
	function categories_id_estimated(Writing $writing, $category_id_default = 0, $threshold = 2) {
		$probabilities = array();
		$probability_max = 0;
		$category_id_best = $category_id_default;
		foreach ($this->categories as $category) {
			$probabilities[$category->id] = $this->probability($writing, $category->id);
			if ($probabilities[$category->id] > $probability_max) {
				$probability_max = $probabilities[$category->id];
				$category_id_best = $category->id;
			}
		}
		foreach ($probabilities as $category_id => $probability) {
			if ($category_id != $category_id_best) {
				if (isset($probabilities[$category_id_best]) and $probability * $threshold > $probabilities[$category_id_best]) {
					return $category_id_default;
				}
			}
		}
		
		return $category_id_best;
	}
}
