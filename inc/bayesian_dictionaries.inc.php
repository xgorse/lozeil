<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Bayesian_Dictionaries extends Collector  {
	public $filters = null;
	public $count = array();
	public $categories = array();
	
	function __construct($class = null, $table = null, $db = null) {
		$class = "Bayesian_Dictionary";
		if ($table === null) {
			$table = $GLOBALS['dbconfig']['table_bayesiandictionaries'];
		}
		if ($db === null) {
			$db = new db();
		}
		parent::__construct($class, $table, $db);
	}
	
	function prepare() {
		$this->select();
		foreach ($this as $bayesiandictionnary) {
			if (!isset($this->count[$bayesiandictionnary->categories_id])) {
				$this->count[$bayesiandictionnary->categories_id] = array();
			}
			if (!isset($this->count[$bayesiandictionnary->categories_id][$bayesiandictionnary->word])) {
				$this->count[$bayesiandictionnary->categories_id][$bayesiandictionnary->word] = 0;
			}
			$this->count[$bayesiandictionnary->categories_id][$bayesiandictionnary->word] += $bayesiandictionnary->occurrences;
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
			$query_where[] = $this->db->config['table_bayesiandictionaries'].".id IN ".array_2_list($this->id);
		}
		if (isset($this->filters['word'])) {
			$query_where[] = $this->db->config['table_bayesiandictionaries'].".word = ".$this->db->quote($this->filters['word']);
		}
		if (isset($this->filters['field'])) {
			$query_where[] = $this->db->config['table_bayesiandictionaries'].".field = ".$this->db->quote($this->filters['field']);
		}
		if (isset($this->filters['categories_id'])) {
			$query_where[] = $this->db->config['table_bayesiandictionaries'].".categories_id = ".(int)$this->filters['categories_id'];
		}
		if (isset($this->filters['occurrences'])) {
			$query_where[] = $this->db->config['table_bayesiandictionaries'].".occurrences > ".(int)$this->filters['occurrences'];
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
	
	function getData(Writing $writing) {
		preg_match_all('(\w{3,})u', $writing->comment, $matches['comment']);
		$matches['comment'] = $matches['comment'][0];
		
		$matches['amount_inc_vat'] = $writing->amount_inc_vat;
		$matches['categories_id'] = $writing->categories_id;
		
		return $matches;
	}
	
	function train() {
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->truncateTable();
		$writings = new Writings();
		$writings->filter_with(array('positive_categories_id' => true));
		$writings->select();
		foreach($writings as $writing) {
			$data = $this->getData($writing);
			foreach ($data['comment'] as $word) {
				$bayesiandictionary = new Bayesian_Dictionary();
				$bayesiandictionary->word = $word;
				$bayesiandictionary->categories_id = $writing->categories_id;
				$bayesiandictionary->field = "comment";
				if (!$bayesiandictionary->exists()) {
					$bayesiandictionary->occurrences = 1;
				} else {
					$id = $bayesiandictionary->getId();
					$bayesiandictionary->load((int)$id['id']);
					$bayesiandictionary->occurrences += 1;
				}
				$bayesiandictionary->save();
			}
			$bayesiandictionary = new Bayesian_Dictionary();
			$bayesiandictionary->word = $writing->amount_inc_vat;
			$bayesiandictionary->categories_id = $writing->categories_id;
			$bayesiandictionary->field = "amount_inc_vat";
			if (!$bayesiandictionary->exists()) {
				$bayesiandictionary->occurrences = 1;
			} else {
				$id = $bayesiandictionary->getId();
				$bayesiandictionary->load((int)$id['id']);
				$bayesiandictionary->occurrences += 1;
			}
			$bayesiandictionary->save();
		}
		
	return true;
	}
	
	function word_probabilities($word, $categories_id) {
		$occurrences = isset($this->count[$categories_id]) ? array_sum($this->count[$categories_id]) : 0;
		$word_occurrence = isset($this->count[$categories_id][$word]) ? $this->count[$categories_id][$word] : 0;
		if ($occurrences == 0) return 0;
		return $word_occurrence/$occurrences;
	}
	
	function word_weighted_probabilities($word, $category, $weight = 1.0, $assumed_probability = 0.5) {
		$basic_probability = $this->word_probabilities($word, $category);

		$total = 0;
		foreach ($this->count as $category) {
			if (isset($category[$word])) {
				$total += $category[$word];
			}
		}
		
		return (($weight * $assumed_probability) + ($total * $basic_probability)) / ($weight + $total);
	}
	
	function data_probability(Writing $writing, $category) {
		$probabilities = 1;
		$data = $this->getData($writing);
		
		foreach($data['comment'] as $word) {
			$probabilities *= $this->word_weighted_probabilities($word, $category, $GLOBALS['param']['comment_weight']);
		}
		
		$probabilities *= $this->word_weighted_probabilities($data['amount_inc_vat'], $category, $GLOBALS['param']['amount_inc_vat_weight']);
		
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
	
	function classify(Writing $writing, $default_category = 0, $threshold = 3) {
		$probabilities = array();
		$max = 0;
		$best = $default_category;
		foreach ($this->categories as $category) {
			$probabilities[$category->id] = $this->probability($writing, $category->id);
			if ($probabilities[$category->id] > $max) {
				$max = $probabilities[$category->id];
				$best = $category->id;
			}
		}
		foreach ($probabilities as $category => $probability) {

			if ($category != $best) {
				if (isset($probabilities[$best]) and $probability * $threshold > $probabilities[$best]) {
					return $default_category;
				}
			}
		}
		
		return $best;
	}
}
