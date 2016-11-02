<?php

/* E:\www\bg360/plugins/rainlab/user/components/account/register.htm */
class __TwigTemplate_772546cfde03fafec981fcc693f9b63546c2a1701c88577aa39e8e81bb682dbd extends Twig_Template
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
    data-request=\"onRegister\">
    <div class=\"form-group\">
        <label for=\"registerName\">Full Name</label>
        <input
            name=\"name\"
            type=\"text\"
            class=\"form-control\"
            id=\"registerName\"
            placeholder=\"Enter your full name\" />
    </div>

    <div class=\"form-group\">
        <label for=\"registerEmail\">Email</label>
        <input
            name=\"email\"
            type=\"email\"
            class=\"form-control\"
            id=\"registerEmail\"
            placeholder=\"Enter your email\" />
    </div>

    <div class=\"form-group\">
        <label for=\"registerPassword\">Password</label>
        <input
            name=\"password\"
            type=\"password\"
            class=\"form-control\"
            id=\"registerPassword\"
            placeholder=\"Choose a password\" />
    </div>

    <button type=\"submit\" class=\"btn btn-default\">Register</button>
</form>
";
    }

    public function getTemplateName()
    {
        return "E:\\www\\bg360/plugins/rainlab/user/components/account/register.htm";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }

    public function getSource()
    {
        return "<form
    data-request=\"onRegister\">
    <div class=\"form-group\">
        <label for=\"registerName\">Full Name</label>
        <input
            name=\"name\"
            type=\"text\"
            class=\"form-control\"
            id=\"registerName\"
            placeholder=\"Enter your full name\" />
    </div>

    <div class=\"form-group\">
        <label for=\"registerEmail\">Email</label>
        <input
            name=\"email\"
            type=\"email\"
            class=\"form-control\"
            id=\"registerEmail\"
            placeholder=\"Enter your email\" />
    </div>

    <div class=\"form-group\">
        <label for=\"registerPassword\">Password</label>
        <input
            name=\"password\"
            type=\"password\"
            class=\"form-control\"
            id=\"registerPassword\"
            placeholder=\"Choose a password\" />
    </div>

    <button type=\"submit\" class=\"btn btn-default\">Register</button>
</form>
";
    }
}
