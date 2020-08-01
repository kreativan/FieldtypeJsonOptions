<?php
/**
 *  FieldtypeJsonOptions
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2020 kraetivan.net
 *  @link http://www.kraetivan.net
 *
*/
class FieldtypeJsonOptions extends Fieldtype {

	public static $defaultOptionValues = array();

	public static function getModuleInfo() {
		return array(
		'title' => 'JSON Options',
		'version' => 100,
		'summary' => 'Select and radio options from a json file'
		);
	}

	public function ___getConfigInputfields(Field $field) {

		$inputfields = $this->wire(new InputfieldWrapper());

		$f = $this->wire('modules')->get("InputfieldText");
		$f->attr('name', 'json_file');
		$f->label = 'Json File Path';
		$f->value = $field->json_file;
		$f->placeholder = "Folder path...";
		$f->required = true;
		$f->columnWidth = "100%";
		$f->description = "eg: `site/templates/lib/options/my-file.json`";
		$inputfields->add($f);

		$f = $this->wire('modules')->get("InputfieldRadios");
		$f->attr('name', 'input_type');
		$f->label = 'Inputfield';
		$f->options = [
			'InputfieldSelect' => "Select",
			'InputfieldRadios' => "Radios",
		];
		$f->value = $field->input_type;
		$f->optionColumns = "1";
		$f->required = true;
		$f->defaultValue = "InputfieldSelect";
		$f->columnWidth = "100%";
		$inputfields->add($f);

		return $inputfields;

	}

	public function getInputfield(Page $page, Field $fields) {

		$json_file = $this->config->paths->root . $fields->json_file;
		if(empty($fields->json_file) || !file_exists($json_file)) return;

		$json_data = file_get_contents($json_file);
		$array = json_decode($json_data, true);


		$inputfield = $this->modules->get("{$fields->input_type}");
		if($fields->input_type == "InputfieldRadios") $inputfield->optionColumns = "1";

		foreach($array as $value => $label) {
			$inputfield->addOption($value, $label);
		}

		return $inputfield;

	}

	public function getDatabaseSchema(Field $field) {
		$schema = parent::getDatabaseSchema($field);
		$schema['data'] = 'text NOT NULL';
		$schema['keys']['data_exact'] = 'KEY `data_exact` (`data`(255))';
		$schema['keys']['data'] = 'FULLTEXT KEY `data` (`data`)';
		return $schema;
	}

	public function sanitizeValue(Page $page, Field $field, $value) {
		return $value;
	}

}
