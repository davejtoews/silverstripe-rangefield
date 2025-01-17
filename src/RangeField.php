<?php

namespace Firesphere\RangeField;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\FormField;
use SilverStripe\View\Requirements;

/**
 * Class RangeField
 *
 * A rangefield gives the user the option to select a value from a range, or set a range
 * @todo support for multiple handles, it seems not to work
 * @package Firesphere\Rangefield\Forms
 */
class RangeField extends FormField
{

    /**
     * @var array|int
     */
    protected $start = [0];

    /**
     * @var int
     */
    protected $min = 0;

    /**
     * @var int
     */
    protected $max = 100;

    /**
     * @var array
     */
    protected $range = [];

    /**
     * @var bool
     */
    protected $snap = false;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $density = 4;

    /**
     * @var bool
     */
    protected $showPips = true;

    /**
     * @var int|bool
     */
    protected $step;


    /**
     * @var string
     */
    protected $unit = '';


    /**
     * @var int
     */
    protected $decimalPlaces = 2;

    /**
     * RangeField constructor.
     * @param string      $name The internal field name, passed to forms.
     * @param null|string $title The human-readable field label.
     * @param int|array   $start Starting point(s) on the line
     * @param mixed       $value The value of the field.
     * @param int|array   $min Lowest value of the range
     * @param int         $max Highest value of the range
     * @param array       $range Associative array with keys which determine the percentage point on the range
     *                     And values being the labels on the field
     */
    public function __construct($name, $title = null, $start = 0, $min = 0, $max = 100, $range = [], $value = null)
    {
        if (!is_array($start)) {
            $start = [$start];
        }

        $this->start = $start;
        $this->min = $min;
        $this->max = $max;
        $this->range = $range;

        $this->setInputType('hidden');

        parent::__construct($name, $title, $value);
    }

    /**
     * @param array $properties
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function field($properties = array())
    {
        Requirements::set_force_js_to_bottom(true);

        $this->setupData();

        /** @todo find a way to get this a bit nicer. It's the only way to get it in without breaking on submit */
        $properties['JSConfig'] = "var $this->name = " . Convert::array2json($this->getData());

        $field = parent::Field($properties);

        return $field;
    }

    /**
     * @param string unit
     * @param int $decimalPlaces
     */
    public function setFormat($unit, $decimalPlaces)
    {
        $this->setUnit($unit);
        $this->setdecimalPlaces($decimalPlaces);

        return $this;
    }


    protected function setupData()
    {
        $data = [
            'start'             => $this->getStart(),
            'snap'              => $this->isSnap(),
            'animate'           => true,
            'animationDuration' => 300,
            'range'             => [
                'min' => $this->getMin(),
                'max' => $this->getMax()
            ],
            'unit'              => $this->getUnit(),
            'decimalPlaces'     => $this->getdecimalPlaces()
        ];

        if ($this->showPips) {
            $data['pips'] = [  // Show a scale with the slider
                'mode'    => 'steps',
                'stepped' => true,
                'density' => $this->getDensity()
            ];
        }

        if ($this->getStep()) {
            $data['step'] = $this->getStep();
        }

        if (count($this->getRange())) { // Update the range if we've gotten a forced range
            $data['range'] = array_merge($data['range'], $this->getRange());
        }

        $this->setData($data);
    }

    /**
     * @return array|int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param array|int $start
     */
    public function setStart($start)
    {
        $this->start = (array)$start;

        return $this;
    }

    /**
     * @return array
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return array
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @param array $range
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSnap()
    {
        return $this->snap;
    }

    /**
     * @param bool $snap
     */
    public function setSnap($snap)
    {
        $this->snap = $snap;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return int
     */
    public function getDensity()
    {
        return $this->density;
    }

    /**
     * @param int $density
     */
    public function setDensity($density)
    {
        $this->density = $density;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowPips()
    {
        return $this->showPips;
    }

    /**
     * @param bool $showPips
     */
    public function setShowPips($showPips)
    {
        $this->showPips = $showPips;

        return $this;
    }

    /**
     * @return bool|int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param bool|int $step
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return int
     */
    public function getDecimalPlaces()
    {
        return $this->decimalPlaces;
    }

    /**
     * @param int $decimalPlaces
     */
    public function setDecimalPlaces($decimalPlaces)
    {
        $this->decimalPlaces = $decimalPlaces;

        return $this;
    }
}
