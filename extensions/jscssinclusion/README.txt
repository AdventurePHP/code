Installation and configuration notes for jscssinclusion:

Install:
You need to add a configuration file called "DEFAULT_actionconfig.ini"
in /config/extensions/jscssinclusion/biz/actions/{YOUR CONTEXT} with the
content:
---------------------------------------
[sGCJ]
FC.ActionNamespace = "extensions::jscssinclusion::biz::actions"
FC.ActionFile = "JsCssInclusionAction"
FC.ActionClass = "JsCssInclusionAction"
FC.InputFile = "JsCssInclusionInput"
FC.InputClass = "JsCssInclusionInput"
FC.InputParams = ""
----------------------------------------

Know you are ready to use it. Just generate a frontcontroller link for the file
you want to include.
Example:
Your js file is located in /APF/sites/example/pres/frontend/static/js/myjs.js
Your css file is located in /APF/sites/example/pres/frontend/static/css/mycss.css
Then your link for the js file is:
.../index.php?extensions_jscssinclusion_biz-action:sGCJ=path:sites_example_pres_frontend_static_js|type:js|file:myjs
.../index.php?extensions_jscssinclusion_biz-action:sGCJ=path:sites_example_pres_frontend_static_css|type:css|file:mycss

parameters:
path: The relative path from /APF/. You need to replace all / and :: with _
type: The type of your file. possible: js or css
file: The name of your file, without extension (.css, .js).
