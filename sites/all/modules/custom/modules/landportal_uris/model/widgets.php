<?php
require_once(dirname(__FILE__) .'/../database/database_helper.php');
require_once(dirname(__FILE__) .'/../cache/cache_helper.php');



class Widgets {

	public function get($options) {
		$lang = $options->language;
		$api = $options->host;

		$cache = new CacheHelper('widgets', array(
			$lang,
		));
		$cached = $cache->get();
		if ($cached !== null) {
			return $cached;
		} else {
			$database = new DataBaseHelper();
			$database->open();
			$datasources = $database->query("datasources", array($lang));
			$countries = $database->query("countries_without_region", array($lang));
			$database->close();
			$result = $this->compose_data($datasources, $countries);
			$cache->store($result);
			return $result;
		}
	}

	private function compose_data($datasources, $countries) {
		return array(
			"selectors" => array(
				"data-sources" => $this->compose_datasources($datasources),
				"countries" => $this->compose_countries($countries)
			)
		);
	}

	private function compose_datasources($data) {
		$result = array();
		for ($i = 0; $i < count($data); $i++) {
			$datasource_id = $data[$i]["dat_id"];
			if (!array_key_exists($datasource_id, $result)) {
				$result[$datasource_id] = array(
					"id" => $datasource_id,
					"name" => $data[$i]["dat_name"],
					"organization_id" => $data[$i]["organization_id"],
					"indicators" => array(),
					"with_data" => false
				);
			}
			$indicator = array(
				"id" => $data[$i]["ind_id"],
				"preferable_tendency" => $data[$i]["preferable_tendency"],
				"last_update" => $data[$i]["last_update"],
				"starred" => $data[$i]["starred"],
				"name" => utf8_encode($data[$i]["ind_name"]),
				"description" => utf8_encode($data[$i]["ind_description"]),
				"with_data" => $data[$i]["data"] > 0
			);
			if ($data[$i]["data"] > 0)
				$result[$datasource_id]["with_data"] = true;
			array_push($result[$datasource_id]["indicators"], $indicator);
		}
		return array_values($result);
	}

	private function compose_countries($data) {
		$result = array();
		for ($i = 0; $i < count($data); $i++) {
			$country = array(
				"id" => $data[$i]["id"],
				"name" => utf8_encode($data[$i]["country_name"]),
				"faoURI" => utf8_encode($data[$i]["faoURI"]),
				"iso2" => $data[$i]["iso2"],
				"iso3" => $data[$i]["iso3"],
			);
			array_push($result, $country);
		}
		return $result;
	}
}
