<?php

/* 404.html */
class __TwigTemplate_6838988a9c691160267ac7fe8d726427698396815687b502bce6d67c0101c173 extends Twig_Template
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
        echo "<!DOCTYPE html>
<html>
<head>
<meta charset=\"UTF-8\">
<title>Insert title here</title>
</head>
<body>
<h1>404</h1>您知道的....
<div></div>
<span style=\"color:red;font-size: 16px;\">";
        // line 10
        echo twig_escape_filter($this->env, (isset($context["controller"]) ? $context["controller"] : null), "html", null, true);
        echo "</span> Controller Not Exist <span style=\"color:red;font-size: 16px;\">";
        echo twig_escape_filter($this->env, (isset($context["action"]) ? $context["action"] : null), "html", null, true);
        echo "</span> Action!
<div></div>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "404.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  30 => 10,  19 => 1,);
    }
}
/* <!DOCTYPE html>*/
/* <html>*/
/* <head>*/
/* <meta charset="UTF-8">*/
/* <title>Insert title here</title>*/
/* </head>*/
/* <body>*/
/* <h1>404</h1>您知道的....*/
/* <div></div>*/
/* <span style="color:red;font-size: 16px;">{{controller}}</span> Controller Not Exist <span style="color:red;font-size: 16px;">{{action}}</span> Action!*/
/* <div></div>*/
/* </body>*/
/* </html>*/
