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

/* modal.html */
class __TwigTemplate_72e87ceb68de44fca102f4b789c0a28f extends Template
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
        echo "<div class=\"modalWindow\" id=\"modalWindow\">
    <h1 id=\"headerModal\"></h1>
    <p id=\"textModal\"></p>
    <button id=\"buttonModal\">ОК</button>
</div>
<style>
    .modalWindow{
        position: absolute;
        top: calc(50% - 70px);
        left: calc( 50% - 240px);
        min-height: 60px;
        height: auto;
        width: 400px;
        background-color: #e1f5fe;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        transition:1s;
        opacity: 0;
        border-radius: var(--border-radius);
        box-shadow: 6px 3px 20px 4px #4fc3f7;
        padding: 40px;

    }
    .modalWindow *{
        padding: 5px 10px;
    }
    #buttonModal{
        align-self: flex-end;
    }
</style>";
    }

    public function getTemplateName()
    {
        return "modal.html";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "modal.html", "C:\\OSPanel\\domains\\localhost\\testProject\\templates\\modal.html");
    }
}
