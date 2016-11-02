<?php

/* E:\www\bg360/themes/rainlab-vanilla/pages/account.htm */
class __TwigTemplate_32993672a14cdc47e30d42897edcee1596165a02d35ce9882b8f4f532ae2af15 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context['__cms_component_params'] = [];
        echo $this->env->getExtension('CMS')->componentFunction("account"        , $context['__cms_component_params']        );
        unset($context['__cms_component_params']);
        // line 2
        echo "
<hr />

<p><a href=\"";
        // line 5
        echo $this->env->getExtension('Cms\Twig\Extension')->pageFilter("forgot-password");
        echo "\">Forgotten your password?</a></p>";
    }

    public function getTemplateName()
    {
        return "E:\\www\\bg360/themes/rainlab-vanilla/pages/account.htm";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  28 => 5,  23 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "{% component 'account' %}

<hr />

<p><a href=\"{{ 'forgot-password'|page }}\">Forgotten your password?</a></p>";
    }
}
