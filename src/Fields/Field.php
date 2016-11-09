<?php

namespace Akibatech\Crud\Fields;

use Akibatech\Crud\Services\CrudFields;
use Illuminate\View\View;

/**
 * Class Field
 *
 * @package Akibatech\Crud\Fields
 */
abstract class Field
{
    /**
     * @var string
     */
    const TYPE = 'type';

    /**
     * @var CrudFields
     */
    protected $fields;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @var array
     */
    protected $rules;

    /**
     * Field constructor.
     *
     * @param   string       $identifier
     * @param   array|string $rules
     */
    public function __construct($identifier, $rules = null)
    {
        $this->identifier = $identifier;

        if (!is_null($rules))
        {
            $this->withRules($rules);
        }
    }

    /**
     * Constructs staticly.
     *
     * @param   string $idenfitier
     * @param   null|string|array $rules
     * @return  static
     */
    public static function handle($idenfitier, $rules = null)
    {
        return (new static($idenfitier, $rules));
    }


    /**
     * Add validation rules to the field.
     *
     * @param   string|array $rules
     * @return  mixed
     */
    public function withRules($rules)
    {
        if (is_array($rules))
        {
            foreach ($rules as $rule)
            {
                $this->addRule($rule);
            }
        }
        else
        {
            if (is_string($rules))
            {
                if (stripos($rules, '|') !== false)
                {
                    $rules = explode('|', $rules);

                    return $this->withRules($rules);
                }

                return $this->withRules([$rules]);
            }
        }

        return $this;
    }

    /**
     * Add a validation rule.
     *
     * @param   string $rule
     * @return  self
     */
    public function addRule($rule)
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @param   CrudFields $fields
     * @return  self
     */
    public function setFields(CrudFields $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the field identifier.
     *
     * @param   void
     * @return  string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set a custom label for the field.
     *
     * @param   string $name
     * @return  self
     */
    public function withLabel($name)
    {
        $this->label = $name;

        return $this;
    }

    /**
     * Defines a placeholder for the field.
     *
     * @param   string $placeholder
     * @return  self
     */
    public function withPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Appends an help message to the input.
     *
     * @param   string $help
     * @return  self
     */
    public function withHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Render the field form.
     *
     * @param   void
     * @return  string
     */
    public function form()
    {
        return $this->getForm()->render();
    }

    /**
     * Get the form view.
     *
     * @param   void
     * @return  View
     */
    protected function getForm()
    {
        return view()->make($this->getViewName())->with($this->getViewBaseVariables());
    }

    /**
     * Get the field view name.
     *
     * @param   void
     * @return  string
     */
    abstract public function getViewName();

    /**
     * Returns additionnal variables to the views.
     *
     * @param   void
     * @return  array
     */
    protected function getViewVariables()
    {
        return [];
    }

    /**
     * Returns all base variables for the view.
     *
     * @param   void
     * @return  array
     */
    protected function getViewBaseVariables()
    {
        $base_variables = [
            'field'       => $this,
            'has_error'   => $this->hasError(),
            'error'       => $this->getError(),
            'placeholder' => $this->getPlaceholder(),
            'help'        => $this->getHelp(),
            'has_old'     => $this->hasOld(),
            'old'         => $this->getOld(),
            'label'       => $this->getLabel(),
            'name'        => $this->identifier,
            'id'          => 'field-' . $this->identifier,
            'value'       => $this->getValue()
        ];

        return array_merge($base_variables, $this->getViewVariables());
    }

    /**
     * Checks if the field has an error.
     *
     * @param   void
     * @return  bool
     */
    public function hasError()
    {
        return $this->fields->getErrors()->has($this->identifier);
    }

    /**
     * Returns the error.
     *
     * @param   void
     * @return  null|string
     */
    public function getError()
    {
        if ($this->hasError())
        {
            return $this->fields->getErrors()->first($this->identifier);
        }

        return null;
    }

    /**
     * Returns the field's placeholder.
     *
     * @param   void
     * @return  string
     */
    public function getPlaceholder()
    {
        if (empty($this->placeholder))
        {
            return null;
        }

        return $this->placeholder;
    }

    /**
     * Returns the field's help.
     *
     * @param   void
     * @return  string
     */
    public function getHelp()
    {
        if (empty($this->help))
        {
            return null;
        }

        return $this->help;
    }

    /**
     * Checks if the field has a previous value.
     *
     * @param   void
     * @return  bool
     */
    public function hasOld()
    {
        return $this->fields->getOldInput()->has($this->identifier);
    }

    /**
     * Returns the old value.
     *
     * @param   void
     * @return  string|null
     */
    public function getOld()
    {
        if ($this->hasOld())
        {
            return $this->fields->getOldInput()->first($this->identifier);
        }

        return null;
    }

    /**
     * Returns the field's label.
     *
     * @param   void
     * @return  string
     */
    public function getLabel()
    {
        if (empty($this->label))
        {
            return title_case($this->identifier);
        }

        return $this->label;
    }

    /**
     * Get the field value.
     *
     * @param   void
     * @return  mixed
     */
    public function getValue()
    {
        if ($this->fields->getEntry())
        {
            return $this->fields->getEntry()->getModel()->getAttributeValue($this->identifier);
        }

        return null;
    }

    /**
     * Get the value to be displayed on a table.
     *
     * @param   void
     * @return  mixed
     */
    public function getTableValue()
    {
        return $this->getValue();
    }

    /**
     * Set a new value to the model.
     *
     * @param   mixed $value
     * @return  self
     */
    public function newValue($value)
    {
        $this->fields->getEntry()->getModel()->setAttribute($this->identifier, $value);

        return $this;
    }

    /**
     * @param   void
     * @return  string
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Return fields specific scripts files from public folder.
     * Example: ['js/field.js']
     *
     * @param   void
     * @return  array
     */
    public function getScripts()
    {
        return [];
    }

    /**
     * Return fields specific stylesheets files from public folder.
     * Example: ['css/field.css']
     *
     * @param   void
     * @return  array
     */
    public function getCss()
    {
        return [];
    }
}
