<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Dido\Service;

use OCA\Dido\Utile\Evaluator;

class TemplateService
{
    
    // Define repeat tags
    private $_reiterate_start_tag = '{{ section reiterate start }}';
    private $_reiterate_end_tag = '{{ section reiterate end }}';

    private string $_template;
    private string $_template_start;
    private string $_template_end;
    private string $_template_reiterate;
    private array $_template_reiterate_properties;

    public function __construct() {}

    public function fromString($template) {

        // reset all palce holders
        $this->reset();
        // store template
        $this->_template = $template;
        // extract template sections
        $this->extractSections();
        // reset template place holder
        $this->_template = '';
        // compile iteration
        $this->compileIteration();

    }

    public function fromFile($path) {

        // reset all palce holders
        $this->reset();
        // open file
        $file = fopen($path, "r") or die("Unable to open file!");
        // store template
        $this->_template = fread($file,filesize($path));
        // close file
        fclose($file);
        // extract template sections
        $this->extractSections();
        // reset template place holder
        $this->_template = '';
        // compile iteration
        $this->compileIteration();

    }

    public function reset(): void {

        $this->_template = '';
        $this->_template_start = '';
        $this->_template_end = '';
        $this->_template_reiterate = '';
        $this->_template_reiterate_properties = [];

    }

    private function extractSections() {

        // Find the start and end positions of the reiterate section
        $reiterate_start = strpos($this->_template, $this->_reiterate_start_tag);
        $reiterate_end = strpos($this->_template, $this->_reiterate_end_tag);
        // evaluate, if reiterate section exists
        if ($reiterate_start === false || $reiterate_end === false) {
            // reset template placeholder
            $this->_template = $template;
            // throw exception
            throw new Exception("Error Processing Template. Could not determain reiterate start or end.");
        }
        // Extract the start section
        $this->_template_start = substr(
            $this->_template,
            0,
            $reiterate_start
        );
        // Extract the reiterate section
        $this->_template_reiterate = substr(
            $this->_template,
            ($reiterate_start + strlen($this->_reiterate_start_tag)),
            ($reiterate_end - ($reiterate_start + strlen($this->_reiterate_start_tag)))
        );
        // Extract the end section
        $this->_template_end = substr(
            $this->_template,
            ($reiterate_end + strlen($this->_reiterate_end_tag))
        );
        
    }

    private function compileIteration() {

        // https://codeshack.io/lightweight-template-engine-php/

		$this->_template_reiterate = preg_replace('~\{%\s*(.+?)\s*\%}~is', '<?php $1 ?>', $this->_template_reiterate);

		$this->_template_reiterate = preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?php echo $1 ?>', $this->_template_reiterate);

		$this->_template_reiterate = preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\') ?>', $this->_template_reiterate);

	}

    public function generateStart(): string {

        // return template start
        return $this->_template_start;

    }

    public function generateEnd(): string {

        // return template end
        return $this->_template_end;

    }

    public function generateIteration(array|object $data): string {

        // evaluate if data was passed as an object
        if (is_object($data)) {
            $data = ['data' => $data];
        }
        // render iteration
        $iteration = Evaluator::evaluate($this->_template_reiterate, $data);
        // return iteration
        return $iteration;

    }

}
