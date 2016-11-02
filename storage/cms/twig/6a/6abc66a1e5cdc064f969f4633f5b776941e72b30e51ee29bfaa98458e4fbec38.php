<?php

/* E:\www\bg360/plugins/rainlab/user/components/account/signin.htm */
class __TwigTemplate_c86cb50ea37686e3a170b4428ef4ab4f05f51b9b6b1995ac72f093c1c8182eb4 extends Twig_Template
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
        echo "<form
    data-request=\"onSignin\">
    <div class=\"form-group\">
        <label for=\"userSigninLogin\">";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["loginAttributeLabel"]) ? $context["loginAttributeLabel"] : null), "html", null, true);
        echo "</label>
        <input
            name=\"login\"
            type=\"text\"
            class=\"form-control\"
            id=\"userSigninLogin\"
            placeholder=\"Enter your ";
        // line 10
        echo twig_escape_filter($this->env, twig_lower_filter($this->env, (isset($context["loginAttributeLabel"]) ? $context["loginAttributeLabel"] : null)), "html", null, true);
        echo "\" />
    </div>

    <div class=\"form-group\">
        <label for=\"userSigninPassword\">Password</label>
        <input
            name=\"password\"
            type=\"password\"
            class=\"form-control\"
            id=\"userSigninPassword\"
            placeholder=\"Enter your password\" />
    </div>

    <button type=\"submit\" class=\"btn btn-default\">Sign in</button>
</form>";
    }

    public function getTemplateName()
    {
        return "E:\\www\\bg360/plugins/rainlab/user/components/account/signin.htm";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 10,  24 => 4,  19 => 1,);
    }

    public function getSource()
    {
        return "<form
    data-request=\"onSignin\">
    <div class=\"form-group\">
        <label for=\"userSigninLogin\">{{ loginAttributeLabel }}</label>
        <input
            name=\"login\"
            type=\"text\"
            class=\"form-control\"
            id=\"userSigninLogin\"
            placeholder=\"Enter your {{ loginAttributeLabel|lower }}\" />
    </div>

    <div class=\"form-group\">
        <label for=\"userSigninPassword\">Password</label>
        <input
            name=\"password\"
            type=\"password\"
            class=\"form-control\"
            id=\"userSigninPassword\"
            placeholder=\"Enter your password\" />
    </div>

    <button type=\"submit\" class=\"btn btn-default\">Sign in</button>
</form>";
    }
}
