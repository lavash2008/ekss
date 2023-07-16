<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* index.html */
class __TwigTemplate_aa9f98ca468866583178e4e6224879be extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <link rel=\"stylesheet\"  type=\"text/css\" href=\"styles/index.css\">
    <link rel=\"stylesheet\"  type=\"text/css\" href=\"styles/";
        // line 7
        echo twig_escape_filter($this->env, ($context["style"] ?? null), "html", null, true);
        echo ".css\">
    <title>Авторизация</title>
</head>
<body>
    ";
        // line 11
        echo twig_include($this->env, $context, "modal.html");
        echo "
    <div class=\"authorizationWrap\">
        <div class=\"logo\">
            <img src=\"logo/logo.webp\" alt=\"Логотип\">
        </div>
        <form id=\"formAuth\">
            <div class=\"input_field\">
                <label for=\"email\">Почта</label><br>
                <input autofocus name=\"email\" id=\"email\" type=\"email\">
                <span class=\"info_email\"></span>
            </div>
            <div class=\"input_field\">
                <label for=\"pass\">Пароль</label><br>
                <input name=\"pass\" id=\"pass\" type=\"password\"> 
                <span class=\"info_pass\" ></span>
            </div>
            <div class=\"visibl_field\" >
                <input id=\"visibly\" type=\"checkbox\" name=\"visiblPass\" onclick=\"
                    if(this.checked){
                        document.getElementById('pass').type='text';
                    }
                    else{
                        document.getElementById('pass').type='password';
                    }
                \">
                <label for=\"visibly\">Показать пароль</label>
            </div>
            <div class=\"input_field\">
                <input id=\"submit_button\" type=\"submit\" value=\"Вход\">
            </div>
        </form>
    </div>
    <script src=\"script\\jq.js\"></script>
    <script src=\"script\\modal.js\"></script>
    <script src=\"script\\authorization.js\"></script>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 11,  45 => 7,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "index.html", "C:\\OSPanel\\domains\\localhost\\testProject\\templates\\index.html");
    }
}
